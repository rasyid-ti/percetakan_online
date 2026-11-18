<?php

namespace App\Http\Controllers\Operator;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $active = [
            OrderStatus::Processing,
            OrderStatus::Printing,
            OrderStatus::Finishing,
            OrderStatus::Ready,
        ];

        $orders = Order::with('items', 'user')
            ->where('operator_id', $request->user()->id)
            ->where('payment_status', PaymentStatus::Paid->value)
            ->whereIn('status', array_map(fn ($s) => $s->value, $active))
            ->orderBy('deadline')
            ->get();

        $meta = [
            'processing' => ['Cek File & Antrean', 'bg-brand-500', 'Mulai Cetak'],
            'printing' => ['Sedang Dicetak', 'bg-magenta-500', 'Lanjut ke Finishing'],
            'finishing' => ['Finishing', 'bg-sun-400', 'Tandai Siap'],
            'ready' => ['Siap Diambil / Dikirim', 'bg-ink-900', 'Tandai Selesai'],
        ];

        $columns = [];
        foreach ($meta as $status => [$title, $dot, $action]) {
            $columns[] = [
                'title' => $title,
                'dot' => $dot,
                'action' => $action,
                'orders' => $orders->filter(fn ($o) => $o->status->value === $status)->values(),
            ];
        }

        $urgent = $orders->filter(fn ($o) => $o->status !== OrderStatus::Ready
            && $o->deadline
            && $o->deadline->lte(today()))->count();

        return view('operator.dashboard', [
            'columns' => $columns,
            'total' => $orders->count(),
            'urgent' => $urgent,
        ]);
    }

    public function history(Request $request): View
    {
        $orders = Order::with('items', 'user')
            ->where('operator_id', $request->user()->id)
            ->whereIn('status', [OrderStatus::Completed->value, OrderStatus::Cancelled->value])
            ->latest('updated_at')
            ->paginate(15);

        return view('operator.history', ['orders' => $orders]);
    }

    public function advance(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->operator_id === $request->user()->id, 404);

        if ($order->payment_status !== PaymentStatus::Paid) {
            return back()->with('error', 'Pesanan ini belum lunas.');
        }

        $step = match ($order->status) {
            OrderStatus::Processing => [OrderStatus::Printing, 'Pesanan mulai dicetak.'],
            OrderStatus::Printing => [OrderStatus::Finishing, 'Proses cetak selesai. Masuk tahap finishing.'],
            OrderStatus::Finishing => [OrderStatus::Ready, 'Pesanan siap diambil / dikirim.'],
            OrderStatus::Ready => [OrderStatus::Completed, 'Pesanan selesai.'],
            default => null,
        };

        if (! $step) {
            return back()->with('error', 'Status pesanan ini tidak dapat dimajukan.');
        }

        [$next, $note] = $step;

        $order->update([
            'status' => $next,
            'completed_at' => $next === OrderStatus::Completed ? now() : $order->completed_at,
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
            'status' => $next->value,
            'note' => $note,
        ]);

        return back()->with('success', "{$order->code}: {$next->label()}.");
    }

    public function design(Request $request, Order $order, OrderItem $item): StreamedResponse
    {
        abort_unless($order->operator_id === $request->user()->id, 404);
        abort_unless($item->order_id === $order->id, 404);
        abort_unless($item->design_file && Storage::disk('local')->exists($item->design_file), 404);

        return Storage::disk('local')->response($item->design_file);
    }
}