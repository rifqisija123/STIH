<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    /** @use HasFactory<\Database\Factories\MahasiswaFactory> */
    use HasFactory;

    protected $fillable = [
        'nim',
        'nama',
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
        'asal_sekolah',
        'provinsi',
        'kota',
        'tahu_stih_darimana',
        'sumber_beasiswa',
        'jenis_beasiswa',
        'angkatan',
        'kewarganegaraan',
        'jenis_pendaftaran',
        'jalur_pendaftaran',
        'tanggal_masuk_kuliah',
        'tanggal_daftar',
        'mulai_semester',
        'code_religion',
        'district_code',
        'village_code',
        'code_stihs',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_masuk_kuliah' => 'date',
            'tanggal_daftar' => 'date',
            'angkatan' => 'integer',
        ];
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'provinsi', 'province_code');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'kota', 'city_code');
    }

    public function highSchool()
    {
        return $this->belongsTo(HighSchool::class, 'asal_sekolah', 'npsn');
    }

    public function madrasahAliyah()
    {
        return $this->belongsTo(MadrasahAliyah::class, 'asal_sekolah', 'npsn');
    }

    public function vocationalHighSchool()
    {
        return $this->belongsTo(VocationalHighSchool::class, 'asal_sekolah', 'npsn');
    }

    public function getSchoolAttribute()
    {
        return $this->highSchool ?? $this->madrasahAliyah ?? $this->vocationalHighSchool;
    }

    public function tahuStih()
    {
        return $this->belongsTo(TahuStih::class, 'tahu_stih_darimana', 'id');
    }
}
