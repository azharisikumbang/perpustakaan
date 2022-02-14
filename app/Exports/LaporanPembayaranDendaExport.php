<?php

namespace App\Exports;

use App\Services\LaporanPembayaranDendaService;
use App\Utils\LaporanViewHandler;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPembayaranDendaExport implements FromView, WithDrawings, ShouldAutoSize, WithStyles
{
	private string $tipe = 'pembayaran-denda';

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
		return LaporanViewHandler::generateFromTemplate(new LaporanPembayaranDendaService($this->limit, $this->periode_tahun, $this->periode_bulan, $this->periode_hari), 'laporan.base-nested');
	}

	public function styles(Worksheet $sheet)
	{
		return [
			1 => ['font' => [ 'bold' => true, 'size' => 16 ]],
			2 => ['font' => [ 'size' => 12 ]],
			4 => ['font' => [ 'bold' => true, 'size' => 13 ]],
			7 => ['font' => [ 'bold' => true, 'size' => 13 ]],
		];
	}

	public function drawings() 
	{
		return $this->logo;
	}

	public function setLogo(BaseDrawing $drawing) : void
	{
		$this->logo = $drawing;
	}
}
