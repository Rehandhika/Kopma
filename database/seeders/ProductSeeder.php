<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::count() > 0) {
            return;
        }

        $items = [
            // Minuman
            ['name' => 'Air Mineral 600ml', 'price' => 4000, 'cost_price' => 2500, 'category' => 'Minuman', 'stock' => 50],
            ['name' => 'Teh Botol 350ml', 'price' => 6000, 'cost_price' => 4000, 'category' => 'Minuman', 'stock' => 40],
            ['name' => 'Kopi Kaleng', 'price' => 8000, 'cost_price' => 5500, 'category' => 'Minuman', 'stock' => 30],
            ['name' => 'Susu Kotak 200ml', 'price' => 7000, 'cost_price' => 5000, 'category' => 'Minuman', 'stock' => 25],
            ['name' => 'Jus Buah Kemasan', 'price' => 9000, 'cost_price' => 6500, 'category' => 'Minuman', 'stock' => 20],
            
            // Makanan
            ['name' => 'Mi Instan Goreng', 'price' => 3500, 'cost_price' => 2800, 'category' => 'Makanan', 'stock' => 60],
            ['name' => 'Mi Instan Kuah', 'price' => 3500, 'cost_price' => 2800, 'category' => 'Makanan', 'stock' => 60],
            ['name' => 'Biskuit Cokelat', 'price' => 7000, 'cost_price' => 5000, 'category' => 'Makanan', 'stock' => 35],
            ['name' => 'Roti Tawar', 'price' => 15000, 'cost_price' => 11000, 'category' => 'Makanan', 'stock' => 15],
            ['name' => 'Keripik Kentang', 'price' => 10000, 'cost_price' => 7500, 'category' => 'Makanan', 'stock' => 25],
            ['name' => 'Cokelat Batang', 'price' => 12000, 'cost_price' => 9000, 'category' => 'Makanan', 'stock' => 20],
            ['name' => 'Permen', 'price' => 1000, 'cost_price' => 700, 'category' => 'Makanan', 'stock' => 100],
            
            // ATK
            ['name' => 'Pulpen Biru', 'price' => 4000, 'cost_price' => 2500, 'category' => 'ATK', 'stock' => 45],
            ['name' => 'Pulpen Hitam', 'price' => 4000, 'cost_price' => 2500, 'category' => 'ATK', 'stock' => 45],
            ['name' => 'Pensil 2B', 'price' => 3000, 'cost_price' => 2000, 'category' => 'ATK', 'stock' => 40],
            ['name' => 'Buku Tulis 38 Lbr', 'price' => 6000, 'cost_price' => 4000, 'category' => 'ATK', 'stock' => 30],
            ['name' => 'Penghapus', 'price' => 2000, 'cost_price' => 1200, 'category' => 'ATK', 'stock' => 50],
            ['name' => 'Penggaris 30cm', 'price' => 5000, 'cost_price' => 3000, 'category' => 'ATK', 'stock' => 25],
            ['name' => 'Spidol Whiteboard', 'price' => 8000, 'cost_price' => 5500, 'category' => 'ATK', 'stock' => 20],
            
            // Kebutuhan Harian
            ['name' => 'Sabun Mandi', 'price' => 8000, 'cost_price' => 5500, 'category' => 'Kebutuhan Harian', 'stock' => 30],
            ['name' => 'Sampo Sachet', 'price' => 2000, 'cost_price' => 1400, 'category' => 'Kebutuhan Harian', 'stock' => 80],
            ['name' => 'Pasta Gigi', 'price' => 12000, 'cost_price' => 9000, 'category' => 'Kebutuhan Harian', 'stock' => 20],
            ['name' => 'Sikat Gigi', 'price' => 10000, 'cost_price' => 7000, 'category' => 'Kebutuhan Harian', 'stock' => 25],
            ['name' => 'Tisu Pocket', 'price' => 3000, 'cost_price' => 2000, 'category' => 'Kebutuhan Harian', 'stock' => 40],
            ['name' => 'Masker Medis', 'price' => 15000, 'cost_price' => 10000, 'category' => 'Kebutuhan Harian', 'stock' => 35],
        ];

        $now = now();

        foreach ($items as $i => $data) {
            $sku = strtoupper(substr(Str::slug($data['category']), 0, 3)) . '-' . 
                   strtoupper(substr(Str::slug($data['name']), 0, 3)) . '-' . 
                   $now->format('ymd') . '-' . 
                   str_pad((string)($i+1), 3, '0', STR_PAD_LEFT);

            Product::create([
                'name' => $data['name'],
                'sku' => $sku,
                'price' => $data['price'],
                'cost_price' => $data['cost_price'] ?? round($data['price'] * 0.7),
                'stock' => $data['stock'] ?? 0,
                'min_stock' => rand(5, 15),
                'category' => $data['category'],
                'description' => $this->getDescription($data['name']),
                'status' => 'active',
            ]);
        }
    }

    private function getDescription(string $name): string
    {
        $descriptions = [
            'Air Mineral 600ml' => 'Air mineral kemasan 600ml, segar dan menyehatkan',
            'Teh Botol 350ml' => 'Teh botol kemasan 350ml dengan rasa teh asli',
            'Kopi Kaleng' => 'Kopi kaleng siap minum, praktis dan nikmat',
            'Susu Kotak 200ml' => 'Susu kotak UHT 200ml, bergizi tinggi',
            'Jus Buah Kemasan' => 'Jus buah segar dalam kemasan praktis',
            'Mi Instan Goreng' => 'Mi instan goreng dengan berbagai varian rasa',
            'Mi Instan Kuah' => 'Mi instan kuah dengan kuah yang lezat',
            'Biskuit Cokelat' => 'Biskuit dengan lapisan cokelat yang manis',
            'Roti Tawar' => 'Roti tawar segar untuk sarapan',
            'Keripik Kentang' => 'Keripik kentang renyah dengan berbagai rasa',
            'Cokelat Batang' => 'Cokelat batang premium dengan rasa yang nikmat',
            'Permen' => 'Permen dengan berbagai varian rasa',
            'Pulpen Biru' => 'Pulpen tinta biru untuk menulis',
            'Pulpen Hitam' => 'Pulpen tinta hitam untuk menulis',
            'Pensil 2B' => 'Pensil 2B untuk menulis dan menggambar',
            'Buku Tulis 38 Lbr' => 'Buku tulis 38 lembar untuk catatan kuliah',
            'Penghapus' => 'Penghapus untuk menghapus tulisan pensil',
            'Penggaris 30cm' => 'Penggaris plastik 30cm untuk mengukur',
            'Spidol Whiteboard' => 'Spidol untuk menulis di whiteboard',
            'Sabun Mandi' => 'Sabun mandi dengan wangi yang segar',
            'Sampo Sachet' => 'Sampo sachet praktis untuk sekali pakai',
            'Pasta Gigi' => 'Pasta gigi untuk menjaga kesehatan gigi',
            'Sikat Gigi' => 'Sikat gigi dengan bulu lembut',
            'Tisu Pocket' => 'Tisu pocket praktis untuk dibawa kemana-mana',
            'Masker Medis' => 'Masker medis 3 ply untuk perlindungan',
        ];

        return $descriptions[$name] ?? 'Produk berkualitas untuk kebutuhan sehari-hari';
    }
}
