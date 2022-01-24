<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePembayaranRequest;
use App\Models\Peminjaman;
use App\Services\HitungKeterlambatanService;

class PembayaranController extends Controller
{
	public function create(Peminjaman $peminjaman)
    {
		dd($peminjaman);
    }

	public function store(StorePembayaranRequest $request, HitungKeterlambatanService $service) 
	{
		$validated = $request->validated();
		$peminjaman = Peminjaman::where('kode', $validated['kode'])->first();
		$keterlambatan = $service->hitung($peminjaman);

		if ($keterlambatan < 1) {
			return redirect()
                ->route('peminjaman.show', ['peminjaman' => $peminjaman->id])
                ->with(['status' => 0, 'messages' => 'Peminjaman ini tidak memiliki keterlambatan, harap periksa permintaan anda kembali.']);
		}

		$denda = $keterlambatan * $peminjaman->nominal_denda;
		$pembayaran = $peminjaman->pembayaran()->create(['nominal' => $denda]);

		return redirect()
            ->route('peminjaman.show', ['peminjaman' => $peminjaman->id])
            ->with(['status' => 0, 'messages' => sprintf("Pembayaran sebesar Rp. %s berhasil dicatat.", $denda)]);
	}
}
