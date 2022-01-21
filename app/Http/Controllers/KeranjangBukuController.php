<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBokuToKeranjangRequest;
use App\Http\Requests\UpdateBukuToKeranjangRequest;
use App\Models\Buku;
use App\Services\BookListService;

class KeranjangBukuController extends Controller
{
    // @TODO : use form request for validating data
    public function store(StoreBokuToKeranjangRequest $request, BookListService $bookListService)
    {
        $validated = $request->validated();
    	$buku = Buku::with('rak')->findOrFail($validated['buku']);
    	$bookListService->storeBookToList($buku, $validated['jumlah']);

 		return response()->json([
            'status' => 200, 
            'messages' => 'Buku berhasil disimpan dikeranjang.',
            'data' => $bookListService->get($buku)
        ]);
    }

    public function remove(Buku $buku, BookListService $bookListService) 
    {
        $isRemoved = $bookListService->removeBookFromList($buku);

        if (!$isRemoved) {
            // @TODO : handle response if not removed
            return response()->json([
                'status' => 400, 
                'messages' => 'Gagal menghapus buku dari list keranjang.'
            ], 400);
        }

        return response()->json([
            'status' => 200, 
            'messages' => 'Buku berhasil dihapus dikeranjang.'
        ]);
    }

    public function update(UpdateBukuToKeranjangRequest $request, BookListService $bookListService) 
    {
        $validated = $request->validated();

        foreach ($validated['list_buku'] as $data) {
            $buku = Buku::with('rak')->findOrFail($data['buku']);
            $bookListService->storeBookToList($buku, $data['jumlah']);
        }

        return response()->json([
            'status' => 200, 
            'messages' => 'Data keranjang berhasil diperbaharui.'
        ]);
    }
}
