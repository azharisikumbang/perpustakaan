<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Services\HitungKeterlambatanService;
use App\Services\LaporanKeanggotaanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LaporanKeanggotaanTest extends TestCase
{
    use RefreshDatabase;

    private int $expectedTotalAnggota = 100;

    protected function setUp() : void
    {
        parent::setUp();

        $this->signIn();
        $this->withoutExceptionHandling();

        Anggota::factory()->count($this->expectedTotalAnggota)->create()->each(function ($anggota) {
            $has = rand(0, 1);
            if ($has) {
                $peminjaman = Peminjaman::factory()->count(rand(1, 10))->make();
                $anggota->peminjaman()->saveMany($peminjaman);
            }
        });

        Peminjaman::all()->each(function($peminjaman) {
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
    public function semua_data_keanggotaan_harus_masuk_ke_laporan()
    {
        $laporanKeanggotaanService = new LaporanKeanggotaanService();
        $result = $laporanKeanggotaanService->getData();

        // expected 100 data
        $this->assertCount($this->expectedTotalAnggota, $result);
    }

    /** @test */
    public function jumlah_peminjaman_dan_denda_anggota_harus_sesuai_dengan_sebenarnya()
    {
        $laporanKeanggotaanService = new LaporanKeanggotaanService();
        $result = $laporanKeanggotaanService->getData();

        $result->each(function ($anggota) {
            if ($anggota->jumlah_peminjaman < 1) {
                $jumlah_peminjaman = (int) $anggota->jumlah_peminjaman;
                $akumulasi_denda = (int) $anggota->akumulasi_denda;

                $this->assertEquals(0, $jumlah_peminjaman);
                $this->assertEquals(0, $akumulasi_denda);

                // @TODO cek betul tidak ada data peminjaman dan pembayaran pada database
                return;
            }

            $peminjaman = Peminjaman::selectRaw('id, tanggal_pengembalian')
                ->where('peminjam', $anggota->id)
                ->get();

            if (is_null($anggota->akumulasi_denda)) return;

            $pembayaran = Pembayaran::selectRaw('sum(nominal) as akumulasi_denda')
                ->whereIn('peminjaman_id', $peminjaman->pluck('id')->toArray())
                ->first();

            $this->assertEquals($peminjaman->count(), $anggota->jumlah_peminjaman);
            $this->assertEquals($pembayaran->akumulasi_denda, $anggota->akumulasi_denda);            
        });
    }
    
}
