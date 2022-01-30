<?php

namespace App\Exports;

use App\Services\LaporanPengembalianService;
use App\Utils\LaporanViewHandler;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanPengembalianExport implements FromView
{
	private string $tipe = 'pengembalian';

	private int $limit;

	private int $periode_tahun;

	private int $periode_bulan;

	private int $periode_hari;

	public function __construct(Request $request)
	{
		$this->limit = (int) $request->get('limit', 0);
		$this->periode_tahun = (int) $request->get('periode_tahun', 0);
		$this->periode_bulan = (int) $request->get('periode_bulan', 0);
		$this->periode_hari = (int) $request->get('periode_hari', 0);
	}

	/**
     * @return View
     */
	public function view(): View
	{
		return LaporanViewHandler::generateFromTemplate(new LaporanPengembalianService($this->limit, $this->periode_tahun, $this->periode_bulan, $this->periode_hari), 'laporan.base-nested');
	}
}
