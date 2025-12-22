<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('nisn')->nullable();
            $table->string('nama_siswa');
            $table->integer('tahun_lulus')->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kota')->nullable();
            $table->date('tanggal_daftar')->nullable();
            $table->string('tahu_stih_darimana')->nullable();
            $table->string('sumber_beasiswa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
