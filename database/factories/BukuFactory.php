<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BukuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kode' => strtoupper($this->faker->bothify('???-?##-###-' . time())),
            'isbn' => $this->faker->isbn13(),
            'judul' => strtoupper($this->faker->sentence()),
            'penerbit' => $this->faker->company(),
            'pengarang' => $this->faker->name(),
            'tahun_terbit' => $this->faker->year(),
            'stok' => rand(10, 1000),
            'tanggal_masuk' => $this->faker->dateTimeBetween('-3 years'),
            'sampul' => null
        ];
    }
}
