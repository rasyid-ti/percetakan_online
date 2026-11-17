<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $production = [
            OrderStatus::Processing->value,
            OrderStatus::Printing->value,
            OrderStatus::Finishing->value,
        ];

        return view('admin.dashboard', [
            'stats' => [
                [
                    'label' => 'Menunggu Pembayaran',
                    'value' => Order::where('status', OrderStatus::AwaitingPayment->value)
                        ->where('payment_status', PaymentStatus::Unpaid->value)->count(),
                    'hint' => 'Belum ada bukti bayar',
                    'border' => 'border-brand-500',
                ],
                [
                    'label' => 'Menunggu Verifikasi',
                    'value' => Order::where('payment_status', PaymentStatus::WaitingVerification->value)->count(),
                    'hint' => 'Bukti bayar masuk',
                    'border' => 'border-magenta-500',
                ],
                [
                    'label' => 'Dalam Produksi',
                    'value' => Order::whereIn('status', $production)->count(),
                    'hint' => 'Dikerjakan operator',
                    'border' => 'border-sun-400',
                ],
                [
                    'label' => 'Siap Diambil',
                    'value' => Order::where('status', OrderStatus::Ready->value)->count(),
                    'hint' => 'Menunggu pelanggan',
                    'border' => 'border-ink-900',
                ],
            ],
            'recent' => Order::with('user', 'items')->latest()->take(6)->get(),
            'waiting' => Payment::with('order.user')
                ->where('status', PaymentStatus::WaitingVerification->value)
                ->latest()->take(4)->get(),
        ]);
    }
}