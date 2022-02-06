<?php

namespace Tests\Feature;

use App\Models\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PengaturanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_has_a_row_when_see_edit_page()
    {
        $this->signInAsAdministrator();

        $response = $this->get('admin/pengaturan');

        $response->assertOk();
        $response->assertViewHasAll(['lama_pinjaman', 'jumlah_pinjaman', 'nominal_denda']);

        $this->assertDatabaseCount('pengaturan', 1);
    }

    /** @test */
    public function it_should_has_valid_data_when_see_edit_page()
    {
        $this->signInAsAdministrator();

        $validData = Pengaturan::create([
            'lama_pinjaman' => 10,
            'jumlah_pinjaman' => 3,
            'nominal_denda' => 1000
        ]);

        $response = $this->get('admin/pengaturan');
        $response->assertViewHasAll([
            'lama_pinjaman' => $validData->lama_pinjaman,
            'jumlah_pinjaman' => $validData->jumlah_pinjaman,
            'nominal_denda' => $validData->nominal_denda
        ]);
    }
            

    /** @test */
    public function it_can_update_row()
    {
        $this->signInAsAdministrator();

        Pengaturan::create([
            'lama_pinjaman' => 10,
            'jumlah_pinjaman' => 3,
            'nominal_denda' => 1000
        ]);

        $response = $this->put('/admin/pengaturan', [
            'lama_pinjaman' => 5,
            'jumlah_pinjaman' => 2,
            'nominal_denda' => 2000
        ]);

        $response->assertRedirect();
        $response->assertSessionHasAll(['status' => 1, 'messages']);

        $this->assertDatabaseHas('pengaturan', [
            'lama_pinjaman' => 5,
            'jumlah_pinjaman' => 2,
            'nominal_denda' => 2000
        ]);

        $this->assertDatabaseCount('pengaturan', 1);
    }
    

    /** @test */
    public function it_cannot_store_data()
    {
        $this->signInAsAdministrator();

        $response = $this->post('pengaturan', []);
        $response->assertNotFound();

        $this->assertDatabaseCount('pengaturan', 0);
    }

    /** @test */
    public function it_cannot_delete_data()
    {
        $this->signInAsAdministrator();

        $this->assertDatabaseCount('pengaturan', 0);

        Pengaturan::create([
            'lama_pinjaman' => 10,
            'jumlah_pinjaman' => 3,
            'nominal_denda' => 1000
        ]);

        $this->assertDatabaseCount('pengaturan', 1);

        $response = $this->delete('pengaturan', []);
        $response->assertNotFound();

        $response = $this->delete('pengaturan/1', []);
        $response->assertNotFound();

        $this->assertDatabaseCount('pengaturan', 1);
        $this->assertDatabaseHas('pengaturan', [
            'lama_pinjaman' => 10,
            'jumlah_pinjaman' => 3,
            'nominal_denda' => 1000
        ]);

    }
    
}
