<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\Rak;
use App\Services\HitungKeterlambatanService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	// data rak dan data buku
    	Rak::factory()->count(20)->create()->each(function ($rak) {
        	$buku = Buku::factory()->count(20)->make();
        	$rak->buku()->saveMany($buku);
        });

    	// data anggota dan peminjamannya
    	Anggota::factory()->count(100)->create()->each(function ($anggota) {
    		$has = rand(0, 1);
    		if ($has) {
    			$peminjaman = Peminjaman::factory()->count(rand(1, 10))->make();
    			$anggota->peminjaman()->saveMany($peminjaman);
    		}
    	});

    	// lengkapi data peminjaman - buku
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

    	$peminjaman = Peminjaman::all()->each(function($peminjaman) {
    		if (null != $peminjaman->tanggal_pengembalian) {
                $hitungKeterlambatanService = new HitungKeterlambatanService();
                $keterlambatan = $hitungKeterlambatanService->hitung($peminjaman, new \DateTime($peminjaman->tanggal_pengembalian));

    			$pembayaran = Pembayaran::factory()->make([
    				'nominal' => $peminjaman->nominal_denda * $keterlambatan
    			]);

    			$peminjaman->pembayaran()->save($pembayaran);
    		}
    	});
    }
}
