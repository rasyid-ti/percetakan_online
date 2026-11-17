<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $lines = Cart::lines();

        if ($lines->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        return view('checkout.create', [
            'lines' => $lines,
            'subtotal' => $lines->sum('subtotal'),
            'shipping' => (int) config('tintakita.shipping_cost'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $lines = Cart::lines();

        if ($lines->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        $rules = [
            'delivery_method' => ['required', Rule::in(['pickup', 'delivery'])],
            'delivery_address' => ['required_if:delivery_method,delivery', 'nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        $messages = [
            'delivery_method.required' => 'Pilih cara pengambilan pesanan.',
            'delivery_method.in' => 'Cara pengambilan tidak valid.',
            'delivery_address.required_if' => 'Alamat pengiriman wajib diisi.',
            'delivery_address.max' => 'Alamat maksimal 500 karakter.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];

        foreach ($lines as $line) {
            $field = "designs.{$line['key']}";
            $name = $line['product']->name;

            $rules[$field] = ['required', 'file', 'mimes:pdf,png,jpg,jpeg,webp', 'max:20480'];
            $messages["{$field}.required"] = "File desain untuk {$name} wajib diunggah.";
            $messages["{$field}.file"] = "File desain untuk {$name} tidak valid.";
            $messages["{$field}.uploaded"] = "File desain untuk {$name} gagal diunggah (terlalu besar).";
            $messages["{$field}.mimes"] = "File desain {$name} harus berformat PDF, PNG, JPG, atau WEBP.";
            $messages["{$field}.max"] = "File desain {$name} maksimal 20 MB.";
        }

        $data = $request->validate($rules, $messages);

        $subtotal = $lines->sum('subtotal');
        $shipping = $data['delivery_method'] === 'delivery' ? (int) config('tintakita.shipping_cost') : 0;
        $days = (int) $lines->max(fn ($line) => $line['product']->estimated_days);

        $order = DB::transaction(function () use ($request, $data, $lines, $subtotal, $shipping, $days) {
            $order = Order::create([
                'code' => Order::generateCode(),
                'user_id' => $request->user()->id,
                'status' => OrderStatus::AwaitingPayment,
                'payment_status' => PaymentStatus::Unpaid,
                'delivery_method' => $data['delivery_method'],
                'delivery_address' => $data['delivery_method'] === 'delivery' ? $data['delivery_address'] : null,
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'total' => $subtotal + $shipping,
                'notes' => $data['notes'] ?? null,
                'deadline' => now()->addDays($days)->toDateString(),
            ]);

            foreach ($lines as $line) {
                $path = $request->file("designs.{$line['key']}")->store("designs/{$order->id}", 'local');

                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'options' => $line['options'],
                    'qty' => $line['qty'],
                    'unit_price' => $line['unit_price'],
                    'subtotal' => $line['subtotal'],
                    'design_file' => $path,
                    'design_notes' => $line['note'],
                ]);
            }

            OrderStatusLog::create([
                'order_id' => $order->id,
                'user_id' => $request->user()->id,
                'status' => OrderStatus::AwaitingPayment->value,
                'note' => 'Pesanan dibuat. Menunggu pembayaran.',
            ]);

            return $order;
        });

        Cart::clear();

        return redirect()->route('orders.show', $order->code)
            ->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.');
    }
}