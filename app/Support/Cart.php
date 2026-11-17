<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Cart
{
    private const KEY = 'cart';

    public static function add(Product $product, array $optionIds, int $qty, ?string $note): void
    {
        $items = session(self::KEY, []);
        $items[(string) Str::uuid()] = [
            'product_id' => $product->id,
            'options' => $optionIds,
            'qty' => $qty,
            'note' => $note,
        ];
        session([self::KEY => $items]);
    }

    public static function remove(string $key): void
    {
        $items = session(self::KEY, []);
        unset($items[$key]);
        session([self::KEY => $items]);
    }

    public static function clear(): void
    {
        session()->forget(self::KEY);
    }

    public static function count(): int
    {
        return count(session(self::KEY, []));
    }

    /** Harga selalu dihitung ulang dari database, bukan dari data browser. */
    public static function lines(): Collection
    {
        $items = session(self::KEY, []);

        if (! $items) {
            return collect();
        }

        $products = Product::active()
            ->with('options')
            ->whereIn('id', collect($items)->pluck('product_id'))
            ->get()
            ->keyBy('id');

        return collect($items)
            ->map(function (array $item, string $key) use ($products) {
                $product = $products->get($item['product_id']);

                if (! $product) {
                    return null;
                }

                $unit = $product->base_price;
                $labels = [];

                foreach ($item['options'] as $group => $optionId) {
                    $option = $product->options->firstWhere('id', $optionId);

                    if ($option) {
                        $unit += $option->extra_price;
                        $labels[$group] = $option->name;
                    }
                }

                return [
                    'key' => $key,
                    'product' => $product,
                    'options' => $labels,
                    'qty' => $item['qty'],
                    'note' => $item['note'],
                    'unit_price' => $unit,
                    'subtotal' => $unit * $item['qty'],
                ];
            })
            ->filter()
            ->values();
    }
}