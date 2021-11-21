<?php

namespace Tests\Feature;

use App\Models\Rak;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManajemenRakTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_rak_item_handled_correctly()
    {
        $this->singIn();

        $dataRak = [
        	'kode' => 'A01',
        	'alias' => 'SEJARAH'
        ];
        
        $response = $this->post('/admin/rak', $dataRak);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHasAll(['status' => 1, 'messages']);

        $this->assertDatabaseCount('rak', 1);
        $this->assertDatabaseHas('rak', [
        	'kode' => 'A01',
        	'alias' => 'SEJARAH'
        ]);
    }

    /** @test */
    public function invalid_store_request_handled_correctly()
    {
    	$this->singIn();
    	$invalidRequest = ['kode' => null];
    	$response = $this->post('/admin/rak', $invalidRequest);

    	$response->assertInvalid(['kode']);
    	$this->assertDatabaseCount('rak', 0);
    }
    
    /** @test */
    public function it_can_delete_a_rak_row()
    {
        $this->singIn();

        $rak = Rak::factory()->create();

        $response = $this->deleteJson('/admin/rak/' . $rak->id);

        $response->assertOk();
        $response->assertJsonStructure(['status', 'code', 'messages']);
    }
    
    /** @test */
    public function invalid_ids_not_handled_correctly_on_delete()
    {
        $this->singIn();

        $response = $this->deleteJson('/admin/rak/1');
        $response->assertNotFound();
    }
    

}
