<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('provinces.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error('provinces.csv not found!');
            return;
        }

        $handle = fopen($csvFile, 'r');
        
        // Skip header row
        $header = fgetcsv($handle);
        
        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 4) {
                Province::updateOrCreate(
                    ['province_code' => $row[2]],
                    [
                        'region_code' => $row[1],
                        'province' => $row[3],
                    ]
                );
                $count++;
            }
        }
        
        fclose($handle);
        
        $this->command->info("Imported {$count} provinces successfully!");
    }
}
