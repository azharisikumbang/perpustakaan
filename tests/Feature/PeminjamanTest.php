<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PeminjamanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_index_page()
    {
        $this->signIn();

        Peminjaman::factory()->count(10)->create();

        $response = $this->get('admin/peminjaman');

        $this->markTestIncomplete('todo tomorrow');
    }

    /** @test */
    public function a_request_for_out_of_stock_of_book_handled_correctly()
    {
        $this->markTestIncomplete('todo tomorrow');
    }
    
}
