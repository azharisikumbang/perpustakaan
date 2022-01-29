<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\Rak;
use App\Services\HitungKeterlambatanService;
use App\Services\LaporanPengembalianService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LaporanPengembalianTest extends TestCase
{
    use RefreshDatabase;

    private int $expectedTotalSample = 20;

    protected function generateFakeData() : void
    {
        Rak::factory()->count(20)->create()->each(function ($rak) {
            $buku = Buku::factory()->count(20)->make();
            $rak->buku()->saveMany($buku);
        });

        // data anggota dan peminjamannya
        Anggota::factory()->count($this->expectedTotalSample)->create()->each(function ($anggota) {
            $has = rand(0, 1);
            if ($has) {
                $peminjaman = Peminjaman::factory()->count(rand(1, 10))->make();
                $anggota->peminjaman()->saveMany($peminjaman);
            }
        });

        // lengkapi data peminjaman - buku
        $buku = Buku::all();
        Peminjaman::all()->each(function($peminjaman) use ($buku) {
            $peminjaman->buku()->attach(
                $buku->random(rand(1, 3))->pluck('id')->toArray(),
                ['jumlah' => rand(1, 5)]
            );
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

    /** @test */
    public function seluruh_data_pengembalian_bisa_didapatkan()
    {
        $this->generateFakeData();

        $listPeminjaman = (new LaporanPengembalianService())->getData();
        $listPeminjaman->each(function($peminjaman) {
            $this->assertNotNull($peminjaman['tanggal_pengembalian']);
        });
    }
    
}
