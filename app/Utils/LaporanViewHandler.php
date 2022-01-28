<?php 

namespace App\Utils;

use App\Services\LaporanExcelInterface;
use Illuminate\Contracts\View\View;

class LaporanViewHandler
{
	public static function generateFromTemplate(LaporanExcelInterface $contents) : View
	{
		return view('laporan.base', ['handler' => $contents]);
	}
}