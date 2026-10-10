<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Admin Gudang',
                'email'    => 'admin@admin.com',
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Budi Santoso',
                'email'    => 'budi@admin.com',
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Siti Nurhaliza',
                'email'    => 'siti@admin.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        $this->command->info('✅ Users seeded: ' . count($users));
    }
}