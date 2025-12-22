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
        Schema::create('high_school', function (Blueprint $table) {
            $table->id();
            $table->string('npsn')->unique();
            $table->string('nama');
            $table->string('bentuk_pendidikan')->nullable();
            $table->string('jalur_pendidikan')->nullable();
            $table->string('jenjang_pendidikan')->nullable();
            $table->string('kementerian_pembina')->nullable();
            $table->string('status_satuan_pendidikan')->nullable();
            $table->string('akreditasi')->nullable();
            $table->string('jenis_pendidikan')->nullable();
            $table->string('sk_pendirian_sekolah_nomor')->nullable();
            $table->date('sk_pendirian_sekolah_tanggal')->nullable();
            $table->string('sk_izin_operasional_nomor')->nullable();
            $table->date('sk_izin_operasional_tanggal')->nullable();
            $table->string('yayasan_nama')->nullable();
            $table->string('yayasan_npyp')->nullable();
            $table->string('alamat_jalan')->nullable();
            $table->string('alamat_rt')->nullable();
            $table->string('alamat_rw')->nullable();
            $table->string('alamat_nama_dusun')->nullable();
            $table->string('alamat_nama_desa')->nullable();
            $table->string('kode_deskel')->nullable();
            $table->string('alamat_nama_kecamatan')->nullable();
            $table->string('kode_kecamatan')->nullable();
            $table->string('alamat_nama_kabupaten')->nullable();
            $table->string('kode_kabkot')->nullable();
            $table->string('alamat_nama_provinsi')->nullable();
            $table->string('kode_provinsi')->nullable();
            $table->string('alamat_nama_negara')->nullable();
            $table->string('luas_tanah_milik')->nullable();
            $table->string('sumber_listrik')->nullable();
            $table->string('akses_internet')->nullable();
            $table->string('nomor_fax')->nullable();
            $table->string('nomor_telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('koordinat')->nullable();
            $table->decimal('lintang', 10, 8)->nullable();
            $table->decimal('bujur', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('high_school');
    }
};
