<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';

    protected $fillable = ['kode', 'isbn', 'judul', 'penerbit', 'pengarang', 'tahun_terbit', 'stok', 'tanggal_masuk', 'rak_id', 'sampul', 'ddc_id'];

    public function rak()
    {
    	return $this->belongsTo(Rak::class);
    }

    public function ddc()
    {
        return $this->belongsTo(DDC::class, 'ddc_id');
    }

    public function peminjaman() 
    {
    	return $this->belongsToMany(Peminjaman::class, 'peminjaman_buku');
    }
}
