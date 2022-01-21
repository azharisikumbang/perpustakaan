<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    const LENGTH = 20;

    protected $table = 'peminjaman';

    protected $appends = ['total_buku'];

    public function peminjam() 
    {
        return $this->belongsTo(User::class);
    }

    public function buku() 
    {
    	return $this->belongsToMany(Buku::class, 'peminjaman_buku');
    }

    public function bukuCount() 
    {
    	return $this
    		->buku()
    		->selectRaw('count(peminjaman_buku.buku_id) as total_buku')
    		->groupBy('pivot_peminjaman_id');
    }

    public function getTotalBukuAttribute() 
    {
    	if (! array_key_exists('bukuCount', $this->relations)) $this->load('bukuCount');

    	$relation = $this->getRelation('bukuCount')->first();

    	return ($relation) ? $relation->total_buku : 0;
    }
}
