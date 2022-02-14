<?php 

namespace App\Services;

use App\Models\Anggota;
use App\Services\LaporanExcelInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanKeanggotaanService implements LaporanExcelInterface
{
	private const TITLE = 'LAPORAN KEANGGOTAAN';

	private ?Collection $data = null;

	private int $limit;

	private int $year;

	private int $month;

	private int $date;

	public function __construct(int $limit = 0, int $year = 0, int $month = 0, int $date = 0)
	{
		$this->limit = $limit;
		$this->year = $year;
		$this->month = $month;
		$this->date = $date;
	}

	public function getData() : Collection
	{
		return $this->data ?? $this->provideData();
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
			'nama' => 'Nama Anggota',
			'jenis_kelamin' => 'Jenis Kelamin',
			'alamat_pribadi' => 'Alamat Pribadi',
			'kontak' => 'Kontak',
			'email' => 'Email',
			'kode' => 'Nomor Identitas',
			'institusi' => 'Nama Institusi',
			'alamat_institusi' => 'Alamat Institusi',
			'jumlah_peminjaman' => 'Jumlah Peminjaman',
			'jumlah_pengembalian' => 'Jumlah Pengembalian',
			'jumlah_pengembalian_tepat_waktu' => 'Jumlah Pengembalian Tepat Waktu',
			'jumlah_pengembalian_terlambat' => 'Jumlah Pengembalian Terlambat',
			'jumlah_peminjaman_berlangsung' => 'Jumlah Peminjaman Berlangsung',
			'jumlah_peminjaman_belum_dikembalikan' => 'Jumlah Peminjaman Belum Dikembalikan',
			'akumulasi_denda' => 'Akumulasi Denda'
		];
	}

	private function provideData() : Collection
	{
		$collection = Anggota::select(DB::raw('anggota.*, count(peminjaman.kode) as jumlah_peminjaman, sum(pembayaran.nominal) as akumulasi_denda, users.email as email'))
				->leftJoin('peminjaman', 'anggota.id', '=', 'peminjaman.peminjam')
				->leftJoin('pembayaran', 'peminjaman.id', '=', 'pembayaran.peminjaman_id')
				->leftJoin('users', 'users.id', '=', 'anggota.auth')
				->groupBy('anggota.id')
				->when($this->year, fn($builder) => $builder->whereYear('anggota.created_at', '=', $this->year))
				->when($this->month, fn($builder) => $builder->whereMonth('anggota.created_at', '=', $this->month))
				->when($this->date, fn($builder) => $builder->whereDay('anggota.created_at', '=', $this->date))
				->when($this->limit, fn($builder) => $builder->limit($this->limit))
				->get();

		return $this->generateDataAttributes($collection);
	}

	private function generateDataAttributes(Collection $collection) : Collection
	{
		$counter = new HitungKeterlambatanService();

		return $collection->map(function ($anggota) use ($counter) {
			$arrayAnggota = $anggota->toArray();
			$jumlahPengembalian = 0;
			$jumlahPengembalianTepatWaktu = 0;
			$jumlahPengembalianTerlambat = 0;
			$jumlahPeminjamanBerlangsung = 0;
			$jumlahPeminjamanBelumDikembalikan = 0;

			foreach ($anggota->peminjaman as $peminjaman) {
				$tanggal_pengembalian = $peminjaman->tanggal_pengembalian ? new \DateTime($peminjaman->tanggal_pengembalian) : new \DateTime();
				$keterlambatan = $counter->hitung($peminjaman, $tanggal_pengembalian);
				if (!is_null($peminjaman->tanggal_pengembalian)) $jumlahPengembalian++;

				if (!is_null($peminjaman->tanggal_pengembalian)  && $keterlambatan < 1) $jumlahPengembalianTepatWaktu++;
				if (!is_null($peminjaman->tanggal_pengembalian)  && $keterlambatan >= 1) $jumlahPengembalianTerlambat++;

				if (is_null($peminjaman->tanggal_pengembalian)  && $keterlambatan < 1) $jumlahPeminjamanBerlangsung++;
				if (is_null($peminjaman->tanggal_pengembalian) && $keterlambatan >= 1) $jumlahPeminjamanBelumDikembalikan++;
			}

			$arrayAnggota['jenis_kelamin'] = $this->iso5218($arrayAnggota['jenis_kelamin']);

			return array_merge($arrayAnggota, [
				'jumlah_pengembalian' => $jumlahPengembalian,
				'jumlah_pengembalian_tepat_waktu' => $jumlahPengembalianTepatWaktu,
				'jumlah_pengembalian_terlambat' => $jumlahPengembalianTerlambat,
				'jumlah_peminjaman_berlangsung' => $jumlahPeminjamanBerlangsung,
				'jumlah_peminjaman_belum_dikembalikan' => $jumlahPeminjamanBelumDikembalikan,
				'rows' => 1
			]);
		});
	}

	private function iso5218(int $kode) : string
	{
		$kelamin = "Tidak disebutkan";
		switch ($kode) {
			case 0:
				$kelamin = "Tidak diketahui";
				break;
			case 1:
				$kelamin = "Laki-laki";
				break;
			case 2:
				$kelamin = "Perempuan";
				break;
			
			default:
				$kelamin = "Tidak disebutkan";
				break;
		}

		return $kelamin;
	}
}