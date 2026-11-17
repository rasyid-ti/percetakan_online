<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()->with('items')->latest()->get();

        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        $this->own($order);

        $order->load('items', 'statusLogs.user', 'latestPayment');

        return view('orders.show', [
            'order' => $order,
            'canPay' => $this->canPay($order),
        ]);
    }

    public function pay(Request $request, Order $order): RedirectResponse
    {
        $this->own($order);

        if (! $this->canPay($order)) {
            return back()->with('error', 'Pesanan ini tidak dapat dibayar saat ini.');
        }

        $data = $request->validate([
            'method' => ['required', 'in:transfer,qris'],
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'note' => ['nullable', 'string', 'max:300'],
        ], [
            'method.required' => 'Pilih metode pembayaran.',
            'method.in' => 'Metode pembayaran tidak valid.',
            'proof.required' => 'Bukti pembayaran wajib diunggah.',
            'proof.file' => 'Bukti pembayaran tidak valid.',
            'proof.uploaded' => 'Bukti pembayaran gagal diunggah (terlalu besar).',
            'proof.mimes' => 'Bukti harus berformat JPG, PNG, WEBP, atau PDF.',
            'proof.max' => 'Ukuran bukti maksimal 5 MB.',
            'note.max' => 'Catatan maksimal 300 karakter.',
        ]);

        $path = $request->file('proof')->store("proofs/{$order->id}", 'local');

        Payment::create([
            'order_id' => $order->id,
            'method' => $data['method'],
            'amount' => $order->total,
            'proof' => $path,
            'status' => PaymentStatus::WaitingVerification,
            'note' => $data['note'] ?? null,
        ]);

        $order->update(['payment_status' => PaymentStatus::WaitingVerification]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
            'status' => $order->status->value,
            'note' => 'Bukti pembayaran diunggah. Menunggu verifikasi admin.',
        ]);

        return redirect()->route('orders.show', $order->code)
            ->with('success', 'Bukti pembayaran terkirim. Admin akan segera memverifikasi.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->own($order);

        if (! $this->canPay($order)) {
            return back()->with('error', 'Pesanan ini tidak dapat dibatalkan.');
        }

        $order->update(['status' => OrderStatus::Cancelled]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
            'status' => OrderStatus::Cancelled->value,
            'note' => 'Pesanan dibatalkan oleh pelanggan.',
        ]);

        return redirect()->route('orders.show', $order->code)
            ->with('success', 'Pesanan dibatalkan.');
    }

    public function proof(Order $order): StreamedResponse
    {
        $this->own($order);

        $payment = $order->latestPayment;
        abort_unless($payment && $payment->proof && Storage::disk('local')->exists($payment->proof), 404);

        return Storage::disk('local')->response($payment->proof);
    }

    public function design(Order $order, OrderItem $item): StreamedResponse
    {
        $this->own($order);

        abort_unless($item->order_id === $order->id, 404);
        abort_unless($item->design_file && Storage::disk('local')->exists($item->design_file), 404);

        return Storage::disk('local')->response($item->design_file);
    }

    private function own(Order $order): void
    {
        abort_unless($order->user_id === auth()->id(), 404);
    }

    /** Boleh bayar atau batal selama belum lunas dan belum dibatalkan. */
    private function canPay(Order $order): bool
    {
        return $order->status === OrderStatus::AwaitingPayment
            && in_array($order->payment_status, [PaymentStatus::Unpaid, PaymentStatus::Rejected], true);
    }
}