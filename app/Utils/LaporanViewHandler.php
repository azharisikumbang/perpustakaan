<?php 

namespace App\Utils;

use App\Services\LaporanExcelInterface;
use Illuminate\Contracts\View\View;

class LaporanViewHandler
{
	public static function generateFromTemplate(LaporanExcelInterface $contents, string $template = 'laporan.base') : View
	{
		return view($template, ['handler' => $contents]);
	}
}