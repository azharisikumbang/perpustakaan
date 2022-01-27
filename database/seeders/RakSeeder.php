<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Rak;
use Illuminate\Database\Seeder;

class RakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rak::factory()->count(20)->create()->each(function ($rak) {
        	$buku = Buku::factory()->count(20)->make();
        	$rak->buku()->saveMany($buku);
        });
    }
}
