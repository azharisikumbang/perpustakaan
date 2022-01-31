<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Rak;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$anggota = Anggota::all();

    	$anggota->each(function ($anggota) {
	    	$has = rand(0, 1);
			if ($has) {
				$peminjaman = Peminjaman::factory()->count(rand(1, 10))->make();
				$anggota->peminjaman()->saveMany($peminjaman);
			}
		});

    	$buku = Buku::all();

    	Peminjaman::all()->each(function($peminjaman) use ($buku) {
            for ($i = 0; $i < rand(1, 5); $i++) {
                $jumlah = rand(1, 3);
                $peminjaman->buku()->attach(
                    $buku->random(1)->pluck('id')->toArray(),
                    ['jumlah' => $jumlah]
                );
            }  		
    	});
    }
}
