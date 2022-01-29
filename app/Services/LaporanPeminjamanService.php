<?php

namespace App\Services;

use App\Models\Peminjaman;
use App\Services\LaporanExcelInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanPeminjamanService implements LaporanExcelInterface
{
	private const TITLE = 'LAPORAN PEMINJAMAN';

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
			'tanggal_peminjaman' => 'Tanggal Peminjaman', 
			'kode_peminjaman' => 'Kode Peminjaman', 
			'nama_anggota' => 'Peminjam', 
			'kode_anggota' => 'No. Anggota', 
			'lama_peminjaman' => 'Lama Peminjaman (hari)', 
			'batas_pengembalian' => 'Batas Pengembalian',
			'akumulasi_buku_dipinjam' => 'Akumulasi Semua Buku Dipinjam', 
			'kode_buku' => 'Kode Buku', 
			'judul_buku' => 'Judul Buku', 
			'isbn_buku' => 'ISBN', 
			'rak' => 'Rak', 
			'jumlah_peminjaman_buku' => 'Jumlah Buku Dipinjam',
			'tanggal_pengembalian' => 'Tanggal Pengembalian',
			'dikembalikan' => 'Dikembalikan',
			'hari_keterlambatan' => 'Hari Keterlambatan',
			'akumulasi_denda' => 'Denda',
			'dibayarkan' => 'Lunas',
		];
	}

	public function getData() : Collection
	{
		return $this->data ?? $this->provideData();
	}

	private function provideData() : Collection
	{
		$peminjaman = Peminjaman::select(DB::raw('
				peminjaman.id,
				peminjaman.kode as kode_peminjaman,
				peminjaman.tanggal_peminjaman,
				peminjaman.lama_peminjaman,
				peminjaman.tanggal_pengembalian,
				peminjaman.nominal_denda,
				anggota.kode as kode_anggota,
				anggota.nama as nama_anggota,
				buku.kode as kode_buku,
				buku.judul as judul_buku,
				buku.isbn as isbn_buku,
				peminjaman_buku.jumlah as jumlah_peminjaman_buku,
				rak.kode as kode_rak,
				rak.alias as alias_rak,
				pembayaran.tanggal_pembayaran as tanggal_pembayaran,
				pembayaran.nominal as nominal_pembayaran'
			))
			->leftJoin('anggota', 'anggota.id', '=', 'peminjaman.peminjam')
			->leftJoin('peminjaman_buku', 'peminjaman_buku.peminjaman_id', '=', 'peminjaman.id')
			->leftJoin('buku', 'buku.id', '=', 'peminjaman_buku.buku_id')
			->leftJoin('rak', 'buku.rak_id', '=', 'rak.id')					
			->leftJoin('pembayaran', 'pembayaran.peminjaman_id', '=', 'peminjaman.id')
			->orderBy('peminjaman.id', 'desc')
			->when($this->limit, fn($builder) => $builder->limit($this->limit))
			->get();

		return $this->restructureData($peminjaman);
	}

	private function restructureData(Collection $collection) : Collection
	{
		$hitungKeterlambatanService = new HitungKeterlambatanService();
		$structured = [];
		foreach ($collection as $key => $peminjaman) {
			$comparator = !is_null($peminjaman->tanggal_pengembalian) ? new \DateTime($peminjaman->tanggal_pengembalian) : null;
			$keterlambatan = $hitungKeterlambatanService->hitung($peminjaman, $comparator);
			$tanggal_peminjaman = new \DateTime($peminjaman->tanggal_peminjaman);
			$tanggal_pengembalian = $tanggal_peminjaman->modify(sprintf("+%s days", $peminjaman->lama_peminjaman));

			$structured[$peminjaman->kode_peminjaman]['kode_peminjaman'] = $peminjaman->kode_peminjaman ?? '-';
			$structured[$peminjaman->kode_peminjaman]['tanggal_peminjaman'] = $peminjaman->tanggal_peminjaman ?? '-';
			$structured[$peminjaman->kode_peminjaman]['lama_peminjaman'] = $peminjaman->lama_peminjaman ?? '-';
			$structured[$peminjaman->kode_peminjaman]['batas_pengembalian'] = $tanggal_pengembalian->format('Y-m-d H:i:s') ?? '-';
			$structured[$peminjaman->kode_peminjaman]['tanggal_pengembalian'] = $peminjaman->tanggal_pengembalian ?? '-';
			$structured[$peminjaman->kode_peminjaman]['akumulasi_denda'] = $peminjaman->nominal_pembayaran ?? '-';
			$structured[$peminjaman->kode_peminjaman]['akumulasi_buku_dipinjam'] = $peminjaman->total_buku ?? '-';
			$structured[$peminjaman->kode_peminjaman]['tanggal_pembayaran'] = $peminjaman->tanggal_pembayaran ?? '-';
			$structured[$peminjaman->kode_peminjaman]['kode_anggota'] = $peminjaman->kode_anggota ?? '-';
			$structured[$peminjaman->kode_peminjaman]['nama_anggota'] = $peminjaman->nama_anggota ?? '-';
			$structured[$peminjaman->kode_peminjaman]['dibayarkan'] = $peminjaman->tanggal_pembayaran ? 'LUNAS' : '-';
			$structured[$peminjaman->kode_peminjaman]['hari_keterlambatan'] = $keterlambatan;
			$structured[$peminjaman->kode_peminjaman]['dikembalikan'] = is_null($peminjaman->tanggal_pengembalian) ? 'TIDAK' : 'YA';

			// can be array
			$structured[$peminjaman->kode_peminjaman]['kode_buku'][] = $peminjaman->kode_buku;
			$structured[$peminjaman->kode_peminjaman]['judul_buku'][] = $peminjaman->judul_buku;
			$structured[$peminjaman->kode_peminjaman]['isbn_buku'][] = $peminjaman->isbn_buku;
			$structured[$peminjaman->kode_peminjaman]['jumlah_peminjaman_buku'][] = $peminjaman->jumlah_peminjaman_buku;
			$structured[$peminjaman->kode_peminjaman]['rak'][] = sprintf("%s - %s", $peminjaman->kode_rak, $peminjaman->alias_rak);
			

			$structured[$peminjaman->kode_peminjaman]['rows'] = count($structured[$peminjaman->kode_peminjaman]['kode_buku']);
		}

		return new Collection($structured);
	}
}