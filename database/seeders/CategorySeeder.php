<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bahan Pokok',    'description' => 'Beras, gula, tepung, minyak goreng, dan sejenisnya'],
            ['name' => 'Minuman',        'description' => 'Air mineral, kopi, teh, sirup, dan sejenisnya'],
            ['name' => 'Alat Tulis',     'description' => 'Pulpen, pensil, buku tulis, penghapus, dan sejenisnya'],
            ['name' => 'Kebersihan',     'description' => 'Sabun, deterjen, pembersih lantai, dan sejenisnya'],
            ['name' => 'Peralatan',      'description' => 'Peralatan kerja dan perlengkapan kantor'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('✅ Categories seeded: ' . count($categories));
    }
}