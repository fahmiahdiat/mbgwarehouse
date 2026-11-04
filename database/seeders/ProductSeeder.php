<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');
        $units      = Unit::pluck('id', 'name');
        $suppliers  = Supplier::pluck('id', 'name');
        $locations  = Location::pluck('id', 'code');

        $products = [
            // ==================== BAHAN KERING ====================
            [
                'code' => 'BRG001', 'name' => 'Beras Premium',
                'category' => 'Bahan Kering', 'unit' => 'Kilogram',
                'supplier' => 'PT Sumber Pangan Nusantara', 'location' => 'A1',
                'stock' => 500, 'min_stock' => 100,
                'description' => 'Beras kualitas premium',
            ],
            [
                'code' => 'BRG002', 'name' => 'Gula Pasir',
                'category' => 'Bahan Kering', 'unit' => 'Kilogram',
                'supplier' => 'PT Sumber Pangan Nusantara', 'location' => 'A1',
                'stock' => 20, 'min_stock' => 30,
                'description' => 'Gula pasir putih',
            ],
            [
                'code' => 'BRG003', 'name' => 'Tepung Terigu',
                'category' => 'Bahan Kering', 'unit' => 'Kilogram',
                'supplier' => 'PT Sumber Pangan Nusantara', 'location' => 'A2',
                'stock' => 0, 'min_stock' => 20,
                'description' => 'Tepung terigu serbaguna',
            ],
            [
                'code' => 'BRG004', 'name' => 'Minyak Goreng',
                'category' => 'Bahan Kering', 'unit' => 'Liter',
                'supplier' => 'PT Sumber Pangan Nusantara', 'location' => 'A2',
                'stock' => 35, 'min_stock' => 50,
                'description' => 'Minyak goreng kemasan pouch',
            ],

            // ==================== MINUMAN ====================
            [
                'code' => 'BRG005', 'name' => 'Air Mineral Galon',
                'category' => 'Minuman', 'unit' => 'Pieces',
                'supplier' => 'PT Tirta Minuman Segar', 'location' => 'B1',
                'stock' => 80, 'min_stock' => 20,
                'description' => 'Air mineral galon 19L',
            ],
            [
                'code' => 'BRG006', 'name' => 'Kopi Sachet',
                'category' => 'Minuman', 'unit' => 'Dus',
                'supplier' => 'PT Tirta Minuman Segar', 'location' => 'B1',
                'stock' => 15, 'min_stock' => 10,
                'description' => 'Kopi instan sachet',
            ],
            [
                'code' => 'BRG007', 'name' => 'Teh Celup',
                'category' => 'Minuman', 'unit' => 'Dus',
                'supplier' => 'PT Tirta Minuman Segar', 'location' => 'B2',
                'stock' => 5, 'min_stock' => 15,
                'description' => 'Teh celup 25 sachet per dus',
            ],

            // ==================== KEBUTUHAN UMUM ====================
            [
                'code' => 'BRG011', 'name' => 'Gas LPG 3 Kg',
                'category' => 'Kebutuhan Umum', 'unit' => 'Pieces',
                'supplier' => 'PT Bersih Cemerlang', 'location' => 'G1',
                'stock' => 3, 'min_stock' => 5,
                'description' => 'Tabung gas LPG 3 Kg',
            ],
            [
                'code' => 'BRG012', 'name' => 'Sabun Cuci Piring',
                'category' => 'Kebutuhan Umum', 'unit' => 'Liter',
                'supplier' => 'PT Bersih Cemerlang', 'location' => 'G1',
                'stock' => 40, 'min_stock' => 10,
                'description' => 'Sabun cuci piring cair',
            ],
            [
                'code' => 'BRG013', 'name' => 'Pembersih Lantai',
                'category' => 'Kebutuhan Umum', 'unit' => 'Liter',
                'supplier' => 'PT Bersih Cemerlang', 'location' => 'G1',
                'stock' => 8, 'min_stock' => 10,
                'description' => 'Pembersih lantai wangi',
            ],
            [
                'code' => 'BRG014', 'name' => 'Tisue Gulung',
                'category' => 'Kebutuhan Umum', 'unit' => 'Dus',
                'supplier' => 'PT Bersih Cemerlang', 'location' => 'G1',
                'stock' => 12, 'min_stock' => 5,
                'description' => 'Tisue gulung 1 dus isi 10 roll',
            ],

            // ==================== ATK ====================
            [
                'code' => 'BRG008', 'name' => 'Pulpen Hitam',
                'category' => 'ATK', 'unit' => 'Pieces',
                'supplier' => 'Toko Alat Tulis Jaya', 'location' => 'B2',
                'stock' => 200, 'min_stock' => 50,
                'description' => 'Pulpen tinta hitam standar',
            ],
            [
                'code' => 'BRG009', 'name' => 'Buku Tulis 38 Lembar',
                'category' => 'ATK', 'unit' => 'Pieces',
                'supplier' => 'Toko Alat Tulis Jaya', 'location' => 'B2',
                'stock' => 150, 'min_stock' => 30,
                'description' => 'Buku tulis 38 lembar',
            ],
            [
                'code' => 'BRG010', 'name' => 'Kertas HVS A4',
                'category' => 'ATK', 'unit' => 'Rim',
                'supplier' => 'Toko Alat Tulis Jaya', 'location' => 'G1',
                'stock' => 3, 'min_stock' => 5,
                'description' => 'Kertas HVS A4 70gsm, 1 rim = 500 lembar',
            ],
            [
                'code' => 'BRG015', 'name' => 'Tinta Printer Hitam',
                'category' => 'ATK', 'unit' => 'Pieces',
                'supplier' => 'Toko Alat Tulis Jaya', 'location' => 'B2',
                'stock' => 1, 'min_stock' => 2,
                'description' => 'Tinta printer Epson hitam',
            ],
            [
                'code' => 'BRG016', 'name' => 'Spidol Whiteboard',
                'category' => 'ATK', 'unit' => 'Pieces',
                'supplier' => 'Toko Alat Tulis Jaya', 'location' => 'B2',
                'stock' => 6, 'min_stock' => 3,
                'description' => 'Spidol whiteboard hitam',
            ],

            // ==================== APD ====================
            [
                'code' => 'BRG017', 'name' => 'Masker Medis',
                'category' => 'APD', 'unit' => 'Dus',
                'supplier' => 'PT Bersih Cemerlang', 'location' => 'B2',
                'stock' => 8, 'min_stock' => 5,
                'description' => 'Masker medis 1 dus isi 50 pcs',
            ],
            [
                'code' => 'BRG018', 'name' => 'Sarung Tangan Latex',
                'category' => 'APD', 'unit' => 'Dus',
                'supplier' => 'PT Bersih Cemerlang', 'location' => 'B2',
                'stock' => 4, 'min_stock' => 5,
                'description' => 'Sarung tangan latex 1 dus isi 100 pcs',
            ],
            [
                'code' => 'BRG019', 'name' => 'Celemek Dapur',
                'category' => 'APD', 'unit' => 'Pieces',
                'supplier' => 'Toko Alat Tulis Jaya', 'location' => 'B2',
                'stock' => 15, 'min_stock' => 10,
                'description' => 'Celemek dapur waterproof',
            ],
            [
                'code' => 'BRG020', 'name' => 'Hairnet / Penutup Kepala',
                'category' => 'APD', 'unit' => 'Dus',
                'supplier' => 'Toko Alat Tulis Jaya', 'location' => 'B2',
                'stock' => 6, 'min_stock' => 3,
                'description' => 'Hairnet 1 dus isi 100 pcs',
            ],

            // ==================== P3K ====================
            [
                'code' => 'BRG021', 'name' => 'Plester Luka',
                'category' => 'P3K', 'unit' => 'Dus',
                'supplier' => 'PT Bersih Cemerlang', 'location' => 'A1',
                'stock' => 0, 'min_stock' => 2,
                'description' => 'Plester luka 1 dus isi 100 pcs',
            ],
            [
                'code' => 'BRG022', 'name' => 'Kasa Steril',
                'category' => 'P3K', 'unit' => 'Dus',
                'supplier' => 'PT Bersih Cemerlang', 'location' => 'A1',
                'stock' => 3, 'min_stock' => 2,
                'description' => 'Kasa steril 1 dus isi 50 pcs',
            ],
            [
                'code' => 'BRG023', 'name' => 'Betadine 60ml',
                'category' => 'P3K', 'unit' => 'Pieces',
                'supplier' => 'PT Bersih Cemerlang', 'location' => 'A1',
                'stock' => 4, 'min_stock' => 2,
                'description' => 'Antiseptik luka',
            ],
            [
                'code' => 'BRG024', 'name' => 'Kapas Luka',
                'category' => 'P3K', 'unit' => 'Pieces',
                'supplier' => 'PT Bersih Cemerlang', 'location' => 'A1',
                'stock' => 5, 'min_stock' => 3,
                'description' => 'Kapas luka 50 gram',
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['code' => $p['code']],
                [
                    'name'        => $p['name'],
                    'description' => $p['description'] ?? null,
                    'category_id' => $categories[$p['category']] ?? null,
                    'unit_id'     => $units[$p['unit']]           ?? null,
                    'supplier_id' => $suppliers[$p['supplier']]   ?? null,
                    'location_id' => $locations[$p['location']]   ?? null,
                    'stock'       => $p['stock'],
                    'min_stock'   => $p['min_stock'],
                    'status'      => 'active',
                ]
            );
        }

        $this->command->info('✅ Products seeded: ' . count($products));
    }
}