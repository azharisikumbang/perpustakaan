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
    	$day = rand(1, 28);
        $tanggalPengembalian = ($day % 2 == 0) ? null : date("Y-m-$day H:i:s");

        Rak::factory()
            ->count(20)
            ->has(Buku::factory()->count(rand(1, 5)))
            ->create();

        $listAnggota = Anggota::factory()->count(20)->create();

        Peminjaman::factory()
            ->count(15)
            ->create([
            	'tanggal_pengembalian' => $tanggalPengembalian,
                'peminjam' => rand(1, 20)
            ]);

        $listBuku = Buku::all();

        Peminjaman::all()->each(function($peminjaman) use ($listBuku) {
            $peminjaman->buku()->attach(
                $listBuku->random(rand(1, 5))->pluck('id')->toArray()
            );
        }); 
    }
}
