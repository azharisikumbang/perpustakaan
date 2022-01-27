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
        $denda = [1000, 2000, 3000, 4000, 5000];
        $tanggal_pengembalian = (rand(0, 2) > 1) ? $this->faker->dateTimeBetween("-7 days") : null;

        return [
            'kode' => sprintf("%s/PINJAM/%s", date('Y/m'), str_pad(rand(0, 99999), 6, '0', STR_PAD_LEFT)),
            'tanggal_peminjaman' => $this->faker->dateTimeThisYear(),
            'lama_peminjaman' => rand(7, 10),
            'tanggal_pengembalian' => $tanggal_pengembalian,
            'nominal_denda' => $denda[rand(0, 4)],
        ];
    }
}
