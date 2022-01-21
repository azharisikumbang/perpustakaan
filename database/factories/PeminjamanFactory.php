<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PeminjamanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kode' => '2021/12/PINJAM/' . rand(1, 99999),
            'tanggal_peminjaman' => $this->faker->dateTime(),
            'lama_peminjaman' => $this->faker->randomDigitNotZero(),
            'tanggal_pengembalian' => null,
            'nominal_denda' => $this->faker->randomNumber(4),
        ];
    }
}
