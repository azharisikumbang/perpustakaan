<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeminjamanRequest;
use App\Http\Requests\UpdatePeminjamanRequest;
use App\Models\Peminjaman;
use App\Services\BookListService;
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
            ->when(isset($httpRequestAttributes['cari']), function($query) use($httpRequestAttributes) {
                $term = $httpRequestAttributes['cari'];
                return $query->where('kode', 'LIKE', "%{$term}%");
            })
            ->when(
                isset($httpRequestAttributes['order_by']),
                Paginator::paginateByOrderAttribute($orderBy, $orderAs)
            )
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        $listRak->appends(['limit' => $perPage, 'order_by' => $orderBy, 'order_as' => $orderAs]);

        return view('peminjaman.index', $listRak->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(BookListService $service)
    {
        $jumlahBuku = array_reduce($service->getAllBookList()['list-buku'], function($carry, $item) {
            $carry += $item['jumlah'];
            return $carry;
        });

        if ($jumlahBuku < 1) {
            return redirect()
                ->route('buku.index')
                ->with(['status' => 0, 'messages' => 'Silahkan dipilih buku yang akan dipinjam terlebih dahulu.']);
        }

        return redirect()
            ->route('pengajuan.index');
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
    public function show(Peminjaman $peminjaman, HitungKeterlambatanService $service)
    {
        $peminjaman->load(['peminjam', 'buku.rak']);
        $hariKeterlambatan = $service->hitung($peminjaman);
        $keterlambatan = [
            'hari' => $hariKeterlambatan,
            'batas_pengembalian' => date("Y-m-d H:i:s", strtotime(sprintf("+%s days", $peminjaman->lama_peminjaman)))
        ];

        return view('peminjaman.show', array_merge(
            $peminjaman->toArray(), 
            ['keterlambatan' => $keterlambatan]
        ));
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
