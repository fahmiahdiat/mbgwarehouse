<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // ===== KATEGORI YANG DIMONITOR =====
            [
                'name'         => 'Bahan Kering',
                'description'  => 'Beras, gula, tepung, minyak goreng, dan sejenisnya',
                'is_monitored' => true,
            ],
            [
                'name'         => 'Minuman',
                'description'  => 'Air mineral, kopi, teh, sirup',
                'is_monitored' => true,
            ],
            [
                'name'         => 'Kebutuhan Umum',
                'description'  => 'Gas LPG, sabun, deterjen, tisue, pembersih',
                'is_monitored' => true,
            ],
            [
                'name'         => 'ATK',
                'description'  => 'Alat tulis kantor: kertas, pulpen, spidol, tinta',
                'is_monitored' => true,
            ],
            [
                'name'         => 'APD',
                'description'  => 'Alat Pelindung Diri: masker, sarung tangan, celemek, hairnet',
                'is_monitored' => true,
            ],
            [
                'name'         => 'P3K',
                'description'  => 'Pertolongan Pertama: plester, kasa, betadine, obat luka',
                'is_monitored' => true,
            ],

            // ===== KATEGORI YANG TIDAK DIMONITOR =====
            [
                'name'         => 'Bahan Segar',
                'description'  => 'Sayur, daging, rempah segar — datang & habis di hari yang sama',
                'is_monitored' => false,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('✅ Categories seeded: ' . count($categories));
        $this->command->info('   • 6 monitored, 1 unmonitored (Bahan Segar)');
    }
}