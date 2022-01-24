<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Peminjaman;
use App\Services\HitungDendaService;
use App\Services\HitungKeterlambatanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HitungKeterlambatanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function keterlambatan_harus_sesuai_nominal_seharusnya()
    {
        $this->signIn();

        $peminjam = Anggota::factory()->create();
        $peminjaman = Peminjaman::factory()->create([
            'peminjam' => $peminjam->id, 
            'nominal_denda' => 1000,
            'tanggal_peminjaman' => date('2022-01-10 00:00:00'),
            'lama_peminjaman' => 7
        ]);

        $hitungKeterlambatanService = new HitungKeterlambatanService();
        $hariKeterlambatan = $hitungKeterlambatanService->hitung($peminjaman, new \DateTime('2022-01-20 00:00:00'));

        // keterlambatan seharusnya 3 hari
        $this->assertEquals(3, $hariKeterlambatan);

        $hitungDendaService = new HitungDendaService();
        $denda = $hitungDendaService->hitung($peminjaman, $hariKeterlambatan);
        // denda seharusnya 1000 * 3 = 3.000
        $this->assertEquals(3000, $denda);
    }
    
    /** @test */
    public function keterlambatan_berlaku_sehari_setelah_batas_peminjaman()
    {
        $this->signIn();

        $peminjam = Anggota::factory()->create();
        $peminjaman = Peminjaman::factory()->create([
            'peminjam' => $peminjam->id, 
            'nominal_denda' => 1000,
            'tanggal_peminjaman' => date('2022-01-10 00:00:00'),
            'lama_peminjaman' => 7
        ]);

        // pengembalian = 2022-01-17 00:00:00
        $hitungKeterlambatanService = new HitungKeterlambatanService();
        $hariKeterlambatan = $hitungKeterlambatanService->hitung($peminjaman, new \DateTime('2022-01-18 00:01:00'));

        // seharusnya keterlamabatan = 1 hari
        $this->assertEquals(1, $hariKeterlambatan);

        // pengembalian = 2022-01-17 09:00:00 (+9 jam)
        $hariKeterlambatan = $hitungKeterlambatanService->hitung($peminjaman, new \DateTime('2022-01-17 00:00:00'));
        // seharusnya keterlamabatan = 0 hari
        $this->assertEquals(0, $hariKeterlambatan);
    }
    
}
