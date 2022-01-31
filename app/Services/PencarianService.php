<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\DDC;
use App\Models\Peminjaman;
use App\Utils\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PencarianService
{
	public function cari(string $q, array $kriteria = ['all'])
	{
		$unions = [];
		$query = false;

		if (in_array('all', $kriteria)) {
			$kriteria = array_merge($kriteria, ['anggota', 'peminjaman', 'buku', 'ddc']);
		}

		$kriteria = array_unique($kriteria);

		if (in_array('anggota', $kriteria)) {
			$query = Anggota::select(DB::raw("id, kode, concat(kode, ' - ', nama) as display, concat('Anggota') as tipe_display"))
				->where('nama', 'LIKE', "%{$q}%")
				->orWhere('kode', 'LIKE', "%{$q}%");

			$unions['anggota'] = $query;
		}

		if (in_array('peminjaman', $kriteria)) {
			$query = Peminjaman::select(DB::raw("id, kode, kode as display, concat('Peminjaman') as tipe_display"))
				->where('kode', 'LIKE', "%{$q}%");

			$unions['peminjaman'] = $query;
		}

		if (in_array('buku', $kriteria)) {
			$query = Buku::select(DB::raw("id, kode, concat(kode, ' - ', judul) as display, concat('Buku') as tipe_display"))
				->where('kode', 'LIKE', "%{$q}%")
				->orWhere('isbn', 'LIKE', "%{$q}%")
				->orWhere('judul', 'LIKE', "%{$q}%");

			$unions['buku'] = $query;
		}

		if (in_array('ddc', $kriteria)) {
			$query = DDC::select(DB::raw("id, kode, concat(kode, ' - ', klasifikasi) as display, concat('DDC') as tipe_display"))
				->where('kode', 'LIKE', "%{$q}%")
				->orWhere('klasifikasi', 'LIKE', "%{$q}%");

			$unions['ddc'] = $query;
		}

		foreach ($unions as $key => $union) {
			if ($key === strtolower(basename(get_class($query->getModel())))) {
				continue;
			}

			$query = $query->union($union);
		}

		return (false !== $query) 
			? $query->orderBy('id')->paginate(Paginator::OFFSET)->withQueryString() 
			: [];
	}
}