<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnggotaRequest;
use App\Http\Requests\UpdateAnggotaRequest;
use App\Models\Anggota;
use App\Utils\Paginator;

class KeanggotaanController extends Controller
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
        $paginated = Anggota::when(
            isset($httpRequestAttributes['order_by']),
            Paginator::paginateByOrderAttribute($orderBy, $orderAs
        ))->paginate($perPage);

        // @TODO : remove query string for next and previous links if not available
        $paginated->appends(['limit' => $perPage, 'order_by' => $orderBy, 'order_as' => $orderAs]);

        return view('keanggotaan.index', $paginated->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('keanggotaan.create');
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
            ->route('keanggotaan.index')
            ->with(['status' => 1, 'messages' => 'Data berhasil disimpan.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Anggota  $keanggotaan
     * @return \Illuminate\Http\Response
     */
    public function show(Anggota $keanggotaan)
    {
        return view('keanggotaan.show', ['keanggotaan' => $keanggotaan->attributesToArray()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Anggota  $keanggotaan
     * @return \Illuminate\Http\Response
     */
    public function edit(Anggota $keanggotaan)
    {
        return view('keanggotaan.edit', $keanggotaan->attributesToArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAnggotaRequest  $request
     * @param  \App\Models\Anggota  $keanggotaan
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAnggotaRequest $request, Anggota $keanggotaan)
    {
        $keanggotaan->update($request->validated());

        return redirect()
            ->route('keanggotaan.edit', ['keanggotaan' => $keanggotaan->id])
            ->with(['status' => 1, 'messages' => 'Data berhasil diperbaharui.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Anggota  $keanggotaan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Anggota $keanggotaan)
    {
         $isDeleted = $keanggotaan->delete();

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
