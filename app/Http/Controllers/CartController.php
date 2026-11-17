<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $lines = Cart::lines();

        return view('cart.index', [
            'lines' => $lines,
            'total' => $lines->sum('subtotal'),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:' . $product->min_qty, 'max:100000'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'qty.required' => 'Jumlah wajib diisi.',
            'qty.integer' => 'Jumlah harus berupa angka.',
            'qty.min' => "Jumlah minimal {$product->min_qty} {$product->unit}.",
            'qty.max' => 'Jumlah terlalu besar.',
            'note.max' => 'Catatan maksimal 500 karakter.',
        ]);

        $chosen = [];

        foreach ($product->groupedOptions() as $group => $options) {
            $option = $options->firstWhere('id', (int) $request->input("options.{$group}"));

            if (! $option) {
                throw ValidationException::withMessages([
                    'options' => "Pilihan {$group} wajib dipilih.",
                ]);
            }

            $chosen[$group] = $option->id;
        }

        Cart::add($product, $chosen, (int) $data['qty'], $data['note'] ?? null);

        return redirect()->route('cart.index')
            ->with('success', "{$product->name} masuk ke keranjang.");
    }

    public function destroy(string $key): RedirectResponse
    {
        Cart::remove($key);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }
}