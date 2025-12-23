<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use App\Models\Province;
use App\Models\City;
use App\Models\HighSchool;
use App\Models\VocationalHighSchool;
use App\Models\MadrasahAliyah;
use App\Models\TahuStih;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class MahasiswaImport implements ToCollection, WithHeadingRow
{
    public $errors = [];
    public $importedCount = 0;
    public $skippedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Excel row index (starts at 2 because of header)
            $rowIndex = $index + 2; 

            // Convert to array for easier handling
            $data = $row->toArray();

            if ($this->validateAndSaveRow($data, $rowIndex)) {
                $this->importedCount++;
            } else {
                $this->skippedCount++;
            }
        }
    }

    private function validateAndSaveRow(array $row, int $rowNumber): bool
    {
        try {
            // ============================
            // NORMALISASI FIELD
            // ============================
            // Map 'nim' from excel to 'nim' in logic
            $data = [
                'nim' => $row['nim'] ?? null, 
                'nama_siswa' => $row['nama_siswa'] ?? null,
                'tahun_lulus' => $row['tahun_lulus'] ?? null,
                'asal_sekolah' => $row['asal_sekolah'] ?? null,
                'provinsi' => $row['provinsi'] ?? null,
                'kota' => $row['kota'] ?? null,
                'tanggal_daftar' => $this->transformDate($row['tanggal_daftar'] ?? null),
                'tahu_stih_darimana' => $row['tahu_stih_darimana'] ?? null,
                'sumber_beasiswa' => $row['sumber_beasiswa'] ?? null,
                'jenis_beasiswa' => $row['jenis_beasiswa'] ?? null,
            ];

            // ============================
            // VALIDASI DASAR
            // ============================
            $rules = [
                'nim' => 'required|numeric|digits:10',
                'nama_siswa' => 'required|string|max:255',
                'tahun_lulus' => 'required|integer|digits:4|min:2000|max:' . (date('Y') + 1),
                'asal_sekolah' => 'required|string',
                'provinsi' => 'required|string',
                'kota' => 'required|string',
                'tanggal_daftar' => 'required|date',
                'tahu_stih_darimana' => 'required|string',
                'sumber_beasiswa' => 'required|in:beasiswa,non_beasiswa',
            ];

            $messages = [
                'nim.digits' => 'NIM harus 10 digit angka',
                'nim.numeric' => 'NIM harus berupa angka',
                'sumber_beasiswa.in' => 'Sumber beasiswa harus "beasiswa" atau "non_beasiswa" (case sensitive)',
            ];

            // Jika sumber beasiswa adalah "beasiswa", maka jenis_beasiswa wajib diisi
            if ($data['sumber_beasiswa'] === 'beasiswa') {
                $rules['jenis_beasiswa'] = 'required|in:50%,100%';
                $messages['jenis_beasiswa.required'] = 'Jenis beasiswa wajib diisi jika sumber beasiswa adalah "beasiswa"';
                $messages['jenis_beasiswa.in'] = 'Jenis beasiswa harus "50%" atau "100%"';
            } else {
                // Jika sumber beasiswa bukan "beasiswa", jenis_beasiswa harus null
                if (!empty($data['jenis_beasiswa'])) {
                    $this->errors[] = "Baris {$rowNumber}: Jenis beasiswa harus kosong jika sumber beasiswa bukan 'beasiswa'";
                    return false;
                }
                $data['jenis_beasiswa'] = null;
            }

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                $this->errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                return false;
            }

            // ============================
            // VALIDASI UNIQUE NIM
            // ============================
            if (Mahasiswa::where('nim', $data['nim'])->exists()) {
                $this->errors[] = "Baris {$rowNumber}: NIM '{$data['nim']}' sudah terdaftar di database";
                return false;
            }

            // ============================
            // MAPPING PROVINSI & KOTA
            // ============================
            $provinceCode = $this->mapProvinsi($data['provinsi']);
            if (!$provinceCode) {
                $this->errors[] = "Baris {$rowNumber}: Provinsi '{$data['provinsi']}' tidak ditemukan";
                return false;
            }

            $cityCode = $this->mapKota($data['kota'], $provinceCode);
            if (!$cityCode) {
                // Try finding city without strict province check first to see if it's a mismatch
                $this->errors[] = "Baris {$rowNumber}: Kota '{$data['kota']}' tidak ditemukan di provinsi tersebut";
                return false;
            }

            // ============================
            // EXTRACT & VALIDATE NPSN
            // ============================
            $npsn = $this->extractNpsn($data['asal_sekolah']);
            if (!$npsn || !$this->validateSchoolExists($npsn)) {
                $this->errors[] = "Baris {$rowNumber}: Sekolah '{$data['asal_sekolah']}' (NPSN) tidak valid/ditemukan";
                return false;
            }

            // ============================
            // MAP TAHU STIH
            // ============================
            $tahuStihId = $this->mapTahuStih($data['tahu_stih_darimana']);
            if (!$tahuStihId) {
                // Should default to Lainnya per logic, so this might not hit often
                $this->errors[] = "Baris {$rowNumber}: Sumber info STIH tidak valid";
                return false;
            }

            // ============================
            // SIMPAN KE DATABASE
            // ============================
            Mahasiswa::create([
                'nim' => $data['nim'],
                'nama_siswa' => $data['nama_siswa'],
                'tahun_lulus' => $data['tahun_lulus'],
                'asal_sekolah' => $npsn,
                'provinsi' => $provinceCode,
                'kota' => $cityCode,
                'tanggal_daftar' => $data['tanggal_daftar'],
                'tahu_stih_darimana' => $tahuStihId,
                'sumber_beasiswa' => $data['sumber_beasiswa'],
                'jenis_beasiswa' => $data['jenis_beasiswa'],
            ]);

            return true;

        } catch (\Exception $e) {
            $this->errors[] = "Baris {$rowNumber}: Terjadi kesalahan internal ({$e->getMessage()})";
            return false;
        }
    }

    private function transformDate($value)
    {
        if (!$value) return null;
        try {
            // Excel dates are sometimes integers (days since 1900-01-01)
            // Phpspreadsheet handles this usually in WithHeadingRow/ToCollection? 
            // Often it returns a generic object or int.
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            // If string DD-MM-YYYY or YYYY-MM-DD
            return date('Y-m-d', strtotime($value));
        } catch (\Exception $e) {
            return null;
        }
    }

    private function mapProvinsi(string $provinsiName): ?string
    {
        $provinsiName = trim($provinsiName);
        
        // 1. Direct Match
        $province = Province::where('province', 'LIKE', "%{$provinsiName}%")
            ->orWhere('province', 'LIKE', str_replace(' ', '%', $provinsiName))
            ->first();
        if ($province) return $province->province_code;

        // 2. Word-based Match
        $words = explode(' ', $provinsiName);
        if (count($words) > 1) {
            $province = Province::where(function($query) use ($words) {
                foreach ($words as $word) {
                    if (strlen($word) > 2) { 
                        $query->where('province', 'LIKE', "%{$word}%");
                    }
                }
            })->first();
            if ($province) return $province->province_code;
        }
        return null;
    }

    private function mapKota(string $kotaName, string $provinceCode): ?string
    {
        $kotaName = trim($kotaName);

        // 1. Direct Match
        $city = City::where('province_code', $provinceCode)
            ->where(function($query) use ($kotaName) {
                $query->where('city', 'LIKE', "%{$kotaName}%")
                      ->orWhere('city', 'LIKE', str_replace(' ', '%', $kotaName));
            })->first();
        if ($city) return $city->city_code;

        // 2. Word-based
        $words = explode(' ', $kotaName);
        if (count($words) > 1) {
            $city = City::where('province_code', $provinceCode)
                ->where(function($query) use ($words) {
                    foreach ($words as $word) {
                        if (strlen($word) > 2) {
                            $query->where('city', 'LIKE', "%{$word}%");
                        }
                    }
                })->first();
            if ($city) return $city->city_code;
        }
        return null;
    }

    private function extractNpsn($value): ?string
    {
        if (!$value) return null;
        if (preg_match('/\[(\d+)\]$/', $value, $m)) return $m[1];
        if (preg_match('/^\d{8}$/', $value)) return $value;
        return null;
    }

    private function validateSchoolExists(string $npsn): bool
    {
        return HighSchool::where('npsn', $npsn)->exists()
            || VocationalHighSchool::where('npsn', $npsn)->exists()
            || MadrasahAliyah::where('npsn', $npsn)->exists();
    }

    private function mapTahuStih($source): ?string
    {
        if (!$source) return null;
        $source = strtolower(trim($source));
        
        $mappings = [
            'website' => 'TH002',
            'media sosial' => 'TH001', 'sosial_media' => 'TH001', 'facebook' => 'TH001', 'instagram' => 'TH001', 'twitter' => 'TH001',
            'teman' => 'TH003', 'keluarga' => 'TH003',
            'guru' => 'TH004', 'sekolah' => 'TH004',
            'pameran' => 'TH005',
            'banner' => 'TH006', 'spanduk' => 'TH006',
            'flyer' => 'TH007', 'brosur' => 'TH007',
            'radio' => 'TH008', 'tv' => 'TH008', 'televisi' => 'TH008'
        ];
        
        foreach ($mappings as $keyword => $id) {
            if (strpos($source, $keyword) !== false) return $id;
        }
        
        $found = TahuStih::where('sumber', 'LIKE', "%{$source}%")->first();
        return $found ? $found->id : 'TH009'; // Default Lainnya
    }
}
