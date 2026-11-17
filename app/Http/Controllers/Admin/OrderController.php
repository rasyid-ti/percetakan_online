<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request, string $mode = 'orders'): View
    {
        $isPayments = $mode === 'payments';
        $param = $isPayments ? 'payment' : 'status';
        $value = (string) $request->query($param, $isPayments ? PaymentStatus::WaitingVerification->value : '');
        $q = trim((string) $request->query('q'));

        $orders = Order::with('user', 'items', 'operator')
            ->when(! $isPayments && OrderStatus::tryFrom($value), fn ($query) => $query->where('status', $value))
            ->when($isPayments && PaymentStatus::tryFrom($value), fn ($query) => $query->where('payment_status', $value))
            ->when($q, fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('code', 'like', "%{$q}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"));
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $options = $isPayments
            ? array_merge([['all', 'Semua']], array_map(fn ($s) => [$s->value, $s->label()], PaymentStatus::cases()))
            : array_merge([['', 'Semua']], array_map(fn ($s) => [$s->value, $s->label()], OrderStatus::cases()));

        return view('admin.orders.index', compact('orders', 'mode', 'param', 'value', 'q', 'options'));
    }

    public function show(Order $order): View
    {
        $order->load('user', 'operator', 'items', 'statusLogs.user', 'payments.verifier');

        $operators = User::where('role', Role::Operator->value)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.orders.show', [
            'order' => $order,
            'payments' => $order->payments->sortByDesc('id'),
            'operators' => $operators,
            'isOpen' => ! in_array($order->status, [OrderStatus::Completed, OrderStatus::Cancelled], true),
        ]);
    }

    public function approve(Request $request, Order $order, Payment $payment): RedirectResponse
    {
        if (! $this->canVerify($order, $payment)) {
            return back()->with('error', 'Pembayaran ini tidak dapat diverifikasi.');
        }

        DB::transaction(function () use ($request, $order, $payment) {
            $payment->update([
                'status' => PaymentStatus::Paid,
                'verified_by' => $request->user()->id,
                'paid_at' => now(),
            ]);

            $order->update([
                'payment_status' => PaymentStatus::Paid,
                'status' => OrderStatus::Processing,
            ]);

            $this->log($order, $request, OrderStatus::Processing, 'Pembayaran diverifikasi. Pesanan masuk antrean produksi.');
        });

        return back()->with('success', 'Pembayaran disetujui. Pesanan masuk antrean produksi.');
    }

    public function reject(Request $request, Order $order, Payment $payment): RedirectResponse
    {
        if (! $this->canVerify($order, $payment)) {
            return back()->with('error', 'Pembayaran ini tidak dapat diverifikasi.');
        }

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:300'],
        ], [
            'reason.required' => 'Alasan penolakan wajib diisi.',
            'reason.max' => 'Alasan maksimal 300 karakter.',
        ]);

        DB::transaction(function () use ($request, $order, $payment, $data) {
            $payment->update([
                'status' => PaymentStatus::Rejected,
                'verified_by' => $request->user()->id,
                'note' => $data['reason'],
            ]);

            $order->update(['payment_status' => PaymentStatus::Rejected]);

            $this->log($order, $request, $order->status, 'Pembayaran ditolak: ' . $data['reason']);
        });

        return back()->with('success', 'Pembayaran ditolak. Pelanggan diminta mengunggah ulang bukti.');
    }

    public function assign(Request $request, Order $order): RedirectResponse
    {
        if (in_array($order->status, [OrderStatus::Completed, OrderStatus::Cancelled], true)) {
            return back()->with('error', 'Pesanan ini sudah ditutup.');
        }

        $data = $request->validate([
            'operator_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', Role::Operator->value)->where('is_active', true),
            ],
        ], [
            'operator_id.exists' => 'Operator tidak valid.',
        ]);

        $operator = $data['operator_id'] ? User::find($data['operator_id']) : null;
        $order->update(['operator_id' => $operator?->id]);

        $this->log(
            $order,
            $request,
            $order->status,
            $operator ? "Pesanan ditugaskan kepada {$operator->name}." : 'Penugasan operator dihapus.'
        );

        return back()->with('success', $operator ? "Pesanan ditugaskan kepada {$operator->name}." : 'Penugasan operator dihapus.');
    }

    public function complete(Request $request, Order $order): RedirectResponse
    {
        if ($order->status !== OrderStatus::Ready) {
            return back()->with('error', 'Hanya pesanan berstatus "Siap Diambil / Dikirim" yang bisa diselesaikan.');
        }

        $order->update(['status' => OrderStatus::Completed, 'completed_at' => now()]);
        $this->log($order, $request, OrderStatus::Completed, 'Pesanan selesai.');

        return back()->with('success', 'Pesanan ditandai selesai.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if (in_array($order->status, [OrderStatus::Completed, OrderStatus::Cancelled], true)) {
            return back()->with('error', 'Pesanan ini sudah ditutup.');
        }

        $order->update(['status' => OrderStatus::Cancelled]);
        $this->log($order, $request, OrderStatus::Cancelled, 'Pesanan dibatalkan oleh admin.');

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function design(Order $order, OrderItem $item): StreamedResponse
    {
        abort_unless($item->order_id === $order->id, 404);
        abort_unless($item->design_file && Storage::disk('local')->exists($item->design_file), 404);

        return Storage::disk('local')->response($item->design_file);
    }

    public function proof(Payment $payment): StreamedResponse
    {
        abort_unless($payment->proof && Storage::disk('local')->exists($payment->proof), 404);

        return Storage::disk('local')->response($payment->proof);
    }

    private function canVerify(Order $order, Payment $payment): bool
    {
        abort_unless($payment->order_id === $order->id, 404);

        return $payment->status === PaymentStatus::WaitingVerification
            && $order->status === OrderStatus::AwaitingPayment;
    }

    private function log(Order $order, Request $request, OrderStatus $status, string $note): void
    {
        OrderStatusLog::create([
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
            'status' => $status->value,
            'note' => $note,
        ]);
    }
}