<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RakFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kode' => strtoupper($this->faker->bothify('?##')),
            'alias' => strtoupper($this->faker->word())
        ];
    }
}
