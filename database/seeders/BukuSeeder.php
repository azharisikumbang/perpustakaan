<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Rak;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rak::factory()
        	->count(20)
        	->has(Buku::factory()->count(rand(0, 5)))
        	->create();
    }
}
