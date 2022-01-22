<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    protected $fillable = ['nama', 'nomor_identitas', 'institusi', 'alamat_institusi', 'alamat_pribadi', 'jenis_kelamin', 'kontak', 'auth'];

    public function peminjaman()
    {
    	return $this->hasMany(Peminjaman::class, 'peminjam');
    }
}
