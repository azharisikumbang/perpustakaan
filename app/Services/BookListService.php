<?php 

namespace App\Services;

use App\Models\Anggota;
use App\Models\Buku;

class BookListService 
{
	const CONTAINER_NAME = 'keranjang-pinjam';

	public function storeBookToList(Buku $buku, int $amout) : void
	{
		if (is_null(session(self::CONTAINER_NAME))) { 
			$this->setUpBookListContainer();
		}

    	$session = session(self::CONTAINER_NAME);
    	$session['diperbaharui'] = time();
    	$session['list-buku'][$buku->kode] = $this->createListItem($buku, $amout);

 		session([self::CONTAINER_NAME => $session]);
	}

	public function updateAmout(Buku $buku, int $amout) : bool
	{
		$current = $this->get($buku);

		if (count($current) < 1) return false;
		
		$current['jumlah'] = $amout;
		$session = session(self::CONTAINER_NAME);
    	$session['diperbaharui'] = time();
    	$session['list-buku'][$buku->kode] = $current;

    	session([self::CONTAINER_NAME => $session]);

    	return true;
	}

	public function setPeminjam(Anggota $anggota) : void
	{
		$session = session(self::CONTAINER_NAME);
    	$session['diperbaharui'] = time();
    	$session['peminjam'] = $anggota->toArray();

 		session([self::CONTAINER_NAME => $session]);
	}

	public function getAllBookList() : array 
	{
		if (is_null(session(self::CONTAINER_NAME))) $this->setUpBookListContainer();

		return session(self::CONTAINER_NAME);
	}

	public function get(Buku $buku) : array 
	{
		$session = $this->getAllBookList();

		return (array_key_exists($buku->kode, $session['list-buku'])) ? $session['list-buku'][$buku->kode] : [];
	}

	public function removeBookFromList(Buku $buku) : bool 
	{
		$session = session(self::CONTAINER_NAME);

		if (!array_key_exists($buku->kode, $session['list-buku'])) return false;

		$session['diperbaharui'] = time();
		unset($session['list-buku'][$buku->kode]);
		session([self::CONTAINER_NAME => $session]);

		return true;
	}

	public function setUpBookListContainer() : void
	{
		session([self::CONTAINER_NAME => [
			'list-buku' => [],
			'peminjam' => null,
			'petugas' => auth()->id(),
			'diperbaharui' => time()
		]]);
	}

	private function createListItem(Buku $buku, int $amout) : array 
	{
		return [
			'id' => $buku->id,
    		'kode' => $buku->kode,
    		'jumlah' => $amout,
    		'details' => [
    			'isbn' => $buku->isbn,
    			'judul' => $buku->judul,
    			'pengarang' => $buku->pengarang,
    			'stok' => $buku->stok,
    			'rak' => $buku->rak->kode . ' - ' . $buku->rak->alias,
    			'sampul' => $buku->sampul
    		]
    	];
	}
}