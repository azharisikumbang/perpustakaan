<?php 

namespace App\Services;

use App\Models\Peminjaman;

class HitungKeterlambatanService
{
	public function hitung(Peminjaman $peminjaman, \DateTime $comparator = null) : int
	{
		$comparator = $comparator ?? new \DateTime();
        $batas_pengembalian = new \DateTime($peminjaman->tanggal_peminjaman);
        $batas_pengembalian->modify("+{$peminjaman->lama_peminjaman} days");
        $perbedaan = $comparator->diff($batas_pengembalian);

        if ($perbedaan->invert === 0) return 0;

        return ($perbedaan->h > 0) ? $perbedaan->days + 1 : $perbedaan->days;
	}

}