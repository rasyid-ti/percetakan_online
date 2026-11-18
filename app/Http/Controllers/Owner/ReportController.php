<?php

namespace App\Http\Controllers\Owner;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to] = $this->range($request);

        return view('owner.report', [
            ...$this->data($from, $to),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->range($request);
        $data = $this->data($from, $to);
        $filename = "laporan-penjualan-{$from->toDateString()}_{$to->toDateString()}.csv";

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // agar Excel membaca UTF-8

            fputcsv($out, ['Kode Pesanan', 'Tanggal Bayar', 'Pelanggan', 'Metode', 'Total (Rp)'], ';');

            foreach ($data['payments'] as $payment) {
                fputcsv($out, [
                    $payment->order->code,
                    $payment->paid_at->format('Y-m-d H:i'),
                    $payment->order->user->name,
                    $payment->method,
                    $payment->amount,
                ], ';');
            }

            fputcsv($out, [], ';');
            fputcsv($out, ['Total omzet', '', '', '', $data['revenue']], ';');
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function range(Request $request): array
    {
        $parse = function (?string $value, Carbon $fallback): Carbon {
            try {
                return $value ? Carbon::parse($value) : $fallback;
            } catch (\Throwable) {
                return $fallback;
            }
        };

        $from = $parse($request->query('dari'), now()->startOfMonth())->startOfDay();
        $to = $parse($request->query('sampai'), now())->endOfDay();

        if ($to->lessThan($from)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }

    private function data(Carbon $from, Carbon $to): array
    {
        $payments = Payment::with('order.user')
            ->where('status', PaymentStatus::Paid->value)
            ->whereBetween('paid_at', [$from, $to])
            ->whereHas('order', fn ($q) => $q->where('status', '!=', OrderStatus::Cancelled->value))
            ->orderBy('paid_at')
            ->get();

        $orderIds = $payments->pluck('order_id')->unique();

        $products = OrderItem::whereIn('order_id', $orderIds)
            ->get()
            ->groupBy('product_name')
            ->map(fn ($rows, $name) => [
                'name' => $name,
                'qty' => $rows->sum('qty'),
                'orders' => $rows->pluck('order_id')->unique()->count(),
                'revenue' => $rows->sum('subtotal'),
            ])
            ->sortByDesc('revenue')
            ->values();

        $revenue = (int) $payments->sum('amount');

        return [
            'payments' => $payments,
            'products' => $products,
            'revenue' => $revenue,
            'orderCount' => $orderIds->count(),
            'average' => $orderIds->count() > 0 ? (int) round($revenue / $orderIds->count()) : 0,
            'shipping' => (int) Order::whereIn('id', $orderIds)->sum('shipping_cost'),
        ];
    }
}