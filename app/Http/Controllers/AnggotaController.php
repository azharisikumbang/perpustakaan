<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnggotaRequest;
use App\Http\Requests\UpdateAnggotaRequest;
use App\Models\Anggota;
use App\Utils\Paginator;

class AnggotaController extends Controller
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
        $paginated = Anggota::when(isset($httpRequestAttributes['cari']), function($query) use ($httpRequestAttributes) {
                $term = $httpRequestAttributes['cari'];
                return $query->where('nama', 'LIKE', "%{$term}%")
                    ->orWhere('nomor_identitas', 'LIKE', "%{$term}%");
            })
            ->when(
            isset($httpRequestAttributes['order_by']),
            Paginator::paginateByOrderAttribute($orderBy, $orderAs
        ))->paginate($perPage);

        // @TODO : remove query string for next and previous links if not available
        $paginated->appends(['limit' => $perPage, 'order_by' => $orderBy, 'order_as' => $orderAs]);

        return view('anggota.index', $paginated->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('anggota.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAnggotaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAnggotaRequest $request)
    {
        Anggota::create($request->validated());

        return redirect()
            ->route('anggota.index')
            ->with(['status' => 1, 'messages' => 'Data berhasil disimpan.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Anggota $anggotum
     * @return \Illuminate\Http\Response
     */
    public function show(Anggota $anggotum)
    {
        return view('anggota.show', ['anggota' => $anggotum->attributesToArray()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Anggota $anggotum
     * @return \Illuminate\Http\Response
     */
    public function edit(Anggota $anggotum)
    {
        return view('anggota.edit', $anggotum->attributesToArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAnggotaRequest  $request
     * @param  \App\Models\Anggota $anggotum
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAnggotaRequest $request, Anggota $anggotum)
    {
        $anggotum->update($request->validated());

        return redirect()
            ->route('anggota.edit', ['anggota' => $anggotum->id])
            ->with(['status' => 1, 'messages' => 'Data berhasil diperbaharui.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Anggota $anggotum
     * @return \Illuminate\Http\Response
     */
    public function destroy(Anggota $anggotum)
    {
         $isDeleted = $anggotum->delete();

        if (!$isDeleted) {
            return response()->json([
                'status' => 'fail',
                'code' => 500,
                'messages' => 'Internal Server Error.'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'messages' => 'Resource Deleted.'
        ]);
    }
}
