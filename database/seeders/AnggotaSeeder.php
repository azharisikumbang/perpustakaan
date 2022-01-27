<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class AnggotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Anggota::factory()->count(20)->create()->each(function ($anggota) {
    		$has = rand(0, 1);
    		if ($has) {
    			$peminjaman = Peminjaman::factory()->count(rand(1, 10))->make();
    			$anggota->peminjaman()->saveMany($peminjaman);
    		}
    	});
    }
}
