<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PengaturanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'lama_pinjaman' => 0,
            'jumlah_pinjaman' => 0,
            'nominal_denda' => 0
        ];
    }
}
