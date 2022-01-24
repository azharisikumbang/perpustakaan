<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = ['tanggal', 'nominal', 'peminjaman_id'];

    public function peminjaman()
    {
    	return $this->belongsTo(Peminjaman::class);
    }
}
