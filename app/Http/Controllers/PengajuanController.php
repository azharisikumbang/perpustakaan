<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanRequest;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use App\Services\BookListService;

class PengajuanController extends Controller
{
    public function index(BookListService $service) 
    {
    	$pengaturan = Pengaturan::firstOrCreate();

    	$jumlahBuku = array_reduce($service->getAllBookList()['list-buku'], function($carry, $item) {
    		$carry += $item['jumlah'];
    		return $carry;
    	});

    	return view('pengajuan.index', [ 'pengaturan' => $pengaturan->toArray(), 'jumlah_buku' => $jumlahBuku ]);
    }

    public function store(StorePengajuanRequest $request, BookListService $bookListService)
    {
    	$validated = $request->validated();

    	$peminjam = Anggota::where(['nomor_identitas' => $validated['user']])->first();
    	$bookListService->setPeminjam($peminjam);

    	foreach ($validated['buku_item_total'] as $key => $item) {
    		$bookListService->updateAmout(Buku::make(['kode' => $key]), $item);
    	}

        $listBuku = [];
        $dataPeminjaman = $bookListService->getAllBookList();
        foreach ($dataPeminjaman['list-buku'] as $buku) {
            if ($buku['details']['stok'] < $buku['jumlah']) {
                return redirect()
                    ->route('pengajuan.index')
                    ->with(['status' => 0, 'messages' => 'Buku yang anda pinjam melebihi stok, silahkan periksa kembali..']);
            }

            // update stok
            $newStok = $buku['details']['stok'] - $buku['jumlah'];
            Buku::find($buku['id'])->update(['stok' => $newStok]);
            $listBuku[$buku['id']] = ['jumlah' => $buku['jumlah']];
        }

        $detailPeminjaman = Pengaturan::select(['id', 'lama_pinjaman', 'nominal_denda'])->first();

        $peminjaman = Peminjaman::create([
            'tanggal_peminjaman' => date('Y/m/d H:i:s'),
            'lama_peminjaman' => $detailPeminjaman->lama_pinjaman,
            'tanggal_pengembalian' => date("Y/m/d H:i:s", strtotime("+" . $detailPeminjaman->lama_pinjaman . " days")),
            'nominal_denda' => $detailPeminjaman->nominal_denda,
            'peminjam' => $peminjam['id']
        ]);

        $peminjaman->buku()->attach($listBuku);
        $bookListService->setUpBookListContainer();

        return redirect()
            ->route('peminjaman.index')
            ->with(['status' => 1, 'messages' => 'Peminjaman berhasil diajukan.']);
    }

}
