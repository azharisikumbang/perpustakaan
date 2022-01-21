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
        Rak::factory()->count(20)->create();
        Buku::factory()->count(20)->create(['rak_id' => rand(1, 20)]);
    }
}
