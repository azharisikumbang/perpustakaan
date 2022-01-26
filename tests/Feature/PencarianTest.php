<?php

namespace Tests\Feature;

use App\Services\PencarianService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PencarianTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pencarian_harus_mempunyai_pagination()
    {
        $query = '0';
        $kriteria = ['all'];

        $service = new PencarianService();
        $result = $service->byModel($query, $kriteria);

        dd($result);
    }
    
}
