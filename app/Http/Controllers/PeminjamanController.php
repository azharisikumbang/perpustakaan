<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeminjamanRequest;
use App\Http\Requests\UpdatePeminjamanRequest;
use App\Models\Peminjaman;
use App\Services\HitungKeterlambatanService;
use App\Utils\Paginator;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $httpRequestAttributes = request()->toArray();
        $perPage = $httpRequestAttributes['limit'] ?? Paginator::OFFSET;
        $orderBy = $httpRequestAttributes['order_by'] ?? 'id';
        $orderAs = $httpRequestAttributes['order_as'] ?? null;
        $listRak = Peminjaman::with('peminjam')
            ->select('id', 'kode', 'tanggal_peminjaman', 'lama_peminjaman', 'peminjam')
            ->when(
                isset($httpRequestAttributes['order_by']),
                Paginator::paginateByOrderAttribute($orderBy, $orderAs)
            )->paginate($perPage);

        $listRak->appends(['limit' => $perPage, 'order_by' => $orderBy, 'order_as' => $orderAs]);

        return view('peminjaman.index', $listRak->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('peminjaman.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePeminjamanRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePeminjamanRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Peminjaman  $peminjaman
     * @return \Illuminate\Http\Response
     */
    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['peminjam', 'buku.rak']);
        $peminjaman = $peminjaman->toArray();
        $sekarang = is_null($peminjaman['tanggal_pengembalian']) ? new \DateTime() : new \DateTime($peminjaman['tanggal_pengembalian']);
        $batas_pengembalian = new \DateTime($peminjaman['tanggal_peminjaman']);
        $batas_pengembalian->modify("+{$peminjaman['lama_peminjaman']} days");
        $perbedaan = $sekarang->diff($batas_pengembalian);
        $keterlambatan = [
            'batas_pengembalian' => $batas_pengembalian->format('Y-m-d H:i:s'),
            'terlambat' => $perbedaan->invert, 
            'hari' => ($perbedaan->h > 0) ? $perbedaan->days + 1 : $perbedaan->days
        ];

        $peminjaman['keterlambatan'] = $keterlambatan;

        return view('peminjaman.show', $peminjaman);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Peminjaman  $peminjaman
     * @return \Illuminate\Http\Response
     */
    public function edit(Peminjaman $peminjaman)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Peminjaman  $peminjaman
     * @return \Illuminate\Http\Response
     */
    public function update(Peminjaman $peminjaman, HitungKeterlambatanService $service)
    {
        if (!is_null($peminjaman->tanggal_pengembalian)) {
            return redirect()
                ->route('peminjaman.show', ['peminjaman' => $peminjaman->id])
                ->with(['status' => 0, 'messages' => 'Peminjaman ini telah tercatat dikembalikan, mohon periksa kembali.']);
        }

        $hariKeterlambatan = $service->hitung($peminjaman, new \DateTime());
        if ($hariKeterlambatan > 0 && is_null($peminjaman->tanggal_pengembalian)) {
            return redirect()
                ->route('pembayaran.create', ['peminjaman' => $peminjaman->id])
                ->with(['status' => 0, 'messages' => 'Peminjaman telah terlambat, silahkan lakukan pembayaran denda terlebih dahulu.']);
        }

        DB::transaction(function() use ($peminjaman) {
            $peminjaman->update(['tanggal_pengembalian' => date('Y-m-d H:i:s')]);
            $peminjaman->buku->each(function($buku) use ($peminjaman) {
                $buku->update(['stok' => $buku->stok + $buku->pivot->jumlah]);
            });
        });

        return redirect()
            ->route('peminjaman.show', ['peminjaman' => $peminjaman->id])
            ->with(['status' => 1, 'messages' => 'Berhasil menyimpan catatan pengembalian.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Peminjaman  $peminjaman
     * @return \Illuminate\Http\Response
     */
    public function destroy(Peminjaman $peminjaman)
    {
        //
    }
}
