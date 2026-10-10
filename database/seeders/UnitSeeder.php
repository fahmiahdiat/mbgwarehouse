<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Kilogram', 'symbol' => 'Kg'],
            ['name' => 'Liter',    'symbol' => 'L'],
            ['name' => 'Pieces',   'symbol' => 'Pcs'],
            ['name' => 'Dus',      'symbol' => 'Dus'],
            ['name' => 'Rim',      'symbol' => 'Rim'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(
                ['name' => $unit['name']],
                $unit
            );
        }

        $this->command->info('✅ Units seeded: ' . count($units));
    }
}