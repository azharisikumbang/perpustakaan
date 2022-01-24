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

class PembayaranDendaTest extends TestCase
{
	use RefreshDatabase;

	private function createPeminjaman() : Peminjaman
	{
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
        $this->post(route('pengajuan.store'), [
            'user' => $peminjam->nomor_identitas,
            'buku_item_total' => $buku_item_total,
            'buku_total' => 3
        ]);

        return Peminjaman::first();
	}

    /** @test */
    public function pengembalian_yang_tidak_memiliki_keterlambatan_harus_tidak_punya_denda()
    {
    	$this->signIn();
    	$this->withoutExceptionHandling();
        $peminjaman = $this->createPeminjaman();
        $peminjaman->update(['lama_peminjaman' => 7, 'tanggal_peminjaman' => date('Y-m-d H:i:s')]);
        	
        // ajukan pengembalian
        $this->put(route('peminjaman.update', ['peminjaman' => $peminjaman->id]));

        $requestData = ['nominal' => 10000, 'kode' => $peminjaman->kode];
        $response = $this->post(route('pembayaran.store'), $requestData);

        $response->assertRedirect(route('peminjaman.show', ['peminjaman' => $peminjaman->id]));
        $response->assertSessionHas('status', 0);
        $response->assertSessionHas('messages', 'Peminjaman ini tidak memiliki keterlambatan, harap periksa permintaan anda kembali.');

        $this->assertDatabaseCount('pembayaran', 0);

    }

    /** @test */
    public function nominal_pembayaran_harus_sesuai_dengan_nominal_dan_hari_keterlambatan()
    {
    	$this->signIn();
        $peminjaman = $this->createPeminjaman();
        $peminjaman->update(['lama_peminjaman' => 7, 'tanggal_peminjaman' => date('2022-01-01 H:i:s')]);

        // ajukan pengembalian
        $this->put(route('peminjaman.update', ['peminjaman' => $peminjaman->id]));

        $hitungKeterlambatanService = new HitungKeterlambatanService();
        $keterlambatan = $hitungKeterlambatanService->hitung($peminjaman);
        $denda = $peminjaman->nominal_denda * $keterlambatan;

        $requestData = ['nominal' => $denda, 'kode' => $peminjaman->kode];
        $response = $this->post(route('pembayaran.store'), $requestData);

        $this->assertDatabaseCount('pembayaran', 1);
        $this->assertDatabaseHas('pembayaran', [
			'nominal' => $denda,
			'peminjaman_id' => $peminjaman->id
        ]);

        $response->assertSessionHas('messages', sprintf("Pembayaran sebesar Rp. %s berhasil dicatat.", $denda));
    }    
}
