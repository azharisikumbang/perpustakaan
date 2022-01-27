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
        $tanggal_peminjaman = $this->faker->dateTimeThisYear();
        $lama_peminjaman = rand(7, 10);
        $batas_pengembalian = new \DateTime($tanggal_peminjaman->format('Y-m-d H:i:s'));
        $batas_pengembalian->modify(sprintf("+%s days", $lama_peminjaman + 3));
        $tanggal_pengembalian = (rand(0, 2) > 1) ? $batas_pengembalian->format('Y-m-d H:i:s') : null;

        return [
            'kode' => sprintf("%s/PINJAM/%s", date('Y/m'), str_pad(rand(0, 99999), 6, '0', STR_PAD_LEFT)),
            'tanggal_peminjaman' => $tanggal_peminjaman,
            'lama_peminjaman' => $lama_peminjaman,
            'tanggal_pengembalian' => $tanggal_pengembalian,
            'nominal_denda' => $denda[rand(0, 4)],
        ];
    }
}
