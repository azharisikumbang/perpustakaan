<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDDCRequest;
use App\Http\Requests\UpdateDDCRequest;
use App\Models\DDC;
use App\Models\Role;
use App\Utils\Paginator;
use Illuminate\Http\Request;

class DDCController extends Controller
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
        $paginated = DDC::when(isset($httpRequestAttributes['cari']), function($query) use($httpRequestAttributes) {
                $term = $httpRequestAttributes['cari'];
                return $query->where('kode', 'LIKE', "%{$term}%")
                    ->orWhere('klasifikasi', 'LIKE', "%{$term}%");
            })
            ->when(
                isset($httpRequestAttributes['order_by']),
                Paginator::paginateByOrderAttribute($orderBy, $orderAs
            ))
            ->orderBy('kode', 'asc')
            ->paginate($perPage);

        // @TODO : remove query string for next and previous links if not available
        $paginated->appends(['limit' => $perPage, 'order_by' => $orderBy, 'order_as' => $orderAs]);

        return view('ddc.index', array_merge(
            $paginated->toArray(), 
            [ 'is_administrator' => auth()->user()->hasRole(Role::ADMINISTRATOR) ]
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('ddc.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDDCRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDDCRequest $request)
    {
        $validated = $request->validated();

        DDC::create($validated);

        return redirect()
            ->route('ddc.index')
            ->with(['status' => 1, 'messages' => 'Data berhasil disimpan.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DDC  $ddc
     * @return \Illuminate\Http\Response
     */
    public function show(DDC $ddc, Request $request)
    {   
        $listBuku = $ddc->buku()->paginate($request->get('limit', 10));

        return view('ddc.show', [
            'ddc' => $ddc->toArray(), 
            'list_buku' => $listBuku->toArray(),
            'is_administrator' => auth()->user()->hasRole(Role::ADMINISTRATOR)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DDC  $ddc
     * @return \Illuminate\Http\Response
     */
    public function edit(DDC $ddc)
    {
        return view('ddc.edit', $ddc->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDDCRequest  $request
     * @param  \App\Models\DDC  $ddc
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDDCRequest $request, DDC $ddc)
    {
        $ddc->update($request->validated());

        return redirect()
            ->route('ddc.edit', ['ddc' => $ddc->id])
            ->with(['status' => 1, 'messages' => 'Data berhasil diperbaharui.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DDC  $ddc
     * @return \Illuminate\Http\Response
     */
    public function destroy(DDC $ddc)
    {
        $isDeleted = $ddc->delete();

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
