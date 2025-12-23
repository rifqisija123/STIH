<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\District;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('districts.csv');
        $csvData = array_map('str_getcsv', file($csvFile));
        $header = array_shift($csvData); // Remove header

        $chunks = array_chunk($csvData, 1000); // Process in chunks to avoid memory issues

        foreach ($chunks as $chunk) {
            $insertData = [];
            foreach ($chunk as $row) {
                // Ensure row has enough columns (prevent index error)
                if (count($row) < 5) continue;

                // Mapping based on CSV structure:
                // id, province_code, city_code, district_code, district, created_at, updated_at
                $insertData[] = [
                    'province_code' => $row[1],
                    'city_code'     => $row[2],
                    'district_code' => $row[3],
                    'district'      => $row[4],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
            
            // Use insertOrIgnore to handle duplicates gracefully
            DB::table('districts')->insertOrIgnore($insertData);
        }
    }
}
