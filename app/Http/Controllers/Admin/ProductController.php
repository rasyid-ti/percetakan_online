<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q'));

        $products = Product::with('category')
            ->withCount('options')
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('category_id')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'q'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product(),
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $product = DB::transaction(function () use ($request, $data) {
            $product = Product::create([
                ...$this->fields($data),
                'slug' => $this->uniqueSlug($data['name']),
                'image' => $request->hasFile('image')
                    ? $request->file('image')->store('products', 'public')
                    : null,
            ]);

            $this->syncOptions($product, $data['options'] ?? []);

            return $product;
        });

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product->load('options'),
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $product, $data) {
            $image = $product->image;

            if ($request->boolean('remove_image') && $image) {
                Storage::disk('public')->delete($image);
                $image = null;
            }

            if ($request->hasFile('image')) {
                if ($image) {
                    Storage::disk('public')->delete($image);
                }
                $image = $request->file('image')->store('products', 'public');
            }

            $product->update([...$this->fields($data), 'image' => $image]);
            $this->syncOptions($product, $data['options'] ?? []);
        });

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk dihapus. Riwayat pesanan lama tetap tersimpan.');
    }

    public function toggle(Product $product): RedirectResponse
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('success', $product->is_active ? 'Produk diaktifkan.' : 'Produk dinonaktifkan.');
    }

    private function validated(Request $request): array
    {
        // Baris opsi yang benar-benar kosong diabaikan
        $options = collect($request->input('options', []))
            ->filter(fn ($o) => filled($o['group'] ?? null) || filled($o['name'] ?? null))
            ->values()
            ->all();
        $request->merge(['options' => $options]);

        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'base_price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'unit' => ['required', 'string', 'max:20'],
            'min_qty' => ['required', 'integer', 'min:1', 'max:100000'],
            'estimated_days' => ['required', 'integer', 'min:1', 'max:60'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'options' => ['nullable', 'array', 'max:40'],
            'options.*.id' => ['nullable', 'integer'],
            'options.*.group' => ['required', 'string', 'max:50'],
            'options.*.name' => ['required', 'string', 'max:150'],
            'options.*.extra_price' => ['nullable', 'integer', 'min:0', 'max:100000000'],
        ], $this->messages(), $this->attributes());

        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        return $data;
    }

    private function fields(array $data): array
    {
        return [
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'base_price' => $data['base_price'],
            'unit' => $data['unit'],
            'min_qty' => $data['min_qty'],
            'estimated_days' => $data['estimated_days'],
            'is_active' => $data['is_active'],
            'is_featured' => $data['is_featured'],
        ];
    }

    /** Opsi lama diperbarui (ID tetap), opsi baru dibuat, opsi yang dihapus di form ikut dihapus. */
    private function syncOptions(Product $product, array $rows): void
    {
        $keep = [];

        foreach (array_values($rows) as $i => $row) {
            $attrs = [
                'group' => trim($row['group']),
                'name' => trim($row['name']),
                'extra_price' => (int) ($row['extra_price'] ?? 0),
                'sort_order' => $i,
            ];

            $existing = ! empty($row['id'])
                ? $product->options()->whereKey($row['id'])->first()
                : null;

            if ($existing) {
                $existing->update($attrs);
                $keep[] = $existing->id;
            } else {
                $keep[] = $product->options()->create($attrs)->id;
            }
        }

        $product->options()->whereNotIn('id', $keep)->delete();
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'produk';
        $slug = $base;
        $i = 2;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    private function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka bulat.',
            'exists' => ':attribute tidak valid.',
            'min.numeric' => ':attribute minimal :min.',
            'max.numeric' => ':attribute maksimal :max.',
            'max.string' => ':attribute maksimal :max karakter.',
            'max.array' => ':attribute maksimal :max baris.',
            'max.file' => 'Ukuran :attribute maksimal 3 MB.',
            'image' => ':attribute harus berupa gambar.',
            'mimes' => ':attribute harus berformat JPG, PNG, atau WEBP.',
            'uploaded' => ':attribute gagal diunggah (terlalu besar).',
        ];
    }

    private function attributes(): array
    {
        return [
            'category_id' => 'Kategori',
            'name' => 'Nama produk',
            'description' => 'Deskripsi',
            'base_price' => 'Harga dasar',
            'unit' => 'Satuan',
            'min_qty' => 'Minimal pesan',
            'estimated_days' => 'Estimasi produksi',
            'image' => 'Gambar produk',
            'options' => 'Opsi',
            'options.*.group' => 'Kelompok opsi',
            'options.*.name' => 'Nama opsi',
            'options.*.extra_price' => 'Tambahan harga opsi',
        ];
    }
}