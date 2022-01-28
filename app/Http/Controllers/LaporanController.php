<?php

namespace App\Http\Controllers;

use App\Exports\LaporanKeanggotaanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index()
    {
    	return view('laporan.index');
    }

    public function generate(Request $request)
    {
    	return Excel::download(new LaporanKeanggotaanExport($request), sprintf('laporan-keanggotaan-%s.xlsx', time()));
    }
}
