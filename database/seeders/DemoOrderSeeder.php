<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\Role;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoOrderSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('role', Role::Customer->value)->first();
        $operator = User::where('role', Role::Operator->value)->first();
        $admin = User::where('role', Role::Admin->value)->first();
        $products = Product::with('options')->where('is_active', true)->get();

        if (! $customer || ! $admin || $products->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 30; $i++) {
            $paidAt = now()->subDays(random_int(0, 27))->setTime(random_int(8, 20), random_int(0, 59));
            $doneAt = $paidAt->copy()->addDays(2);
            $completed = $doneAt->lte(now());

            $lines = [];
            foreach ($products->random(random_int(1, 2)) as $product) {
                $chosen = $product->options->groupBy('group')->map(fn ($group) => $group->random());
                $unit = $product->base_price + $chosen->sum('extra_price');
                $qty = $product->min_qty * random_int(1, 5);

                $lines[] = [
                    'product' => $product,
                    'options' => $chosen->map(fn ($o) => $o->name)->all(),
                    'qty' => $qty,
                    'unit' => $unit,
                    'subtotal' => $unit * $qty,
                ];
            }

            $subtotal = collect($lines)->sum('subtotal');

            $order = new Order([
                'code' => 'DMO-' . Str::upper(Str::random(8)),
                'user_id' => $customer->id,
                'operator_id' => $operator?->id,
                'status' => $completed ? OrderStatus::Completed : OrderStatus::Printing,
                'payment_status' => PaymentStatus::Paid,
                'delivery_method' => 'pickup',
                'subtotal' => $subtotal,
                'shipping_cost' => 0,
                'total' => $subtotal,
                'notes' => '[DEMO]',
                'deadline' => $doneAt->toDateString(),
                'completed_at' => $completed ? $doneAt : null,
            ]);
            $order->created_at = $paidAt->copy()->subHours(2);
            $order->updated_at = $completed ? $doneAt : $paidAt;
            $order->save();

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'options' => $line['options'],
                    'qty' => $line['qty'],
                    'unit_price' => $line['unit'],
                    'subtotal' => $line['subtotal'],
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'method' => random_int(0, 1) ? 'transfer' : 'qris',
                'amount' => $subtotal,
                'status' => PaymentStatus::Paid,
                'verified_by' => $admin->id,
                'paid_at' => $paidAt,
            ]);
        }
    }
}