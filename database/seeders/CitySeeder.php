<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('cities.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error('cities.csv not found!');
            return;
        }

        $handle = fopen($csvFile, 'r');
        
        // Skip header row
        $header = fgetcsv($handle);
        
        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 5) {
                City::updateOrCreate(
                    ['city_code' => $row[3]],
                    [
                        'region_code' => $row[1],
                        'province_code' => $row[2],
                        'city' => $row[4],
                    ]
                );
                $count++;
            }
        }
        
        fclose($handle);
        
        $this->command->info("Imported {$count} cities successfully!");
    }
}
