<?php

namespace Database\Factories\Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class SiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kelas = config('kelas');
        return [
            'nama'  => $this->faker->name,
            'kelas' => $this->faker->randomElement($kelas),
        ];
    }
}
