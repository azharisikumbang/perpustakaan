<?php

use App\Models\Peminjaman;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeminjamanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode');             
            $table->unsignedInteger('kode_counter');
            $table->dateTime('tanggal_peminjaman');
            $table->unsignedTinyInteger('lama_peminjaman')->default(0);
            $table->dateTime('tanggal_pengembalian')->nullable()->default(null);
            $table->unsignedDecimal('nominal_denda', 16, 0)->default(0);
            $table->unsignedBigInteger('peminjam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peminjaman');
    }
}
