<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('home', [
            'categories' => Category::withCount('products')->orderBy('sort_order')->get(),
            'featured' => Product::active()->with('category')->where('is_featured', true)->take(4)->get(),
        ]);
    }

    public function catalog(Request $request): View
    {
        $slug = $request->string('kategori')->toString();
        $search = trim($request->string('q')->toString());

        $products = Product::active()
            ->with('category')
            ->when($slug, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $slug)))
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get();

        return view('catalog', [
            'products' => $products,
            'categories' => Category::orderBy('sort_order')->get(),
            'activeCategory' => $slug,
            'search' => $search,
        ]);
    }

    public function product(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load('category', 'options');

        return view('product.show', [
            'product' => $product,
            'groups' => $product->groupedOptions(),
        ]);
    }
}