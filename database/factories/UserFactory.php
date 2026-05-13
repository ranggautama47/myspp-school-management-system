<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'              => fake('id_ID')->name(),
            'email'             => fake()->unique()->safeEmail(),
            'phone'             => fake('id_ID')->phoneNumber(),
            'image'             => null,
            'scan_ijazah'       => null,
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
            'email_verified_at' => now(),
        ];
    }

    /** State: user sudah verified */
    public function verified(): static
    {
        return $this->state(['email_verified_at' => now()]);
    }

    /** State: user belum verified */
    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }
}
