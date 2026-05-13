<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        $jurusan = fake()->randomElement([
            'Teknik Informatika',
            'Akuntansi',
            'Manajemen Bisnis',
            'Teknik Elektro',
            'Desain Komunikasi Visual',
            'Keperawatan',
            'Farmasi',
            'Teknik Sipil',
        ]);

        return [
            'name'     => $jurusan,
            'semester' => fake()->numberBetween(1, 8),
            'cost'     => fake()->randomElement([
                1_500_000, 2_000_000, 2_500_000, 2_750_000, 3_000_000, 3_500_000,
            ]),
        ];
    }
}
