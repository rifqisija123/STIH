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
        'nama_siswa',
        'tahun_lulus',
        'asal_sekolah',
        'provinsi',
        'kota',
        'tanggal_daftar',
        'tahu_stih_darimana',
        'sumber_beasiswa',
        'jenis_beasiswa',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_daftar' => 'date',
            'tahun_lulus' => 'integer',
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
