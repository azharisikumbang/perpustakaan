<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\DDC;
use App\Models\Rak;
use App\Services\LaporanDataBukuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LaporanDataBukuTest extends TestCase
{
    use RefreshDatabase;

    private int $expectedTotalSample = 20;

    protected function generateFakeData() : void
    {
        DDC::factory()->create();
        Rak::factory()->count($this->expectedTotalSample)->create()->each(function ($rak) {
            $buku = Buku::factory()->count(100)->make(['ddc_id' => 1]);
            $rak->buku()->saveMany($buku);
        });
    }

    /** @test */
    public function seluruh_data_buku_bisa_didapatkan()
    {
        $this->generateFakeData();

        $listBuku = (new LaporanDataBukuService(20))->getData();
        $this->assertCount(20, $listBuku);
    }
}
