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
        Schema::table('mahasiswas', function (Blueprint $table) {
            // Rename nama_siswa to nama
            $table->renameColumn('nama_siswa', 'nama');
        });

        Schema::table('mahasiswas', function (Blueprint $table) {
            // Drop old columns that are no longer needed
            $table->dropColumn(['tahun_lulus', 'tanggal_daftar']);
        });

        Schema::table('mahasiswas', function (Blueprint $table) {
            // Add new REQUIRED columns (after nama)
            $table->string('email')->unique()->after('nama')->nullable();
            $table->string('nisn', 10)->after('email')->nullable(); // String to preserve leading 0
            $table->string('jenis_kelamin', 1)->after('nisn')->nullable(); // L/P

            // Add new NULLABLE columns
            $table->string('nomor_telepon', 20)->nullable()->after('jenis_kelamin'); // String for leading 0
            $table->string('tempat_lahir')->nullable()->after('nomor_telepon');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('agama')->nullable()->after('tanggal_lahir');
            $table->text('alamat')->nullable()->after('agama');
            $table->string('rt', 5)->nullable()->after('alamat'); // String for leading 0
            $table->string('rw', 5)->nullable()->after('rt'); // String for leading 0
            $table->string('dusun')->nullable()->after('rw');
            $table->string('kelurahan')->nullable()->after('dusun');
            $table->string('kecamatan')->nullable()->after('kelurahan');
            $table->string('kode_pos', 10)->nullable()->after('kecamatan');
            $table->string('program_studi')->nullable()->after('kode_pos');

            // After jenis_beasiswa
            $table->integer('angkatan')->nullable()->after('jenis_beasiswa');
            $table->string('kewarganegaraan')->nullable()->after('angkatan');
            $table->string('jenis_pendaftaran')->nullable()->after('kewarganegaraan');
            $table->string('jalur_pendaftaran')->nullable()->after('jenis_pendaftaran');
            $table->date('tanggal_masuk_kuliah')->nullable()->after('jalur_pendaftaran');
            $table->string('mulai_semester')->nullable()->after('tanggal_masuk_kuliah');
            $table->string('code_religion')->nullable()->after('mulai_semester');
            $table->string('district_code')->nullable()->after('code_religion');
            $table->string('village_code')->nullable()->after('district_code');
            $table->string('code_stihs')->nullable()->after('village_code');
        });

        // Modify existing columns to be nullable
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->string('asal_sekolah')->nullable()->change();
            $table->string('provinsi')->nullable()->change();
            $table->string('kota')->nullable()->change();
            $table->string('tahu_stih_darimana')->nullable()->change();
            $table->string('sumber_beasiswa')->nullable()->change();
            $table->string('jenis_beasiswa')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'email',
                'nisn',
                'jenis_kelamin',
                'nomor_telepon',
                'tempat_lahir',
                'tanggal_lahir',
                'agama',
                'alamat',
                'rt',
                'rw',
                'dusun',
                'kelurahan',
                'kecamatan',
                'kode_pos',
                'program_studi',
                'angkatan',
                'kewarganegaraan',
                'jenis_pendaftaran',
                'jalur_pendaftaran',
                'tanggal_masuk_kuliah',
                'mulai_semester',
                'code_religion',
                'district_code',
                'village_code',
                'code_stihs'
            ]);
        });

        Schema::table('mahasiswas', function (Blueprint $table) {
            // Rename nama back to nama_siswa
            $table->renameColumn('nama', 'nama_siswa');

            // Add back dropped columns
            $table->integer('tahun_lulus')->nullable()->after('nama_siswa');
            $table->date('tanggal_daftar')->nullable()->after('kota');
        });

        // Revert nullable changes
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->string('asal_sekolah')->nullable(false)->change();
            $table->string('provinsi')->nullable(false)->change();
            $table->string('kota')->nullable(false)->change();
            $table->string('tahu_stih_darimana')->nullable(false)->change();
            $table->string('sumber_beasiswa')->nullable(false)->change();
        });
    }
};
