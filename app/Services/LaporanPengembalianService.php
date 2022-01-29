<?php

namespace App\Services;

use App\Models\Peminjaman;
use App\Services\LaporanExcelInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanPengembalianService implements LaporanExcelInterface
{
	private const TITLE = 'LAPORAN PENGEMBALIAN';

	private ?Collection $data = null;

	private int $limit = 0;

	private ?string $periode = null;

	public function __construct(int $limit = 0, ?string $periode = null)
	{
		$this->limit = $limit;
		$this->limit = $limit;
	}

	public function getTitle() : string
	{
		return self::TITLE;
	}

	public function getTimestamp() : \DateTime
	{
		return new \DateTime();
	}

	public function getHeader() : array
	{
		return [
			'tanggal_pengembalian' => 'Tanggal Pengembalian',
			'kode_peminjaman' => 'Kode Peminjaman', 
			'tanggal_peminjaman' => 'Tanggal Peminjaman', 
			'nama_anggota' => 'Peminjam', 
			'terlambat' => 'Terlambat',
			'keterlambatan' => 'Keterlambatan (hari)',
			'akumulasi_denda' => 'Denda',
			'lunas' => 'Lunas'
		];
	}

	public function getData() : Collection
	{
		return $this->data ?? $this->provideData();
	}

	private function provideData() : Collection
	{
		$pengembalian = Peminjaman::select(DB::raw('
				peminjaman.id,
				peminjaman.kode as kode_peminjaman,
				peminjaman.tanggal_peminjaman,
				peminjaman.tanggal_pengembalian,
				peminjaman.lama_peminjaman,
				anggota.nama as nama_anggota,
				pembayaran.tanggal_pembayaran as tanggal_pembayaran,
				pembayaran.nominal as nominal_pembayaran',
			))
			->leftJoin('anggota', 'anggota.id', '=', 'peminjaman.peminjam')
			->leftJoin('pembayaran', 'pembayaran.peminjaman_id', '=', 'peminjaman.id')
			->orderBy('peminjaman.id', 'desc')
			->where('peminjaman.tanggal_pengembalian', '!=', '')
			->when($this->limit, fn($builder) => $builder->limit($this->limit))
			->get();

		return $this->restructureData($pengembalian);
	}

	private function restructureData(Collection $collection) : Collection
	{
		$hitungKeterlambatanService = new HitungKeterlambatanService();
		$structured = [];
		foreach ($collection as $key => $peminjaman) {
			$keterlambatan = $hitungKeterlambatanService->hitung($peminjaman, new \DateTime($peminjaman->tanggal_pengembalian));

			$structured[$peminjaman->kode_peminjaman]['kode_peminjaman'] = $peminjaman->kode_peminjaman ?? '-';
			$structured[$peminjaman->kode_peminjaman]['tanggal_peminjaman'] = $peminjaman->tanggal_peminjaman ?? '-';
			$structured[$peminjaman->kode_peminjaman]['tanggal_pengembalian'] = $peminjaman->tanggal_pengembalian ?? '-';
			$structured[$peminjaman->kode_peminjaman]['akumulasi_denda'] = $peminjaman->nominal_pembayaran ?? '-';
			$structured[$peminjaman->kode_peminjaman]['nama_anggota'] = $peminjaman->nama_anggota ?? '-';
			$structured[$peminjaman->kode_peminjaman]['lunas'] = !is_null($peminjaman->tanggal_pembayaran) ? 'LUNAS' : 'TIDAK';
			$structured[$peminjaman->kode_peminjaman]['terlambat'] = $keterlambatan > 0 ? 'YA' : 'TIDAK';
			$structured[$peminjaman->kode_peminjaman]['keterlambatan'] = $keterlambatan;

			$structured[$peminjaman->kode_peminjaman]['rows'] = 1;
		}

		return new Collection($structured);
	}
}