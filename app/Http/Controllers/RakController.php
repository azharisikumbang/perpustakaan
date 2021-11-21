<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRakRequest;
use App\Http\Requests\UpdateRakRequest;
use App\Models\Rak;
use App\Utils\Paginator;

class RakController extends Controller
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
        $listRak = Rak::when(
            isset($httpRequestAttributes['order_by']),
            Paginator::paginateByOrderAttribute(
                $orderBy, 
                $orderAs)
            )->paginate($perPage);

        $listRak->appends(['limit' => $perPage, 'order_by' => $orderBy, 'order_as' => $orderAs]);

        return view('rak.index', $listRak->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rak.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRakRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRakRequest $request)
    {
        Rak::create($request->validated());

        return redirect()
            ->route('rak.index')
            ->with(['status' => 1, 'messages' => 'Data berhasil disimpan.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rak  $rak
     * @return \Illuminate\Http\Response
     */
    public function show(Rak $rak)
    {
        return view('rak.show', ['rak' => $rak->attributesToArray()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rak  $rak
     * @return \Illuminate\Http\Response
     */
    public function edit(Rak $rak)
    {
        return view('rak.edit', ['rak' => $rak->attributesToArray()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRakRequest  $request
     * @param  \App\Models\Rak  $rak
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRakRequest $request, Rak $rak)
    {
        $rak->update($request->validated());

        return redirect()
            ->route('rak.edit', ['rak' => $rak->id])
            ->with(['status' => 1, 'messages' => 'Data berhasil diperbaharui.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rak  $rak
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rak $rak)
    {
        $isDeleted = $rak->delete();

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
