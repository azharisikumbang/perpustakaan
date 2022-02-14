<?php

namespace App\Http\Controllers;

use App\Exports\LaporanKeanggotaanExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanKeanggotaanController extends Controller
{
    public function __invoke(Request $request)
    {
    	return view('laporan.base-nested', ['handler' => false]);
    }
}
