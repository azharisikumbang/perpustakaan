<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use App\Models\Buku;
use App\Models\Rak;
use App\Utils\Paginator;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
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
        $paginated = Buku::with('rak')
            ->when(isset($httpRequestAttributes['cari']), function($query) use($httpRequestAttributes) {
                $term = $httpRequestAttributes['cari'];
                return $query->where('kode', 'LIKE', "%{$term}%")
                    ->orWhere('isbn', 'LIKE', "%{$term}%")
                    ->orWhere('judul', 'LIKE', "%{$term}%");
            })
            ->when(
                isset($httpRequestAttributes['order_by']),
                Paginator::paginateByOrderAttribute($orderBy, $orderAs
            ))->paginate($perPage);

        // @TODO : remove query string for next and previous links if not available
        $paginated->appends(['limit' => $perPage, 'order_by' => $orderBy, 'order_as' => $orderAs]);

        return view('buku.index', $paginated->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $listRak = Rak::all()->toArray();

        if (count($listRak) < 1) {
            return redirect()
                ->route('rak.create')
                ->with(['status' => 0, 'messages' => 'Anda perlu menambahkan data rak terlebih dahulu.']);
        }

        return view('buku.create', ['listRak' => $listRak]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBukuRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBukuRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('sampul')) {
            $file = $request->file('sampul');
            $filename = $file->hashName();
            $file->storePubliclyAs('images/sampul', $filename);

            $validated['sampul'] = $filename;
        }

        Buku::create($validated);

        return redirect()
            ->route('buku.index')
            ->with(['status' => 1, 'messages' => 'Data berhasil disimpan.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Buku  $buku
     * @return \Illuminate\Http\Response
     */
    public function show(Buku $buku)
    {
        return view('buku.show', array_merge(
            $buku->toArray(), ['rak' => $buku->rak->toArray()]
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Buku  $buku
     * @return \Illuminate\Http\Response
     */
    public function edit(Buku $buku)
    {
        return view('buku.edit', [
            'listRak' => Rak::all()->toArray(),
            'buku' => $buku->toArray()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBukuRequest  $request
     * @param  \App\Models\Buku  $buku
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBukuRequest $request, Buku $buku)
    {
        $validated = $request->validated();

        if ($request->hasFile('sampul')) {

            if ($buku->sampul) {
                Storage::delete("images/sampul/{$buku->sampul}");
            }

            $file = $request->file('sampul');
            $filename = $file->hashName();
            $file->storePubliclyAs('images/sampul', $filename);

            $validated['sampul'] = $filename;
        }

        $buku->update($validated);

        return redirect()
            ->route('buku.edit', ['buku' => $buku->id])
            ->with(['status' => 1, 'messages' => 'Data berhasil diperbaharui.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Buku  $buku
     * @return \Illuminate\Http\Response
     */
    public function destroy(Buku $buku)
    {
        $sampul = $buku->sampul;
        $isDeleted = $buku->delete();

        if (!$isDeleted) {
            return response()->json([
                'status' => 'fail',
                'code' => 500,
                'messages' => 'Internal Server Error.'
            ], 500);
        }

        Storage::delete("images/sampul/{$sampul}");

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'messages' => 'Resource Deleted.'
        ]);
    }
}
