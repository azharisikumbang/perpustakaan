<?php

namespace App\Http\Controllers;

use App\Services\PencarianService;
use App\Utils\Paginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PencarianController extends Controller
{
    public function index(Request $request, PencarianService $service)
    {
        $kriteria = $request->has('kriteria') ? $request->get('kriteria') : [];
    	$result = ($request->has('q')) ? $service->cari($request->get('q'), $request->get('kriteria')) : [];

    	return view('pencarian.index', compact('result', 'kriteria'));
    }

    public function show(Request $request)
    {
        $listKriteria = $request->has('kriteria') ? $request->get('kriteria') : [];
        $listKriteria = in_array('all', $listKriteria) ? array_merge($listKriteria, ['peminjaman', 'ddc', 'anggota', 'buku']) : $listKriteria;
        $listKriteria = array_unique($listKriteria);

        foreach ($listKriteria as $kriteria) {
            if (class_exists(sprintf("\App\Models\%s", ucfirst($kriteria)))) {
                $object = ucfirst($kriteria);
                $model = sprintf("App\Models\%s", $object);
                $model = new $model();
                $found = $model->where(['kode' => $request->get('kode')])->first();

                return redirect()
                    ->route(sprintf("%s.show", strtolower($object)), [ strtolower($object) => $found->id ]);
            }
        }
        
        return redirect()->route('dashboard');
    }
}
