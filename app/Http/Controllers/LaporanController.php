<?php

namespace App\Http\Controllers;

use App\Exports\LaporanDataBukuExport;
use App\Exports\LaporanKeanggotaanExport;
use App\Exports\LaporanPembayaranDendaExport;
use App\Exports\LaporanPeminjamanExport;
use App\Exports\LaporanPengembalianExport;
use App\Utils\LaporanExcelDownloader;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;

class LaporanController extends Controller
{
    public function index()
    {
    	return view('laporan.index');
    }

    public function generate(Request $request)
    {
    	return LaporanExcelDownloader::fromRequest($request);
    }
}
