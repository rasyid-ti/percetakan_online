<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedCatalog();
    }

    private function seedUsers(): void
    {
        $users = [
            ['Budi Santoso',   'owner@tintakita.test',    Role::Owner],
            ['Siti Admin',     'admin@tintakita.test',    Role::Admin],
            ['Rudi Operator',  'operator@tintakita.test', Role::Operator],
            ['Andi Pelanggan', 'customer@tintakita.test', Role::Customer],
        ];

        foreach ($users as [$name, $email, $role]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'phone' => '0812' . random_int(10000000, 99999999),
                'role' => $role,
                'password' => 'password',
                'email_verified_at' => now(),
            ]);
        }
    }

    private function seedCatalog(): void
    {
        $catalog = [
            ['Kartu Nama', 'card', 'Kartu nama profesional untuk bisnis Anda.', [
                ['Kartu Nama Standar', 500, 'pcs', 100, 2, true, [
                    ['Ukuran', '9 x 5.5 cm', 0],
                    ['Bahan', 'Art Carton 260gr', 0],
                    ['Bahan', 'Ivory 310gr', 200],
                    ['Finishing', 'Tanpa Laminasi', 0],
                    ['Finishing', 'Laminasi Doff', 300],
                    ['Finishing', 'Laminasi Glossy', 300],
                ]],
            ]],
            ['Spanduk & Banner', 'banner', 'Cetak outdoor tahan cuaca.', [
                ['X-Banner', 45000, 'pcs', 1, 2, true, [
                    ['Ukuran', '60 x 160 cm', 0],
                    ['Ukuran', '80 x 180 cm', 15000],
                    ['Bahan', 'Flexi 280gr', 0],
                    ['Bahan', 'Albatros', 10000],
                ]],
                ['Spanduk Flexi', 25000, 'meter', 1, 1, false, [
                    ['Bahan', 'Flexi 280gr', 0],
                    ['Bahan', 'Flexi 340gr', 5000],
                    ['Finishing', 'Mata Ayam', 0],
                ]],
            ]],
            ['Stiker', 'sticker', 'Stiker label, vinyl, dan cutting.', [
                ['Stiker Vinyl Cutting', 3500, 'lembar', 10, 2, true, [
                    ['Ukuran', 'A4', 0],
                    ['Ukuran', 'A3', 3500],
                    ['Bahan', 'Vinyl Glossy', 0],
                    ['Bahan', 'Vinyl Doff', 500],
                    ['Finishing', 'Cutting Kiss Cut', 1000],
                ]],
            ]],
            ['Brosur & Flyer', 'flyer', 'Media promosi cetak berkualitas.', [
                ['Flyer A5', 600, 'lembar', 100, 2, false, [
                    ['Ukuran', 'A5', 0],
                    ['Bahan', 'HVS 100gr', 0],
                    ['Bahan', 'Art Paper 150gr', 150],
                    ['Finishing', 'Satu Sisi', 0],
                    ['Finishing', 'Bolak-balik', 250],
                ]],
            ]],
            ['Undangan', 'invitation', 'Undangan pernikahan dan acara.', [
                ['Undangan Softcover', 2500, 'pcs', 50, 4, true, [
                    ['Ukuran', '14 x 21 cm', 0],
                    ['Bahan', 'Art Carton 260gr', 0],
                    ['Bahan', 'Linen Emboss', 1200],
                    ['Finishing', 'Tanpa Laminasi', 0],
                    ['Finishing', 'Spot UV', 900],
                ]],
            ]],
        ];

        foreach ($catalog as $i => [$catName, $icon, $catDesc, $products]) {
            $category = Category::create([
                'name' => $catName,
                'slug' => Str::slug($catName),
                'icon' => $icon,
                'description' => $catDesc,
                'sort_order' => $i,
            ]);

            foreach ($products as [$name, $price, $unit, $minQty, $days, $featured, $options]) {
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => "{$name} cetak berkualitas tinggi dengan hasil tajam dan warna akurat.",
                    'base_price' => $price,
                    'unit' => $unit,
                    'min_qty' => $minQty,
                    'estimated_days' => $days,
                    'is_featured' => $featured,
                ]);

                foreach ($options as $n => [$group, $optName, $extra]) {
                    $product->options()->create([
                        'group' => $group,
                        'name' => $optName,
                        'extra_price' => $extra,
                        'sort_order' => $n,
                    ]);
                }
            }
        }
    }
}