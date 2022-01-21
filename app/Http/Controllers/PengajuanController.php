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

    	$jumlahBuku = array_reduce(session('keranjang-pinjam')['list-buku'], function($carry, $item) {
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

    	dd(Peminjaman::make());
   		
    	dd($bookListService->getAllBookList());
    }

}
