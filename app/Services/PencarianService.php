<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Utils\Paginator;
use Illuminate\Database\Eloquent\Builder;

class PencarianService
{
	public function cari(string $q, array $kriteria)
	{
		$unions = [];
		$query = false;

		if (in_array('all', $kriteria)) {
			$kriteria = array_merge($kriteria, ['anggota', 'peminjaman', 'buku']);
		}

		$kriteria = array_unique($kriteria);

		if (in_array('anggota', $kriteria)) {
			$query = Anggota::select('id', 'kode')
				->where('nama', 'LIKE', "%{$q}%")
				->orWhere('kode', 'LIKE', "%{$q}%");

			$unions['anggota'] = $query;
		}

		if (in_array('peminjaman', $kriteria)) {
			$query = Peminjaman::select('id', 'kode')
				->where('kode', 'LIKE', "%{$q}%");

			$unions['peminjaman'] = $query;
		}

		if (in_array('buku', $kriteria)) {
			$query = Buku::select('id', 'kode')
				->where('kode', 'LIKE', "%{$q}%")
				->orWhere('isbn', 'LIKE', "%{$q}%")
				->orWhere('judul', 'LIKE', "%{$q}%");

			$unions['buku'] = $query;
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