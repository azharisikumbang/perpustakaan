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

        if ($jumlahBuku < 1) {
            return redirect()
                ->route('buku.index')
                ->with(['status' => 0, 'messages' => 'Jumlah peminjaman minimal 1 buku, silahkan tambahkan terlebih dahulu..']);
        }

    	return view('pengajuan.index', [ 'pengaturan' => $pengaturan->toArray(), 'jumlah_buku' => $jumlahBuku ]);
    }

    public function store(StorePengajuanRequest $request, BookListService $bookListService)
    {
    	$validated = $request->validated();

    	$peminjam = Anggota::where(['kode' => $validated['user']])->first();
    	$bookListService->setPeminjam($peminjam);

    	foreach ($validated['buku_item_total'] as $key => $item) {
    		$bookListService->updateAmout(Buku::make(['kode' => $key]), $item);
    	}

        $listBuku = [];
        $dataPeminjaman = $bookListService->getAllBookList();
        foreach ($dataPeminjaman['list-buku'] as $buku) {

            if ($buku['details']['stok'] < 1) {
                return redirect()
                    ->route('pengajuan.index')
                    ->with(['status' => 0, 'messages' => 'Buku yang anda pinjam tidak memiliki stok.']);
            }

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
            'nominal_denda' => $detailPeminjaman->nominal_denda,
            'peminjam' => $peminjam['id']
        ]);

        $peminjaman->buku()->attach($listBuku);
        $bookListService->setUpBookListContainer();

        return redirect()
            ->route('peminjaman.show', ['peminjaman' => $peminjaman->id])
            ->with(['status' => 1, 'messages' => 'Peminjaman berhasil dibuat.']);
    }

}
