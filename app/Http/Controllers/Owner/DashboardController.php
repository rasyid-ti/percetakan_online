<?php

namespace App\Http\Controllers\Owner;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $period = in_array($request->query('periode'), ['7', '30', 'tahun'], true)
            ? $request->query('periode')
            : '7';

        $start = match ($period) {
            '30' => now()->subDays(29)->startOfDay(),
            'tahun' => now()->startOfYear(),
            default => now()->subDays(6)->startOfDay(),
        };
        $end = $period === 'tahun' ? now()->endOfYear() : now()->endOfDay();

        // Pembayaran lunas dari pesanan yang tidak dibatalkan
        $payments = Payment::query()
            ->join('orders', 'orders.id', '=', 'payments.order_id')
            ->where('payments.status', PaymentStatus::Paid->value)
            ->where('orders.status', '!=', OrderStatus::Cancelled->value)
            ->whereBetween('payments.paid_at', [$start, $end])
            ->get(['payments.amount', 'payments.paid_at', 'payments.order_id']);

        $revenue = (int) $payments->sum('amount');
        $paidCount = $payments->pluck('order_id')->unique()->count();

        $ordersInPeriod = Order::whereBetween('created_at', [$start, $end]);
        $totalOrders = (clone $ordersInPeriod)->count();
        $completedOrders = (clone $ordersInPeriod)->where('status', OrderStatus::Completed->value)->count();
        $activeCustomers = (clone $ordersInPeriod)->distinct()->count('user_id');

        // Deret grafik
        $series = [];
        if ($period === 'tahun') {
            for ($m = 1; $m <= 12; $m++) {
                $series[] = [
                    'label' => Carbon::create(now()->year, $m, 1)->translatedFormat('M'),
                    'value' => (int) $payments->filter(fn ($p) => $p->paid_at->month === $m)->sum('amount'),
                ];
            }
        } else {
            $days = $period === '30' ? 30 : 7;
            for ($i = $days - 1; $i >= 0; $i--) {
                $day = now()->subDays($i);
                $series[] = [
                    'label' => $days === 7 ? $day->translatedFormat('D') : $day->format('d'),
                    'value' => (int) $payments->filter(fn ($p) => $p->paid_at->isSameDay($day))->sum('amount'),
                ];
            }
        }

        $topProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', PaymentStatus::Paid->value)
            ->where('orders.status', '!=', OrderStatus::Cancelled->value)
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('order_items.product_name')
            ->selectRaw('order_items.product_name as name, COUNT(DISTINCT order_items.order_id) as orders_count, SUM(order_items.subtotal) as revenue')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $activeStatuses = array_map(fn ($s) => $s->value, [
            OrderStatus::Processing, OrderStatus::Printing, OrderStatus::Finishing, OrderStatus::Ready,
        ]);

        $operators = User::where('role', Role::Operator->value)
            ->where('is_active', true)
            ->withCount([
                'assignedOrders as active_count' => fn ($q) => $q->whereIn('status', $activeStatuses),
                'assignedOrders as done_count' => fn ($q) => $q
                    ->where('status', OrderStatus::Completed->value)
                    ->whereBetween('completed_at', [$start, $end]),
            ])
            ->orderBy('name')
            ->get();

        $periodEnd = $end->greaterThan(now()) ? now() : $end;

        return view('owner.dashboard', [
            'period' => $period,
            'periodLabel' => $start->translatedFormat('d M Y') . ' – ' . $periodEnd->translatedFormat('d M Y'),
            'revenue' => $revenue,
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'average' => $paidCount > 0 ? (int) round($revenue / $paidCount) : 0,
            'activeCustomers' => $activeCustomers,
            'chart' => $this->buildChart($series),
            'topProducts' => $topProducts,
            'topMax' => max(1, (int) $topProducts->max('revenue')),
            'operators' => $operators,
            'waitingVerification' => Order::where('payment_status', PaymentStatus::WaitingVerification->value)->count(),
        ]);
    }

    /** Menghitung koordinat SVG (area gambar x 56–632, dasar y 220, tinggi maks 170). */
    private function buildChart(array $series): array
    {
        $n = count($series);
        $peak = (int) collect($series)->max('value');
        $top = max($peak, 1);
        $slot = 576 / $n;
        $barW = min(48, $slot * 0.62);
        $peakIndex = $peak > 0 ? array_search($peak, array_column($series, 'value'), true) : null;
        $labelEvery = $n <= 12 ? 1 : (int) ceil($n / 7);

        $bars = [];
        foreach ($series as $i => $item) {
            $h = $item['value'] / $top * 170;
            $bars[] = [
                'x' => round(56 + $i * $slot + ($slot - $barW) / 2, 1),
                'cx' => round(56 + $i * $slot + $slot / 2, 1),
                'y' => round(220 - $h, 1),
                'h' => round($h, 1),
                'w' => round($barW, 1),
                'color' => $i === $peakIndex ? '#EC0E80' : '#1A8CFF',
                'label' => $i % $labelEvery === 0 ? $item['label'] : '',
                'value' => ($n <= 12 && $item['value'] > 0) ? $this->short($item['value']) : '',
            ];
        }

        $grid = [];
        for ($i = 0; $i <= 4; $i++) {
            $grid[] = ['y' => 220 - 170 * $i / 4, 'label' => $this->short($top * $i / 4)];
        }

        return ['bars' => $bars, 'grid' => $grid];
    }

    private function short(float $value): string
    {
        if ($value >= 1_000_000) {
            return rtrim(rtrim(number_format($value / 1_000_000, 1, ',', '.'), '0'), ',') . ' jt';
        }

        if ($value >= 1_000) {
            return round($value / 1_000) . ' rb';
        }

        return (string) round($value);
    }
}