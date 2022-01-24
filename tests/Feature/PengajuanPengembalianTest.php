<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use App\Models\Rak;
use App\Services\HitungKeterlambatanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PengajuanPengembalianTest extends TestCase
{

	use RefreshDatabase;

    /** @test */
    public function stok_buku_yang_dikembalikan_harus_bertambah()
    {
        $this->signIn();
        $this->withoutExceptionHandling();

        $pengaturan = Pengaturan::factory()->create();
        $rak = Rak::factory()->create();
        $books = Buku::factory()->count(3)->create(['rak_id' => $rak->id, 'stok' => 100]);
        $buku_item_total = [];

        foreach ($books->toArray() as $book) {
            $stokPinjam = rand(1, 100); // 20
            $buku_item_total[$book['id']] = $stokPinjam;
            $this->postJson('admin/keranjang', ['buku' => $book['id'], 'jumlah' => $stokPinjam]);
        }

        $peminjam = Anggota::factory()->create();
        $listBuku = Buku::all();
        $this->post(route('pengajuan.store'), [
            'user' => $peminjam->nomor_identitas,
            'buku_item_total' => $buku_item_total,
            'buku_total' => 3
        ]);

        $peminjaman = Peminjaman::first();
        // diskenariokan tidak terlambat
        $peminjaman->update(['lama_peminjaman' => 100, 'tanggal_peminjaman' => date('Y-m-10 H:i:s')]);
        $books = Buku::all();
        $response = $this->put(route('peminjaman.update', ['peminjaman' => $peminjaman->id]));
        $books->each(function ($book) use ($buku_item_total) {
            $this->assertDatabaseHas('buku', ['stok' => $book->stok + $buku_item_total[$book->id]]);
        });

        $peminjaman = $peminjaman->fresh();
        $this->assertNotNull($peminjaman->tanggal_pengembalian);
    }

    /** @test */
    public function pengembalian_hanya_untuk_peminjaman_yang_belum_dikembalikan()
    {
    	$this->signIn();

        $pengaturan = Pengaturan::factory()->create();
        $rak = Rak::factory()->create();
        $books = Buku::factory()->count(3)->create(['rak_id' => $rak->id, 'stok' => 100]);
        $buku_item_total = [];

        foreach ($books->toArray() as $book) {
            $stokPinjam = rand(1, 100); // 20
            $buku_item_total[$book['id']] = $stokPinjam;
            $this->postJson('admin/keranjang', ['buku' => $book['id'], 'jumlah' => $stokPinjam]);
        }

        $peminjam = Anggota::factory()->create();
        $listBuku = Buku::all();
        $this->post(route('pengajuan.store'), [
            'user' => $peminjam->nomor_identitas,
            'buku_item_total' => $buku_item_total,
            'buku_total' => 3
        ]);

        $peminjaman = Peminjaman::first();
        $peminjaman->update(['tanggal_pengembalian' => date('Y-m-d H:i:s')]);
        $peminjaman = $peminjaman->fresh();

        $this->assertNotNull($peminjaman->tanggal_pengembalian);
        $response = $this->put(route('peminjaman.update', ['peminjaman' => $peminjaman->id]));
        $peminjaman = $peminjaman->fresh();
        $this->assertNotNull($peminjaman->tanggal_pengembalian);

        $response->assertRedirect(route('peminjaman.show', ['peminjaman' => $peminjaman->id]));
    }

    /** @test */
    public function pengembalian_yang_memiliki_keterlambatan_harus_melakukan_pembayaran_terlebih_dahulu()
    {
    	$this->signIn();

        $pengaturan = Pengaturan::factory()->create();
        $rak = Rak::factory()->create();
        $books = Buku::factory()->count(3)->create(['rak_id' => $rak->id, 'stok' => 100]);
        $buku_item_total = [];

        foreach ($books->toArray() as $book) {
            $stokPinjam = rand(1, 100); // 20
            $buku_item_total[$book['id']] = $stokPinjam;
            $this->postJson('admin/keranjang', ['buku' => $book['id'], 'jumlah' => $stokPinjam]);
        }

        $peminjam = Anggota::factory()->create();
        $listBuku = Buku::all();
        $this->post(route('pengajuan.store'), [
            'user' => $peminjam->nomor_identitas,
            'buku_item_total' => $buku_item_total,
            'buku_total' => 3
        ]);

        $peminjaman = Peminjaman::first();
        // diskenarionkan terlambat
        $peminjaman->update(['lama_peminjaman' => 1, 'tanggal_peminjaman' => date('2022-01-01 H:i:s')]);
        $response = $this->put(route('peminjaman.update', ['peminjaman' => $peminjaman->id]));
        $response->assertRedirect(route('pembayaran.create', ['peminjaman' => $peminjaman->id]));
    } 
}
