<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePembayaranRequest;
use App\Models\Peminjaman;
use App\Services\HitungKeterlambatanService;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
	public function create(Peminjaman $peminjaman, HitungKeterlambatanService $service)
    {
    	if (!is_null($peminjaman->tanggal_pengembalian)) {
    		return redirect()
                ->route('peminjaman.show', ['peminjaman' => $peminjaman->id])
                ->with(['status' => 0, 'messages' => 'Peminjaman ini telah tercatat dikembalikan, mohon periksa kembali.']);
    	}

    	$keterlambatan = $service->hitung($peminjaman);

		return view('pembayaran.create', array_merge(
			$peminjaman->toArray(), ['keterlambatan' => [ 'hari' => $keterlambatan]]
		));
    }

	public function store(StorePembayaranRequest $request, HitungKeterlambatanService $service) 
	{
		$validated = $request->validated();
		$peminjaman = Peminjaman::where('kode', $validated['kode'])->first();

		if (!is_null($peminjaman->tanggal_pengembalian)) {
            return redirect()
                ->route('peminjaman.show', ['peminjaman' => $peminjaman->id])
                ->with(['status' => 0, 'messages' => 'Peminjaman ini telah tercatat dikembalikan, mohon periksa kembali.']);
        }

		$keterlambatan = $service->hitung($peminjaman);

		if ($keterlambatan < 1) {
			return redirect()
                ->route('peminjaman.show', ['peminjaman' => $peminjaman->id])
                ->with(['status' => 0, 'messages' => 'Peminjaman ini tidak memiliki keterlambatan, harap periksa permintaan anda kembali.']);
		}

		$denda = $keterlambatan * $peminjaman->nominal_denda;
		if ($validated['nominal'] != $denda) {
			return redirect()
                ->back()
                ->withInput()
                ->with(['status' => 0, 'messages' => 'Pembayaran tidak sesuai dengan nominal seharusnya, mohon dicek kembali.']);
		}

		$pembayaran = $peminjaman->pembayaran()->create(['nominal' => $denda]);

		// @TODO: refactoring this to service
        DB::transaction(function() use ($peminjaman) {
            $peminjaman->update(['tanggal_pengembalian' => date('Y-m-d H:i:s')]);
            $peminjaman->buku->each(function($buku) use ($peminjaman) {
                $buku->update(['stok' => $buku->stok + $buku->pivot->jumlah]);
            });
        });

		return redirect()
            ->route('peminjaman.show', ['peminjaman' => $peminjaman->id])
            ->with(['status' => 1, 'messages' => sprintf("Pembayaran sebesar Rp. %s berhasil dicatat.", $denda)]);
	}
}
