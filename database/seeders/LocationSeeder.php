<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['code' => 'A1', 'name' => 'Rak A1', 'description' => 'Rak depan, sisi kiri bawah'],
            ['code' => 'A2', 'name' => 'Rak A2', 'description' => 'Rak depan, sisi kiri atas'],
            ['code' => 'B1', 'name' => 'Rak B1', 'description' => 'Rak tengah, sisi kiri'],
            ['code' => 'B2', 'name' => 'Rak B2', 'description' => 'Rak tengah, sisi kanan'],
            ['code' => 'G1', 'name' => 'Gudang Belakang', 'description' => 'Area gudang belakang untuk stok besar'],
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate(
                ['code' => $location['code']],
                $location
            );
        }

        $this->command->info('✅ Locations seeded: ' . count($locations));
    }
}