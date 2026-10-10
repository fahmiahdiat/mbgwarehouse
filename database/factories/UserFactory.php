<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
        ];
    }

    /** State: email belum diverifikasi */
    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    /** State: admin gudang */
    public function admin(): static
    {
        return $this->state(fn () => [
            'name'  => 'Admin Gudang',
            'email' => 'admin-' . fake()->unique()->numberBetween(1, 9999) . '@test.com',
        ]);
    }
}