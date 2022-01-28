<?php 

namespace App\Services;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

interface LaporanExcelInterface
{
	public function getTitle() : string;

	public function getTimestamp() : \DateTime;

	public function getHeader() : array;

	public function getData() : Collection;
}