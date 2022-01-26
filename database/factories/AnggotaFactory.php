<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AnggotaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nama' => $this->faker->name(),
            'kode' => $this->faker->bothify('?-###'),
            'institusi' => $this->faker->company(),
            'alamat_institusi' => $this->faker->address(),
            'alamat_pribadi' => $this->faker->address(),
            'jenis_kelamin' => rand(0, 3),
            'kontak' => $this->faker->phoneNumber()
        ];
    }
}
