<?php 

namespace App\Utils;

use App\Exports\LaporanDataBukuExport;
use App\Exports\LaporanKeanggotaanExport;
use App\Exports\LaporanPembayaranDendaExport;
use App\Exports\LaporanPeminjamanExport;
use App\Exports\LaporanPengembalianExport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanExcelDownloader
{
	public static function fromRequest(Request $request, string $filename = null) : BinaryFileResponse
	{	
		$tipe = $request->get('tipe', false);

		switch ($tipe) {
			case 'data-buku':
				$exporter = new LaporanDataBukuExport($request);
				break;
			case 'keanggotaan':
				$exporter = new LaporanKeanggotaanExport($request);
				break;
			case 'pembayaran-denda':
				$exporter = new LaporanPembayaranDendaExport($request);
				break;
			case 'peminjaman':
				$exporter = new LaporanPeminjamanExport($request);
				break;
			case 'pengembalian':
				$exporter = new LaporanPengembalianExport($request);
				break;
			
			default:
				$tipe = false;
				break;
		}

		if (!$tipe) {
			throw new Exception('tipe yang anda maksud tidak diketahui');
			abort(404);
		}

		$filename = $filename ?? sprintf('laporan-%s-%s.xlsx', $tipe, date('Y-m-d_H-i-s'));
		
		$logo = new Drawing();
		$logo->setName('Logo');
        $logo->setDescription('Logo instansi perpustakaan');
        $logo->setPath(public_path('/images/static/logo.png'));
        $logo->setHeight(90);
        $logo->setCoordinates('B1');
		$exporter->setLogo($logo);

		return Excel::download($exporter, $filename);
	}
}