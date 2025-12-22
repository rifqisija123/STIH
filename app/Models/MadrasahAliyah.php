<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MadrasahAliyah extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'madrasah_aliyah';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'npsn',
        'nama',
        'bentuk_pendidikan',
        'jalur_pendidikan',
        'jenjang_pendidikan',
        'kementerian_pembina',
        'status_satuan_pendidikan',
        'akreditasi',
        'jenis_pendidikan',
        'sk_pendirian_sekolah_nomor',
        'sk_pendirian_sekolah_tanggal',
        'sk_izin_operasional_nomor',
        'sk_izin_operasional_tanggal',
        'yayasan_nama',
        'yayasan_npyp',
        'alamat_jalan',
        'alamat_rt',
        'alamat_rw',
        'alamat_nama_dusun',
        'alamat_nama_desa',
        'kode_deskel',
        'alamat_nama_kecamatan',
        'kode_kecamatan',
        'alamat_nama_kabupaten',
        'kode_kabkot',
        'alamat_nama_provinsi',
        'kode_provinsi',
        'alamat_nama_negara',
        'luas_tanah_milik',
        'sumber_listrik',
        'akses_internet',
        'nomor_fax',
        'nomor_telepon',
        'email',
        'website',
        'koordinat',
        'lintang',
        'bujur',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sk_pendirian_sekolah_tanggal' => 'date',
            'sk_izin_operasional_tanggal' => 'date',
            'lintang' => 'decimal:8',
            'bujur' => 'decimal:8',
        ];
    }
}
