<?php

namespace App\Exports;

use App\Services\LaporanKeanggotaanService;
use App\Utils\LaporanViewHandler;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanKeanggotaanExport implements FromView
{
	private string $tipe = 'keanggotaan';

	private int $limit;

	private int $periode;

	public function __construct(Request $request)
	{
		$this->limit = (int) $request->get('limit', 0);
		$this->periode = (int) $request->get('periode', 0);
	}

	/**
     * @return View
     */
	public function view(): View
	{
		return LaporanViewHandler::generateFromTemplate(new LaporanKeanggotaanService($this->limit));
	}
}
