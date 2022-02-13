<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Peminjaman;
use App\Services\HitungKeterlambatanService;
use App\Utils\Paginator;
use Illuminate\Http\Request;

class RiwayatPeminjamanController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $httpRequestAttributes = request()->toArray();
        $perPage = $httpRequestAttributes['limit'] ?? Paginator::OFFSET;
        $orderBy = $httpRequestAttributes['order_by'] ?? 'id';
        $orderAs = $httpRequestAttributes['order_as'] ?? null;
        $listPeminjaman = Peminjaman::select('id', 'kode', 'tanggal_peminjaman', 'lama_peminjaman', 'tanggal_pengembalian')
            ->when(isset($httpRequestAttributes['cari']), function($query) use($httpRequestAttributes) {
                $term = $httpRequestAttributes['cari'];
                return $query->where('kode', 'LIKE', "%{$term}%");
            })
            ->when(
                isset($httpRequestAttributes['order_by']),
                Paginator::paginateByOrderAttribute($orderBy, $orderAs)
            )
            ->where('peminjam', $user->anggota->id)
            ->paginate($perPage);

        $listPeminjaman->appends(['limit' => $perPage, 'order_by' => $orderBy, 'order_as' => $orderAs]);

        return view('riwayat-peminjaman.index', $listPeminjaman->toArray());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Peminjaman  $peminjaman
     * @return \Illuminate\Http\Response
     */
    public function show(Peminjaman $peminjaman, HitungKeterlambatanService $service)
    {
        // @TODO : iff peminjaman adalah milik anggota terautentikasi
        $user = auth()->user();
        // if ($user) {
             
        // }

        $peminjaman->load(['peminjam', 'buku.rak']);
        $hariKeterlambatan = $service->hitung($peminjaman);
        $keterlambatan = [
            'hari' => $hariKeterlambatan,
            'batas_pengembalian' => date("Y-m-d H:i:s", strtotime(sprintf("+%s days", $peminjaman->lama_peminjaman)))
        ];

        return view('riwayat-peminjaman.show', array_merge(
            $peminjaman->toArray(), 
            ['keterlambatan' => $keterlambatan]
        ));
    }
}
