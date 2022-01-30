<?php 

namespace App\Services;

use App\Models\Buku;
use App\Services\LaporanExcelInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanDataBukuService implements LaporanExcelInterface
{
	private const TITLE = 'LAPORAN DATA BUKU';

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
			'kode' => 'Kode Buku',
			'judul' => 'Judul',
			'isbn' => 'ISBN',
			'penerbit' => 'Penerbit',
			'pengarang' => 'Pengarang',
			'rak' => 'Nomor Rak',
			'stok' => 'Stok',
			'tanggal_masuk' => 'Tanggal Masuk Buku'
		];
	}

	private function provideData() : Collection
	{
		$collection = Buku::with('rak')
			->when($this->year, fn($builder) => $builder->whereYear('tanggal_masuk', '=', $this->year))
			->when($this->month, fn($builder) => $builder->whereMonth('tanggal_masuk', '=', $this->month))
			->when($this->date, fn($builder) => $builder->whereDay('tanggal_masuk', '=', $this->date))
			->when($this->limit, fn($builder) => $builder->limit($this->limit))
			->get();

		return $this->generateDataAttributes($collection);
	}

	private function generateDataAttributes(Collection $collection) : Collection
	{
		return $collection->map(function ($buku) {
			return [
				'kode' => $buku->kode,
				'judul' => $buku->judul,
				'rak' => sprintf('%s - %s', $buku->rak->kode, $buku->rak->alias),
				'isbn' => $buku->isbn,
				'penerbit' => $buku->penerbit,
				'pengarang' => $buku->pengarang,
				'stok' => $buku->stok,
				'tanggal_masuk' => $buku->tanggal_masuk,
				'rows' => 1
			];
		});
	}
}