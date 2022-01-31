<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBukuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->string("kode");
            $table->string("isbn");
            $table->string("judul");
            $table->string("sampul")->nullable();
            $table->string("penerbit");
            $table->string("pengarang");
            $table->year("tahun_terbit");
            $table->unsignedInteger("stok");
            $table->date("tanggal_masuk");
            $table->foreignId('rak_id')->contrained('rak')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('ddc_id')->contrained('ddc')->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('buku');
    }
}
