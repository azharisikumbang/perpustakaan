<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Services\HitungKeterlambatanService;
use App\Services\LaporanKeanggotaanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class LaporanKeanggotaanTest extends TestCase
{
    use RefreshDatabase;

    private int $expectedTotalAnggota = 100;

    protected function generateFakeData() : void
    {
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
        $this->generateFakeData();

        $laporanKeanggotaanService = new LaporanKeanggotaanService();
        $result = $laporanKeanggotaanService->getData();

        // expected 100 data
        $this->assertCount($this->expectedTotalAnggota, $result);
    }

    /** @test */
    public function jumlah_peminjaman_dan_denda_anggota_harus_sesuai_dengan_sebenarnya()
    {
        $this->generateFakeData();

        $laporanKeanggotaanService = new LaporanKeanggotaanService();
        $result = $laporanKeanggotaanService->getData();

        $result->each(function ($anggota) {
            if ($anggota['jumlah_peminjaman'] < 1) {
                $this->assertEquals(0, (int) $anggota['jumlah_peminjaman']);
                $this->assertEquals(0, (int) $anggota['akumulasi_denda']);
            }

            $peminjaman = Peminjaman::selectRaw('id, tanggal_pengembalian')
                ->where('peminjam', $anggota['id'])
                ->get();

            if (is_null($anggota['akumulasi_denda'])) return;

            $pembayaran = Pembayaran::selectRaw('sum(nominal) as akumulasi_denda')
                ->whereIn('peminjaman_id', $peminjaman->pluck('id')->toArray())
                ->first();

            $this->assertEquals($peminjaman->count(), $anggota['jumlah_peminjaman']);
            $this->assertEquals($pembayaran->akumulasi_denda, $anggota['akumulasi_denda']);
        });
    }

    /** @test */
    public function anggota_yang_punya_peminjaman_yang_dikembalikan_tepat_waktu_tidak_punya_denda_dilaporan()
    {
        $anggota = Anggota::factory()->create();

        // 3 peminjmana
        $nominal_denda = 1000;
        $peminjaman = Peminjaman::factory()->make([
            'nominal_denda' => $nominal_denda,
            'tanggal_peminjaman' => '2022-01-01 09:00:00',
            'lama_peminjaman' => 7,
            'tanggal_pengembalian' => '2022-01-06 09:00:00'
        ]);

        $anggota->peminjaman()->save($peminjaman);

        $laporanKeanggotaanService = new LaporanKeanggotaanService();
        $result = $laporanKeanggotaanService->getData();

        $result->each(function($anggota) {
            $this->assertEquals(1, (int) $anggota['jumlah_peminjaman']);
            $this->assertEquals(0, (int) $anggota['akumulasi_denda']);
        });  
    }

    /** @test */
    public function anggota_yang_punya_peminjaman_yang_dikembalikan_tidak_tepat_waktu_punya_denda_dilaporan()
    {
        $anggota = Anggota::factory()->create();

        // 3 peminjmana
        $nominal_denda = 1000;
        $peminjaman = Peminjaman::factory()->make([
            'nominal_denda' => $nominal_denda,
            'tanggal_peminjaman' => '2022-01-01 09:00:00',
            'lama_peminjaman' => 7,
            'tanggal_pengembalian' => '2022-01-20 09:00:00'
        ]);

        $hitungKeterlambatanService = new HitungKeterlambatanService();
        $keterlambatan = $hitungKeterlambatanService->hitung($peminjaman, new \DateTime($peminjaman->tanggal_pengembalian));

        $pembayaran = Pembayaran::factory()->make([
            'nominal' => $peminjaman->nominal_denda * $keterlambatan
        ]);

        $anggota->peminjaman()->save($peminjaman);
        $peminjaman->pembayaran()->save($pembayaran);

        $laporanKeanggotaanService = new LaporanKeanggotaanService();
        $result = $laporanKeanggotaanService->getData();

        $result->each(function($anggota) {
            $this->assertEquals(1, (int) $anggota['jumlah_peminjaman']);
            $this->assertEquals(12000, (int) $anggota['akumulasi_denda']);
        }); 
    }

    /** @test */
    public function anggota_yang_punya_peminjaman_tepat_waktu_ataupun_terlambat_harus_tercantum_dengan_benar_dilaporan()
    {
        $anggota = Anggota::factory()->create();
        $hitungKeterlambatanService = new HitungKeterlambatanService();

        // peminjaman 1 tidak terlambat
        $nominal_denda = 1000;
        $peminjaman = Peminjaman::factory()->make([
            'nominal_denda' => $nominal_denda,
            'tanggal_peminjaman' => '2022-01-01 09:00:00',
            'lama_peminjaman' => 7,
            'tanggal_pengembalian' => '2022-01-06 09:00:00'
        ]);

        $anggota->peminjaman()->save($peminjaman);

        // peminjaman 2 terlambat 22 hari
        $peminjaman = Peminjaman::factory()->make([
            'nominal_denda' => $nominal_denda,
            'tanggal_peminjaman' => '2022-01-01 09:00:00',
            'lama_peminjaman' => 7,
            'tanggal_pengembalian' => '2022-01-30 09:00:00'
        ]);

        $keterlambatan = $hitungKeterlambatanService->hitung($peminjaman, new \DateTime($peminjaman->tanggal_pengembalian));

        $pembayaran = Pembayaran::factory()->make([
            'nominal' => $peminjaman->nominal_denda * $keterlambatan
        ]);

        $anggota->peminjaman()->save($peminjaman);
        $peminjaman->pembayaran()->save($pembayaran);

        // peminjaman 3 terlambat 12 hari
        $peminjaman = Peminjaman::factory()->make([
            'nominal_denda' => $nominal_denda,
            'tanggal_peminjaman' => '2022-01-01 09:00:00',
            'lama_peminjaman' => 7,
            'tanggal_pengembalian' => '2022-01-20 09:00:00'
        ]);

        $keterlambatan = $hitungKeterlambatanService->hitung($peminjaman, new \DateTime($peminjaman->tanggal_pengembalian));

        $pembayaran = Pembayaran::factory()->make([
            'nominal' => $peminjaman->nominal_denda * $keterlambatan
        ]);

        $anggota->peminjaman()->save($peminjaman);
        $peminjaman->pembayaran()->save($pembayaran);

        $laporanKeanggotaanService = new LaporanKeanggotaanService();
        $result = $laporanKeanggotaanService->getData();
        $result->each(function($anggota) {
            $this->assertEquals(3, (int) $anggota['jumlah_peminjaman']);
            $this->assertEquals(34000, (int) $anggota['akumulasi_denda']);
        }); 
    }

    /** @test */
    public function perhitungan_atribute_peminjaman_dan_pengembalian_dari_setiap_anggota()
    {   
        $laporanKeanggotaanService = new LaporanKeanggotaanService();

        $anggota = Anggota::factory()->create();

        // 5 peminjaman 3 pengembalian
        // 2 dikembalikan tepat waktu
        $peminjaman = Peminjaman::factory()->make([
            'tanggal_peminjaman' => '2022-01-01 09:00:00',
            'lama_peminjaman' => 7,
            'tanggal_pengembalian' => '2022-01-02 09:00:00'
        ]);

        $anggota->peminjaman()->save($peminjaman);

        $peminjaman = Peminjaman::factory()->make([
            'tanggal_peminjaman' => '2022-01-01 09:00:00',
            'lama_peminjaman' => 7,
            'tanggal_pengembalian' => '2022-01-02 09:00:00'
        ]);

        $anggota->peminjaman()->save($peminjaman);

        // 1 dikembalikan terlambat
        $peminjaman = Peminjaman::factory()->make([
            'tanggal_peminjaman' => '2022-01-01 09:00:00',
            'lama_peminjaman' => 7,
            'tanggal_pengembalian' => '2022-01-30 09:00:00'
        ]);

        $anggota->peminjaman()->save($peminjaman);

        // 1 peminjaman berlangsung
        $peminjaman = Peminjaman::factory()->make([
            'tanggal_peminjaman' => '2022-01-27 09:00:00',
            'lama_peminjaman' => 7,
            'tanggal_pengembalian' => null
        ]);

        $anggota->peminjaman()->save($peminjaman);

        // 1 peminjaman belum dikembalikan
        $peminjaman = Peminjaman::factory()->make([
            'tanggal_peminjaman' => '2022-01-01 09:00:00',
            'lama_peminjaman' => 7,
            'tanggal_pengembalian' => null
        ]);

        $anggota->peminjaman()->save($peminjaman);


        $collection = $laporanKeanggotaanService->getData();
        $collection->each(function($anggota) {
            // 5 peminjaman
            $this->assertEquals(5, $anggota['jumlah_peminjaman']);
            // 3 pengembalian
            $this->assertEquals(3, $anggota['jumlah_pengembalian']);
            // 2 dikembalikan tepat waktu
            $this->assertEquals(2, $anggota['jumlah_pengembalian_tepat_waktu']);
            // 1 dikembalikan terlambat
            $this->assertEquals(1, $anggota['jumlah_pengembalian_terlambat']);
            // 1 peminjaman berlangsung
            $this->assertEquals(1, $anggota['jumlah_peminjaman_berlangsung']);
            // 1 peminjaman belum dikemlabikan
            $this->assertEquals(1, $anggota['jumlah_peminjaman_belum_dikembalikan']);
        });
    }
    
    /** @test */
    public function laporan_bisa_didownload_dalam_bentuk_excel()
    {
        $this->markTestIncomplete();
    }
}
