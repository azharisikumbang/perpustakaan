<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory, SearchableTrait;

    protected $table = 'buku';

    protected $fillable = ['kode', 'isbn', 'judul', 'penerbit', 'pengarang', 'tahun_terbit', 'stok', 'tanggal_masuk', 'rak_id', 'sampul'];

    protected $appends = ['searchable_type'];

    public function rak()
    {
    	return $this->belongsTo(Rak::class);
    }

    public function peminjaman() 
    {
    	return $this->belongsToMany(Peminjaman::class, 'peminjaman_buku');
    }
}
