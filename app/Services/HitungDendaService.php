<?php 

namespace App\Services;

use App\Models\Peminjaman;

class HitungDendaService
{
	public function hitung(Peminjaman $peminjaman, int $keterlambatan) 
	{
        return $keterlambatan * $peminjaman->nominal_denda;
	}

}