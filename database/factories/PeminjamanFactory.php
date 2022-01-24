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
            'kode' => sprintf("%s/PINJAM/%s", date('Y/m'), str_pad(rand(0, 99999), 6, '0', STR_PAD_LEFT)),
            'tanggal_peminjaman' => $this->faker->dateTime(),
            'lama_peminjaman' => $this->faker->randomDigitNotZero(),
            'tanggal_pengembalian' => null,
            'nominal_denda' => $this->faker->randomNumber(4)
        ];
    }
}
