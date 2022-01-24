<?php

namespace Tests\Feature\Peminjaman;

use App\Models\Buku;
use App\Models\Rak;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BookListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_book_should_be_store_at_book_listng_cart()
    {
        $this->signIn();
        $this->withoutExceptionHandling();

        $rak = Rak::factory()->create();
        $books = Buku::factory()->count(3)->create(['rak_id' => $rak->id]);

        $resourceBuku = Buku::with('rak')->first();
        $requestBuku = ['buku' => $resourceBuku->id, 'jumlah' => 1];

        // a single book request
        $response = $this->postJson('admin/keranjang', $requestBuku);
        $response->assertValid();

        $response->assertSessionHas('keranjang-pinjam', function($keranjang) use ($requestBuku, $resourceBuku) {
            $this->assertArrayHasKey('list-buku', $keranjang);
            $this->assertArrayHasKey('peminjam', $keranjang);
            $this->assertArrayHasKey('petugas', $keranjang);
            $this->assertArrayHasKey('diperbaharui', $keranjang);

            $this->assertEquals([ 
                $resourceBuku->kode => [
                    'id' => $requestBuku['buku'], 
                    'kode' => $resourceBuku->kode,
                    'jumlah' => $requestBuku['jumlah'],
                    'details' => [
                        'isbn' => $resourceBuku->isbn,
                        'judul' => $resourceBuku->judul,
                        'pengarang' => $resourceBuku->pengarang,
                        'stok' => $resourceBuku->stok,
                        'rak' => $resourceBuku->rak->kode . ' - ' . $resourceBuku->rak->alias,
                        'sampul' => $resourceBuku->sampul,
                    ]
                ]
            ], $keranjang['list-buku']);

            $this->assertDatabaseHas('buku', ['id' => $requestBuku['buku']]);
            $this->assertDatabaseHas('users', ['id' => $keranjang['petugas']]);
            $this->assertEquals($keranjang['petugas'], auth()->id());

            return true;
        });

        $buku = Buku::find($requestBuku['buku']);
        $this->assertLessThanOrEqual($buku->stok, $requestBuku['jumlah']);

        $response->assertJson(function(AssertableJson $json) {
            $json
                ->has('messages')
                ->where('status', 200)
                ->where('data', array_values(session('keranjang-pinjam')['list-buku'])[0])
                ->etc();
        });
    }

    /** @test */
    public function a_book_should_be_merge_with_others_book_at_listing_cart()
    {
        $this->signIn();

        $rak = Rak::factory()->create();
        $books = Buku::factory()->count(3)->create(['rak_id' => $rak->id]);

        $books->each(function ($book) {
            $this->postJson('admin/keranjang', ['buku' => $book->id, 'jumlah' => $book->stok - 1]);
        });

        tap(session('keranjang-pinjam'), function ($session) use ($books) {
            $this->assertCount(count($books), $session['list-buku']);

            $totalBooks = 0;
            foreach ($books as $book) {
                foreach($session['list-buku'] as $list) {
                    if ($list['id'] == $book->id) $totalBooks++;
                }
            }

            $this->assertEquals($totalBooks, count($session['list-buku']));
        });
    }

    /** @test */
    public function invalid_book_handled_correctly()
    {
        $this->signIn();
        // $this->expectException(\Illuminate\Validation\ValidationException::class);

        $response = $this->postJson('admin/keranjang', ['buku' => 1, 'jumlah' => 1]);
        $response->assertUnprocessable();
        $response->assertJson(function(AssertableJson $json) {
            $json
                ->has('message')
                ->has('errors')
                ->etc();
        });
    }

    /** @test */
    public function remove_a_book_from_listing_cart_handled_correctly()
    {
        $this->signIn();

        $rak = Rak::factory()->create();
        $books = Buku::factory()->count(3)->create(['rak_id' => $rak->id]);
        $books->each(function ($book) {
            $this->postJson('admin/keranjang', ['buku' => $book->id, 'jumlah' => 2]);
        });

        $this->deleteJson('admin/keranjang/1')->assertOk();
        $this->deleteJson('admin/keranjang/2')->assertOk();

        tap(session('keranjang-pinjam'), function ($session) {
            $this->assertCount(1, $session['list-buku']);
        });

        $this->deleteJson('admin/keranjang/1')->assertStatus(400);
    }

    /** @test */
    public function it_should_be_increment_amount_of_book_when_listed_book_re_added()
    {
        $this->signIn();

        $rak = Rak::factory()->create();
        $books = Buku::factory()->count(4)->create(['rak_id' => $rak->id]);

        $books->each(function ($book) {
            $this->postJson('admin/keranjang', ['buku' => $book->id, 'jumlah' => 2]);
        });

        $expectedTotal = [10, 20, 15, 2];

        $response = $this->postJson('admin/keranjang', ['buku' => 1, 'jumlah' => $expectedTotal[0]]);
        $response = $this->postJson('admin/keranjang', ['buku' => 2, 'jumlah' => $expectedTotal[1]]);
        $response = $this->postJson('admin/keranjang', ['buku' => 3, 'jumlah' => $expectedTotal[2]]);

        tap(session('keranjang-pinjam'), function ($session) use($expectedTotal) {
            $no = 0;
            foreach ($session['list-buku'] as $buku) {
                $this->assertEquals($expectedTotal[$no++], $buku['jumlah']);
            }
        });
    }

    /** @test */
    public function it_should_can_update_the_book_list_with_batch_data()
    {
        $this->signIn();
        $this->withoutExceptionHandling();

        $rak = Rak::factory()->create();
        $books = Buku::factory()->count(4)->create(['rak_id' => $rak->id]);

        $books->each(function ($book) {
            $this->postJson('admin/keranjang', ['buku' => $book->id, 'jumlah' => 2]);
        });

        $response = $this->put('admin/keranjang', [
            'list_buku' => [
                [ 'buku' => 1,  'jumlah' => 20],
                [ 'buku' => 2,  'jumlah' => 5],
                [ 'buku' => 3,  'jumlah' => 11]
            ]
        ]);

        $response->assertOk();

        tap(session('keranjang-pinjam'), function ($session) use($books) {
            $books = $books->toArray();
            $this->assertCount(4, $session['list-buku']);
            $this->assertEquals($books[0]['id'], $session['list-buku'][$books[0]['kode']]['id']);
            $this->assertEquals(20, $session['list-buku'][$books[0]['kode']]['jumlah']);  
            $this->assertEquals($books[1]['id'], $session['list-buku'][$books[1]['kode']]['id']);
            $this->assertEquals(5, $session['list-buku'][$books[1]['kode']]['jumlah']);  
            $this->assertEquals($books[2]['id'], $session['list-buku'][$books[2]['kode']]['id']);
            $this->assertEquals(11, $session['list-buku'][$books[2]['kode']]['jumlah']);  
            $this->assertEquals($books[3]['id'], $session['list-buku'][$books[3]['kode']]['id']);
            $this->assertEquals(2, $session['list-buku'][$books[3]['kode']]['jumlah']);        
        });
    }
    
}
