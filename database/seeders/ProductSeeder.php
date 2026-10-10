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
        // Cache master data supaya tidak query berulang
        $categories = Category::pluck('id', 'name');
        $units      = Unit::pluck('id', 'name');
        $suppliers  = Supplier::pluck('id', 'name');
        $locations  = Location::pluck('id', 'code');

        $products = [
            // ===== BAHAN POKOK =====
            [
                'code'        => 'BRG001',
                'name'        => 'Beras Premium',
                'category'    => 'Bahan Pokok',
                'unit'        => 'Kilogram',
                'supplier'    => 'PT Sumber Pangan Nusantara',
                'location'    => 'A1',
                'stock'       => 500,
                'min_stock'   => 100,
                'description' => 'Beras kualitas premium, kemasan 5 kg',
            ],
            [
                'code'        => 'BRG002',
                'name'        => 'Gula Pasir',
                'category'    => 'Bahan Pokok',
                'unit'        => 'Kilogram',
                'supplier'    => 'PT Sumber Pangan Nusantara',
                'location'    => 'A1',
                'stock'       => 20,
                'min_stock'   => 30,
                'description' => 'Gula pasir putih',
            ],
            [
                'code'        => 'BRG003',
                'name'        => 'Tepung Terigu',
                'category'    => 'Bahan Pokok',
                'unit'        => 'Kilogram',
                'supplier'    => 'PT Sumber Pangan Nusantara',
                'location'    => 'A2',
                'stock'       => 0,
                'min_stock'   => 20,
                'description' => 'Tepung terigu serbaguna',
            ],
            [
                'code'        => 'BRG004',
                'name'        => 'Minyak Goreng',
                'category'    => 'Bahan Pokok',
                'unit'        => 'Liter',
                'supplier'    => 'PT Sumber Pangan Nusantara',
                'location'    => 'A2',
                'stock'       => 35,
                'min_stock'   => 50,
                'description' => 'Minyak goreng kemasan pouch 2L',
            ],

            // ===== MINUMAN =====
            [
                'code'        => 'BRG005',
                'name'        => 'Air Mineral Galon',
                'category'    => 'Minuman',
                'unit'        => 'Pieces',
                'supplier'    => 'PT Tirta Minuman Segar',
                'location'    => 'B1',
                'stock'       => 80,
                'min_stock'   => 20,
                'description' => 'Air mineral galon 19L',
            ],
            [
                'code'        => 'BRG006',
                'name'        => 'Kopi Sachet',
                'category'    => 'Minuman',
                'unit'        => 'Dus',
                'supplier'    => 'PT Tirta Minuman Segar',
                'location'    => 'B1',
                'stock'       => 15,
                'min_stock'   => 10,
                'description' => 'Kopi instan sachet, 1 dus = 24 pcs',
            ],
            [
                'code'        => 'BRG007',
                'name'        => 'Teh Celup',
                'category'    => 'Minuman',
                'unit'        => 'Dus',
                'supplier'    => 'PT Tirta Minuman Segar',
                'location'    => 'B2',
                'stock'       => 5,
                'min_stock'   => 15,
                'description' => 'Teh celup, 1 dus = 25 sachet',
            ],

            // ===== ALAT TULIS =====
            [
                'code'        => 'BRG008',
                'name'        => 'Pulpen Hitam',
                'category'    => 'Alat Tulis',
                'unit'        => 'Pieces',
                'supplier'    => 'Toko Alat Tulis Jaya',
                'location'    => 'B2',
                'stock'       => 200,
                'min_stock'   => 50,
                'description' => 'Pulpen tinta hitam standar',
            ],
            [
                'code'        => 'BRG009',
                'name'        => 'Buku Tulis 38 Lembar',
                'category'    => 'Alat Tulis',
                'unit'        => 'Pieces',
                'supplier'    => 'Toko Alat Tulis Jaya',
                'location'    => 'B2',
                'stock'       => 150,
                'min_stock'   => 30,
                'description' => 'Buku tulis 38 lembar',
            ],
            [
                'code'        => 'BRG010',
                'name'        => 'Kertas HVS A4',
                'category'    => 'Alat Tulis',
                'unit'        => 'Rim',
                'supplier'    => 'Toko Alat Tulis Jaya',
                'location'    => 'G1',
                'stock'       => 3,
                'min_stock'   => 5,
                'description' => 'Kertas HVS A4 70gsm, 1 rim = 500 lembar',
            ],

            // ===== KEBERSIHAN =====
            [
                'code'        => 'BRG011',
                'name'        => 'Sabun Cuci Piring',
                'category'    => 'Kebersihan',
                'unit'        => 'Liter',
                'supplier'    => 'PT Bersih Cemerlang',
                'location'    => 'G1',
                'stock'       => 40,
                'min_stock'   => 20,
                'description' => 'Sabun cuci piring cair',
            ],
            [
                'code'        => 'BRG012',
                'name'        => 'Pembersih Lantai',
                'category'    => 'Kebersihan',
                'unit'        => 'Liter',
                'supplier'    => 'PT Bersih Cemerlang',
                'location'    => 'G1',
                'stock'       => 25,
                'min_stock'   => 25,
                'description' => 'Pembersih lantai wangi',
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['code' => $p['code']],
                [
                    'name'        => $p['name'],
                    'description' => $p['description'],
                    'category_id' => $categories[$p['category']]  ?? null,
                    'unit_id'     => $units[$p['unit']]            ?? null,
                    'supplier_id' => $suppliers[$p['supplier']]    ?? null,
                    'location_id' => $locations[$p['location']]    ?? null,
                    'stock'       => $p['stock'],
                    'min_stock'   => $p['min_stock'],
                    'status'      => 'active',
                ]
            );
        }

        $this->command->info('✅ Products seeded: ' . count($products));
    }
}