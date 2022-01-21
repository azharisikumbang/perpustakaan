<?php

namespace App\Http\Controllers;

use App\Http\Requests\CekAnggotaRequest;
use App\Models\Anggota;
use Illuminate\Http\Request;

class CekAnggotaController extends Controller
{
    public function __invoke(CekAnggotaRequest $request) 
    {
    	$anggota = Anggota::where($request->validated())->first();

    	return response()->json([
    		'message' => 'Berhasil mendapatkan data anggota.',
    		'code' => 200,
    		'data' => $anggota->toArray()
    	]);
    }
}
