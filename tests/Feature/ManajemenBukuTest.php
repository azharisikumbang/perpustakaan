<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\DDC;
use App\Models\Rak;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManajemenBukuTest extends TestCase
{
	use RefreshDatabase;
    /** @test */
    public function store_new_book_handled_correctly()
    {
        $this->signInAsAdministrator();

        DDC::factory()->create();
        $rak = (Rak::factory()->create())->attributesToArray();
        $buku = (Buku::factory()->make([ 'rak_id' => $rak['id'], 'ddc_id' => 1 ]))->attributesToArray();
        $response = $this->post('/admin/buku', $buku);

        $this->assertDatabaseHas('buku', [
            'kode' => $buku['kode'],
            'isbn' => $buku['isbn'],
            'judul' => $buku['judul'],
            'penerbit' => $buku['penerbit'],
            'pengarang' => $buku['pengarang'],
            'tahun_terbit' => $buku['tahun_terbit'],
            'stok' => $buku['stok'],
            'tanggal_masuk' => $buku['tanggal_masuk'],
            'rak_id' => $buku['rak_id'],
            'ddc_id' => $buku['ddc_id']
        ]);
        $this->assertDatabaseCount('buku', 1);
        $this->assertDatabaseHas('rak', ['id' => $rak['id'], 'kode' => $rak['kode'], 'alias' => $rak['alias']]);
        $this->assertDatabaseCount('rak', 1);

        $response->assertRedirect();
        $response->assertSessionHasAll(['messages', 'status' => 1]);
        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function store_invalid_book_handled_correctly()
    {
        $this->signInAsAdministrator();

        $response = $this->post('/admin/buku', [
            'kode' => null,
            'isbn' => null,
            'judul' => null,
            'penerbit' => null,
            'pengarang' => null,
            'tahun_terbit' => null,
            'stok' => null,
            'tanggal_masuk' => null,
            'rak_id' => null,
            'ddc_id' => null,
        ]);

        $this->assertDatabaseCount('buku', 0);
        $this->assertDatabaseCount('rak', 0);

        $response->assertRedirect();
        $response->assertInvalid();
        $response->assertSessionHasErrors(['kode', 'isbn', 'judul', 'penerbit', 'pengarang', 'tahun_terbit', 'stok', 'tanggal_masuk', 'rak_id', 'ddc_id']);
    }
    
    // @TODO : more case for storing

    /** @test */
    public function update_a_book_handled_correctly()
    {
        $this->signInAsAdministrator();

        DDC::factory()->create();
        $rak = Rak::factory()->count(2)->create();
        $buku = Buku::factory()->create(['rak_id' => 1, 'ddc_id' => 1]);

        $bukuRequest = [
            'kode' => 'A001-002-123456',
            'isbn' => '978-602-8519-93-8',
            'judul' => 'Laravel for dummy',
            'penerbit' => 'Laravel Book',
            'pengarang' => 'Taylor Otwel',
            'tahun_terbit' => 2020,
            'stok' => 100,
            'tanggal_masuk' => date('Y-m-d'),
            'rak_id' => 2,
            'ddc_id' => 1
        ];

        $response = $this->put("/admin/buku/{$buku->id}", $bukuRequest);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHasAll(['status' => 1, 'messages']);

        $this->assertDatabaseCount('rak', 2);
        $this->assertDatabaseCount('buku', 1);

        $this->assertDatabaseHas('buku', $bukuRequest);
    }
    
    /** @test */
    public function update_an_invalid_book_handled_correctly()
    {
        $this->signInAsAdministrator();

        DDC::factory()->create();
        $rak = Rak::factory()->create();
        $buku = Buku::factory()->create(['rak_id' => 1, 'ddc_id' => 1]);

        $bukuRequest = [
            'kode' => null,
            'isbn' => null,
            'judul' => null,
            'penerbit' => null,
            'pengarang' => null,
            'tahun_terbit' => null,
            'stok' => null,
            'tanggal_masuk' => null,
            'rak_id' => null,
            'ddc_id' => null
        ];

        $response = $this->put("/admin/buku/{$buku->id}", $bukuRequest);

        $response->assertInvalid();
        $response->assertSessionHasErrors(['kode', 'isbn', 'judul', 'penerbit', 'pengarang', 'tahun_terbit', 'stok', 'tanggal_masuk', 'rak_id', 'ddc_id']);

        $this->assertDatabaseCount('rak', 1);
        $this->assertDatabaseCount('buku', 1);

        // unset default timestamp, its fail the test
        $buku = $buku->toArray();
        unset($buku['created_at']);
        unset($buku['updated_at']);

        $this->assertDatabaseHas('buku', $buku);
    }
    
    /** @test */
    public function delete_a_book_handled_correctly()
    {
        $this->signInAsAdministrator();

        DDC::factory()->create();
        $rak = Rak::factory()->create();
        $buku = Buku::factory()->create(['rak_id' => 1, 'ddc_id' => 1]);

        $response = $this->deleteJson("/admin/buku/{$buku->id}");

        $response->assertOk();
        $response->assertJsonStructure(['status', 'code', 'messages']);

        $this->assertDatabaseCount('rak', 1);
        $this->assertDatabaseCount('buku', 0);
    }
    
}
