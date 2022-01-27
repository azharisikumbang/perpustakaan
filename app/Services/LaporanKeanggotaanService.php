<?php 

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanKeanggotaanService
{
	public function generate() : Collection
	{
		return $this->getData();
	}

	public function getData(int $limit = 0) : Collection
	{
		return DB::table('anggota')
			->select(DB::raw('anggota.*, count(peminjaman.kode) as jumlah_peminjaman, sum(pembayaran.nominal) as akumulasi_denda'))
			->leftJoin('peminjaman', 'anggota.id', '=', 'peminjaman.peminjam')
			->leftJoin('pembayaran', 'peminjaman.id', '=', 'pembayaran.peminjaman_id')
			->groupBy('anggota.id')
			->when($limit, fn($builder) => $$builder->limit($limit))
			->get();

	}
}