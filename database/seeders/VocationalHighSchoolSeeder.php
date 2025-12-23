<?php

namespace Database\Seeders;

use App\Models\VocationalHighSchool;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VocationalHighSchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('vocational_high_school.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error('vocational_high_school.csv not found!');
            return;
        }

        $handle = fopen($csvFile, 'r');
        
        // Get header row
        $header = fgetcsv($handle);
        
        $count = 0;
        $batchSize = 500;
        $batch = [];
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 39 && !empty($row[1])) { // Ensure NPSN exists
                $batch[] = [
                    'npsn' => $row[1],
                    'nama' => $row[2],
                    'bentuk_pendidikan' => $row[3] ?: null,
                    'jalur_pendidikan' => $row[4] ?: null,
                    'jenjang_pendidikan' => $row[5] ?: null,
                    'kementerian_pembina' => $row[6] ?: null,
                    'status_satuan_pendidikan' => $row[7] ?: null,
                    'akreditasi' => $row[8] ?: null,
                    'jenis_pendidikan' => $row[9] ?: null,
                    'sk_pendirian_sekolah_nomor' => $row[10] ?: null,
                    'sk_pendirian_sekolah_tanggal' => $this->parseDate($row[11]),
                    'sk_izin_operasional_nomor' => $row[12] ?: null,
                    'sk_izin_operasional_tanggal' => $this->parseDate($row[13]),
                    'yayasan_nama' => $row[14] ?: null,
                    'yayasan_npyp' => $row[15] ?: null,
                    'alamat_jalan' => $row[16] ?: null,
                    'alamat_rt' => $row[17] ?: null,
                    'alamat_rw' => $row[18] ?: null,
                    'alamat_nama_dusun' => $row[19] ?: null,
                    'alamat_nama_desa' => $row[20] ?: null,
                    'kode_deskel' => $row[21] ?: null,
                    'alamat_nama_kecamatan' => $row[22] ?: null,
                    'kode_kecamatan' => $row[23] ?: null,
                    'alamat_nama_kabupaten' => $row[24] ?: null,
                    'kode_kabkot' => $row[25] ?: null,
                    'alamat_nama_provinsi' => $row[26] ?: null,
                    'kode_provinsi' => $row[27] ?: null,
                    'alamat_nama_negara' => $row[28] ?: null,
                    'luas_tanah_milik' => $row[29] ?: null,
                    'sumber_listrik' => $row[30] ?: null,
                    'akses_internet' => $row[31] ?: null,
                    'nomor_fax' => $row[32] ?: null,
                    'nomor_telepon' => $row[33] ?: null,
                    'email' => $row[34] ?: null,
                    'website' => $row[35] ?: null,
                    'koordinat' => $row[36] ?: null,
                    'lintang' => is_numeric($row[37]) ? $row[37] : null,
                    'bujur' => is_numeric($row[38]) ? $row[38] : null,
                ];
                
                $count++;
                
                // Insert in batches for performance
                if (count($batch) >= $batchSize) {
                    $this->insertBatch($batch);
                    $batch = [];
                    $this->command->info("Processed {$count} records...");
                }
            }
        }
        
        // Insert remaining records
        if (!empty($batch)) {
            $this->insertBatch($batch);
        }
        
        fclose($handle);
        
        $this->command->info("Imported {$count} vocational high schools successfully!");
    }
    
    private function insertBatch(array $batch): void
    {
        foreach ($batch as $data) {
            VocationalHighSchool::updateOrCreate(
                ['npsn' => $data['npsn']],
                $data
            );
        }
    }
    
    private function parseDate(?string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }
        
        // Try to parse d/m/Y format
        $date = \DateTime::createFromFormat('d/m/Y', $dateString);
        if ($date) {
            return $date->format('Y-m-d');
        }
        
        // Try to parse Y-m-d format
        $date = \DateTime::createFromFormat('Y-m-d', $dateString);
        if ($date) {
            return $date->format('Y-m-d');
        }
        
        return null;
    }
}
