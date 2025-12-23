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

    /**
     * Header mapping: Excel headers → database columns
     */
    private function getHeaderMapping(): array
    {
        return [
            'nim' => 'nim',
            'nama' => 'nama',
            'email' => 'email',
            'nisn' => 'nisn',
            'jenis_kelamin' => 'jenis_kelamin',
            'jenis kelamin' => 'jenis_kelamin',
            'nomor_telepon' => 'nomor_telepon',
            'nomor telepon' => 'nomor_telepon',
            'tempat_lahir' => 'tempat_lahir',
            'tempat lahir' => 'tempat_lahir',
            'tanggal_lahir' => 'tanggal_lahir',
            'tanggal lahir' => 'tanggal_lahir',
            'agama' => 'agama',
            'alamat' => 'alamat',
            'rt' => 'rt',
            'rw' => 'rw',
            'dusun' => 'dusun',
            'kelurahan' => 'kelurahan',
            'kecamatan' => 'kecamatan',
            'kode_pos' => 'kode_pos',
            'kode pos' => 'kode_pos',
            'program_studi' => 'program_studi',
            'program studi' => 'program_studi',
            'mengetahui_stih_dari' => 'tahu_stih_darimana',
            'mengetahui stih dari' => 'tahu_stih_darimana',
            'sma' => 'asal_sekolah',
            'beasiswa' => 'sumber_beasiswa',
            'jenis_beasiswa' => 'jenis_beasiswa',
            'jenis beasiswa' => 'jenis_beasiswa',
            'angkatan' => 'angkatan',
            'kewarganegaraan' => 'kewarganegaraan',
            'jenis_pendaftaran' => 'jenis_pendaftaran',
            'jenis pendaftaran' => 'jenis_pendaftaran',
            'jalur_pendaftaran' => 'jalur_pendaftaran',
            'jalur pendaftaran' => 'jalur_pendaftaran',
            'tanggal_masuk_kuliah' => 'tanggal_masuk_kuliah',
            'tanggal masuk kuliah' => 'tanggal_masuk_kuliah',
            'mulai_semester' => 'mulai_semester',
            'mulai semester' => 'mulai_semester',
            'code_religion' => 'code_religion',
            'district_code' => 'district_code',
            'village_code' => 'village_code',
            'code_stihs' => 'code_stihs',
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Excel row index (starts at 2 because of header)
            $rowIndex = $index + 2;

            // Convert to array and normalize headers
            $data = $this->normalizeRow($row->toArray());

            if ($this->validateAndSaveRow($data, $rowIndex)) {
                $this->importedCount++;
            } else {
                $this->skippedCount++;
            }
        }
    }

    /**
     * Normalize row data by mapping Excel headers to DB columns
     */
    private function normalizeRow(array $row): array
    {
        $mapping = $this->getHeaderMapping();
        $normalized = [];

        foreach ($row as $header => $value) {
            $headerLower = strtolower(trim(str_replace('_', ' ', $header)));

            // Check direct mapping
            if (isset($mapping[$headerLower])) {
                $normalized[$mapping[$headerLower]] = $value;
            } elseif (isset($mapping[$header])) {
                $normalized[$mapping[$header]] = $value;
            }
        }

        return $normalized;
    }

    private function validateAndSaveRow(array $data, int $rowNumber): bool
    {
        try {
            // ============================
            // PRESERVE LEADING ZEROS
            // ============================
            // Convert numeric values to strings for fields that need leading zeros
            $data['nisn'] = $this->preserveLeadingZeros($data['nisn'] ?? null);
            $data['nim'] = $this->preserveLeadingZeros($data['nim'] ?? null);
            $data['nomor_telepon'] = $this->preserveLeadingZeros($data['nomor_telepon'] ?? null);
            $data['rt'] = $this->preserveLeadingZeros($data['rt'] ?? null);
            $data['rw'] = $this->preserveLeadingZeros($data['rw'] ?? null);

            // Transform dates
            $data['tanggal_lahir'] = $this->transformDate($data['tanggal_lahir'] ?? null);
            $data['tanggal_masuk_kuliah'] = $this->transformDate($data['tanggal_masuk_kuliah'] ?? null);

            // Transform jenis kelamin
            $data['jenis_kelamin'] = $this->normalizeJenisKelamin($data['jenis_kelamin'] ?? null);

            // ============================
            // VALIDATION RULES
            // ============================
            $rules = [
                // Required fields
                'nim' => 'nullable|string|size:10|regex:/^[0-9]+$/',
                'nama' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'nisn' => 'nullable|string|size:10|regex:/^[0-9]+$/',
                'jenis_kelamin' => 'nullable|in:L,P',

                // Nullable fields
                'nomor_telepon' => 'nullable|string|max:20',
                'tempat_lahir' => 'nullable|string|max:255',
                'tanggal_lahir' => 'nullable|date',
                'agama' => 'nullable|string|max:50',
                'alamat' => 'nullable|string',
                'rt' => 'nullable|string|max:5',
                'rw' => 'nullable|string|max:5',
                'dusun' => 'nullable|string|max:255',
                'kelurahan' => 'nullable|string|max:255',
                'kecamatan' => 'nullable|string|max:255',
                'kode_pos' => 'nullable|string|max:10',
                'program_studi' => 'nullable|string|max:255',
                'asal_sekolah' => 'nullable|string',
                'tahu_stih_darimana' => 'nullable|string',
                'sumber_beasiswa' => 'nullable|string',
                'jenis_beasiswa' => 'nullable|string',
                'angkatan' => 'nullable|string',
                'kewarganegaraan' => 'nullable|string|max:50',
                'jenis_pendaftaran' => 'nullable|string|max:100',
                'jalur_pendaftaran' => 'nullable|string|max:100',
                'tanggal_masuk_kuliah' => 'nullable|date',
                'mulai_semester' => 'nullable|string|max:50',
                'code_religion' => 'nullable|string|max:20',
                'district_code' => 'nullable|string|max:20',
                'village_code' => 'nullable|string|max:20',
                'code_stihs' => 'nullable|string|max:20',
            ];

            $messages = [
                'nim.size' => 'NIM harus tepat 10 digit',
                'nim.regex' => 'NIM harus berupa angka',
                'email.email' => 'Format email tidak valid',
                'nisn.size' => 'NISN harus tepat 10 digit',
                'nisn.regex' => 'NISN harus berupa angka',
                'jenis_kelamin.in' => 'Jenis kelamin harus L (Laki-laki) atau P (Perempuan)',
            ];

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                $this->errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                return false;
            }

            // ============================
            // VALIDATE UNIQUE CONSTRAINTS
            // ============================
            if (Mahasiswa::where('nim', $data['nim'])->exists()) {
                $this->errors[] = "Baris {$rowNumber}: NIM '{$data['nim']}' sudah terdaftar di database";
                return false;
            }

            if (Mahasiswa::where('email', $data['email'])->exists()) {
                $this->errors[] = "Baris {$rowNumber}: Email '{$data['email']}' sudah terdaftar di database";
                return false;
            }

            // ============================
            // PROCESS OPTIONAL MAPPINGS
            // ============================

            // Map province if provided
            if (!empty($data['provinsi'])) {
                $provinceCode = $this->mapProvinsi($data['provinsi']);
                if ($provinceCode) {
                    $data['provinsi'] = $provinceCode;
                }
            }

            // Map city if provided
            if (!empty($data['kota']) && !empty($data['provinsi'])) {
                $cityCode = $this->mapKota($data['kota'], $data['provinsi']);
                if ($cityCode) {
                    $data['kota'] = $cityCode;
                }
            }

            // Extract NPSN from school name if format "Name [NPSN]"
            if (!empty($data['asal_sekolah'])) {
                $npsn = $this->extractNpsn($data['asal_sekolah']);
                if ($npsn) {
                    $data['asal_sekolah'] = $npsn;
                }
            }

            // Map tahu_stih_darimana to code_stihs if provided
            if (!empty($data['tahu_stih_darimana']) && empty($data['code_stihs'])) {
                $data['code_stihs'] = $this->mapTahuStih($data['tahu_stih_darimana']);
            }

            // ============================
            // SAVE TO DATABASE
            // ============================
            Mahasiswa::create([
                'nim' => $data['nim'],
                'nama' => $data['nama'],
                'email' => $data['email'],
                'nisn' => $data['nisn'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'nomor_telepon' => $data['nomor_telepon'] ?? null,
                'tempat_lahir' => $data['tempat_lahir'] ?? null,
                'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                'agama' => $data['agama'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'rt' => $data['rt'] ?? null,
                'rw' => $data['rw'] ?? null,
                'dusun' => $data['dusun'] ?? null,
                'kelurahan' => $data['kelurahan'] ?? null,
                'kecamatan' => $data['kecamatan'] ?? null,
                'kode_pos' => $data['kode_pos'] ?? null,
                'program_studi' => $data['program_studi'] ?? null,
                'asal_sekolah' => $data['asal_sekolah'] ?? null,
                'provinsi' => $data['provinsi'] ?? null,
                'kota' => $data['kota'] ?? null,
                'tahu_stih_darimana' => $data['code_stihs'] ?? null,
                'sumber_beasiswa' => $this->normalizeSumberBeasiswa($data['sumber_beasiswa'] ?? null),
                'jenis_beasiswa' => $data['jenis_beasiswa'] ?? null,
                'angkatan' => $data['angkatan'] ?? null,
                'kewarganegaraan' => $data['kewarganegaraan'] ?? null,
                'jenis_pendaftaran' => $data['jenis_pendaftaran'] ?? null,
                'jalur_pendaftaran' => $data['jalur_pendaftaran'] ?? null,
                'tanggal_masuk_kuliah' => $data['tanggal_masuk_kuliah'] ?? null,
                'mulai_semester' => $data['mulai_semester'] ?? null,
                'code_religion' => $data['code_religion'] ?? null,
                'district_code' => $data['district_code'] ?? null,
                'village_code' => $data['village_code'] ?? null,
                'code_stihs' => $data['code_stihs'] ?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            $this->errors[] = "Baris {$rowNumber}: Terjadi kesalahan internal ({$e->getMessage()})";
            return false;
        }
    }

    /**
     * Preserve leading zeros by converting to string with proper padding
     */
    private function preserveLeadingZeros($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // If it's a float (Excel sometimes reads as float), convert to int first
        if (is_float($value)) {
            $value = (int) $value;
        }

        // Convert to string
        return (string) $value;
    }

    /**
     * Normalize jenis kelamin to L or P
     */
    private function normalizeJenisKelamin($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = strtoupper(trim($value));

        if (in_array($value, ['L', 'LAKI-LAKI', 'LAKI', 'MALE', 'M', 'PRIA'])) {
            return 'L';
        }

        if (in_array($value, ['P', 'PEREMPUAN', 'FEMALE', 'F', 'WANITA', 'W'])) {
            return 'P';
        }

        return $value; // Return as-is, let validation handle it
    }

    /**
     * Normalize sumber beasiswa value
     */
    private function normalizeSumberBeasiswa($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = strtolower(trim($value));

        if (in_array($value, ['ya', 'yes', 'beasiswa', '1', 'true'])) {
            return 'beasiswa';
        }

        if (in_array($value, ['tidak', 'no', 'non_beasiswa', 'non beasiswa', '0', 'false'])) {
            return 'non_beasiswa';
        }

        return $value;
    }

    private function transformDate($value)
    {
        if (!$value) return null;
        try {
            // Excel dates are sometimes integers (days since 1900-01-01)
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
            $province = Province::where(function ($query) use ($words) {
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
            ->where(function ($query) use ($kotaName) {
                $query->where('city', 'LIKE', "%{$kotaName}%")
                    ->orWhere('city', 'LIKE', str_replace(' ', '%', $kotaName));
            })->first();
        if ($city) return $city->city_code;

        // 2. Word-based
        $words = explode(' ', $kotaName);
        if (count($words) > 1) {
            $city = City::where('province_code', $provinceCode)
                ->where(function ($query) use ($words) {
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
            'media sosial' => 'TH001',
            'sosial_media' => 'TH001',
            'facebook' => 'TH001',
            'instagram' => 'TH001',
            'twitter' => 'TH001',
            'teman' => 'TH003',
            'keluarga' => 'TH003',
            'guru' => 'TH004',
            'sekolah' => 'TH004',
            'pameran' => 'TH005',
            'banner' => 'TH006',
            'spanduk' => 'TH006',
            'flyer' => 'TH007',
            'brosur' => 'TH007',
            'radio' => 'TH008',
            'tv' => 'TH008',
            'televisi' => 'TH008'
        ];

        foreach ($mappings as $keyword => $id) {
            if (strpos($source, $keyword) !== false) return $id;
        }

        $found = TahuStih::where('sumber', 'LIKE', "%{$source}%")->first();
        return $found ? $found->id : 'TH009'; // Default Lainnya
    }
}
