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

    protected $fillable = ['kode', 'tanggal_peminjaman', 'lama_peminjaman', 'tanggal_pengembalian', 'nominal_denda', 'peminjam'];

    public function peminjam() 
    {
        return $this->belongsTo(Anggota::class, 'peminjam');
    }

    public function buku() 
    {
    	return $this->belongsToMany(Buku::class, 'peminjaman_buku')->withPivot(['jumlah', 'created_at', 'updated_at']);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    public function bukuCount()
    {
    	return $this
    		->buku()
    		->selectRaw('sum(jumlah) as total_buku')
    		->groupBy('pivot_peminjaman_id');
    }

    public function getTotalBukuAttribute() 
    {
    	if (! array_key_exists('bukuCount', $this->relations)) $this->load('bukuCount');

    	$relation = $this->getRelation('bukuCount')->first();

    	return ($relation) ? $relation->total_buku : 0;
    }

    public static function boot() 
    {
        parent::boot();

        static::creating(function($model) {
            $model->kode_counter = Peminjaman::max('kode_counter') + 1;
            $model->kode = sprintf("%s/PINJAM/%s", date('Y/m'), str_pad($model->kode_counter, 6, '0', STR_PAD_LEFT)); // 2021/08/PINJAM/00001
        });
    }
}
