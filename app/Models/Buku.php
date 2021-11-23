<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';

    protected $fillable = ['kode', 'isbn', 'judul', 'penerbit', 'pengarang', 'tahun_terbit', 'stok', 'tanggal_masuk', 'rak_id'];

    public function rak()
    {
    	return $this->belongsTo(Rak::class);
    }
}
