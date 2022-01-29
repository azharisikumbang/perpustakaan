<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Services\LaporanExcelInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanPembayaranDendaService implements LaporanExcelInterface
{
	private const TITLE = 'LAPORAN PEMBAYARAN DENDA';

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
			'tanggal_pembayaran' => 'Tanggal Pembayaran',
			'kode_peminjaman' => 'Kode Peminjaman', 
			'nama_anggota' => 'Peminjam', 
			'akumulasi_denda' => 'Denda',
		];
	}

	public function getData() : Collection
	{
		return $this->data ?? $this->provideData();
	}

	private function provideData() : Collection
	{
		$pembayaran = Pembayaran::select(DB::raw('
				pembayaran.id,
				pembayaran.tanggal_pembayaran as tanggal_pembayaran,
				pembayaran.nominal as nominal_pembayaran,
				peminjaman.kode as kode_peminjaman,
				anggota.nama as nama_anggota'
			))
			->leftJoin('peminjaman', 'peminjaman.id', '=', 'pembayaran.peminjaman_id')
			->leftJoin('anggota', 'anggota.id', '=', 'peminjaman.peminjam')
			->orderBy('pembayaran.id', 'desc')
			->when($this->limit, fn($builder) => $builder->limit($this->limit))
			->get();

		return $this->restructureData($pembayaran);
	}

	private function restructureData(Collection $collection) : Collection
	{
		$hitungKeterlambatanService = new HitungKeterlambatanService();
		$structured = [];
		foreach ($collection as $key => $pembayaran) {
			$structured[] = [
				'tanggal_pembayaran' => $pembayaran->tanggal_pembayaran,
				'kode_peminjaman' => $pembayaran->kode_peminjaman, 
				'nama_anggota' => $pembayaran->nama_anggota, 
				'akumulasi_denda' => $pembayaran->nominal_pembayaran,
				'rows' => 1
			];
		}

		return new Collection($structured);
	}
}