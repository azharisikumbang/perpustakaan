<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use App\Models\Rak;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PengajuanPeminjamanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function stok_buku_yang_dipinjam_tidak_boleh_kosong()
    {
        $this->signIn();
        $this->withoutExceptionHandling();

        $rak = Rak::factory()->create();
        $books = Buku::factory()->count(3)->create(['rak_id' => $rak->id, 'stok' => 0]);
        $buku_item_total = [];

        foreach ($books->toArray() as $book) {
            $buku_item_total[$book['id']] = 1;
            $this->postJson('admin/keranjang', ['buku' => $book['id'], 'jumlah' => 1]);
        }

        $peminjam = Anggota::factory()->create();
        $listBuku = Buku::all();
        $response = $this->post(route('pengajuan.store'), [
            'user' => $peminjam->nomor_identitas,
            'buku_item_total' => $buku_item_total,
            'buku_total' => 3
        ]);

        $books->each(function ($book) {
            $this->assertDatabaseHas('buku', ['stok' => $book->stok]);
        });

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas(['messages' => 'Buku yang anda pinjam tidak memiliki stok.']);
    }
    
    /** @test */
    public function jumlah_buku_yang_dipinjam_tidak_boleh_lebih_dari_stok()
    {
        $this->signIn();

        $rak = Rak::factory()->create();
        $books = Buku::factory()->count(3)->create(['rak_id' => $rak->id, 'stok' => 3]);
        $buku_item_total = [];

        foreach ($books->toArray() as $book) {
            $buku_item_total[$book['id']] = 5;
            $this->postJson('admin/keranjang', ['buku' => $book['id'], 'jumlah' => 5]);
        }

        $peminjam = Anggota::factory()->create();
        $listBuku = Buku::all();
        $response = $this->post(route('pengajuan.store'), [
            'user' => $peminjam->nomor_identitas,
            'buku_item_total' => $buku_item_total,
            'buku_total' => 3
        ]);

        $books->each(function ($book) {
            $this->assertDatabaseHas('buku', ['stok' => $book->stok]);
        });

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas(['messages' => 'Buku yang anda pinjam melebihi stok, silahkan periksa kembali..']);
    }
    
    /** @test */
    public function stok_buku_harus_berkurang_saat_dipinjam()
    {
        $this->signIn();

        $pengaturan = Pengaturan::factory()->create();
        $rak = Rak::factory()->create();
        $books = Buku::factory()->count(3)->create(['rak_id' => $rak->id, 'stok' => 10]);
        $buku_item_total = [];

        foreach ($books->toArray() as $book) {
            $stokPinjam = rand(1, 10);
            $buku_item_total[$book['id']] = $stokPinjam;
            $this->postJson('admin/keranjang', ['buku' => $book['id'], 'jumlah' => $stokPinjam]);
        }

        $peminjam = Anggota::factory()->create();
        $listBuku = Buku::all();
        $response = $this->post(route('pengajuan.store'), [
            'user' => $peminjam->nomor_identitas,
            'buku_item_total' => $buku_item_total,
            'buku_total' => 3
        ]);

        $books->each(function ($book) use ($buku_item_total) {
            $this->assertDatabaseHas('buku', ['stok' => $book->stok - $buku_item_total[$book->id]]);
        });
    }

    /** @test */
    public function peminjaman_tercatat_secara_betul()
    {
        $this->signIn();

        $pengaturan = Pengaturan::factory()->create();
        $rak = Rak::factory()->create();
        $books = Buku::factory()->count(3)->create(['rak_id' => $rak->id, 'stok' => 10]);
        $buku_item_total = [];

        foreach ($books->toArray() as $book) {
            $stokPinjam = rand(1, 10);
            $buku_item_total[$book['id']] = $stokPinjam;
            $this->postJson('admin/keranjang', ['buku' => $book['id'], 'jumlah' => $stokPinjam]);
        }

        $peminjam = Anggota::factory()->create();
        $listBuku = Buku::all();
        $response = $this->post(route('pengajuan.store'), [
            'user' => $peminjam->nomor_identitas,
            'buku_item_total' => $buku_item_total,
            'buku_total' => 3
        ]);

        $this->assertDatabaseHas('peminjaman', [
            'kode' => sprintf("%s/PINJAM/%s", date('Y/m'), str_pad(1, 6, '0', STR_PAD_LEFT)),
            'lama_peminjaman' => $pengaturan->lama_pinjaman,
            'tanggal_pengembalian' => null,
            'nominal_denda' => $pengaturan->nominal_denda,
            'peminjam' => $peminjam->id
        ]);

        $books->each(function ($book) use ($buku_item_total) {
            $this->assertDatabaseHas('peminjaman_buku', [
                'peminjaman_id' => 1,
                'buku_id' => $book->id,
                'jumlah' => $buku_item_total[$book->id]
            ]);
        });

        $response->assertRedirect(route('peminjaman.show', ['peminjaman' => 1]));
        $response->assertSessionHas(['messages' => 'Peminjaman berhasil dibuat.']);
    }
    
    
}
