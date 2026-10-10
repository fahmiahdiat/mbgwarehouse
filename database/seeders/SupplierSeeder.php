<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name'    => 'PT Sumber Pangan Nusantara',
                'phone'   => '021-5550001',
                'email'   => 'sales@sumberpangan.co.id',
                'address' => 'Jl. Industri Raya No. 12, Jakarta Utara',
                'pic'     => 'Andi Wijaya',
            ],
            [
                'name'    => 'CV Makmur Sentosa',
                'phone'   => '021-5550002',
                'email'   => 'order@makmursentosa.com',
                'address' => 'Jl. Raya Bekasi KM 25, Bekasi',
                'pic'     => 'Rina Kartika',
            ],
            [
                'name'    => 'PT Tirta Minuman Segar',
                'phone'   => '031-5550003',
                'email'   => 'b2b@tirtasegar.com',
                'address' => 'Kawasan Industri SIER, Surabaya',
                'pic'     => 'Hendra Kusuma',
            ],
            [
                'name'    => 'Toko Alat Tulis Jaya',
                'phone'   => '021-5550004',
                'email'   => 'atkjaya@gmail.com',
                'address' => 'Pasar Pagi Lantai 2 Blok C, Jakarta Barat',
                'pic'     => 'Lisa Marlina',
            ],
            [
                'name'    => 'PT Bersih Cemerlang',
                'phone'   => '021-5550005',
                'email'   => 'cs@bersihcemerlang.id',
                'address' => 'Jl. Cikini Raya No. 45, Jakarta Pusat',
                'pic'     => 'Dedi Firmansyah',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['name' => $supplier['name']],
                $supplier
            );
        }

        $this->command->info('✅ Suppliers seeded: ' . count($suppliers));
    }
}