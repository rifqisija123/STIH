<?php

namespace Database\Seeders;

use App\Models\TahuStih;
use Illuminate\Database\Seeder;

class TahuStihSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('tahu_stihs.csv');

        if (!file_exists($csvFile)) {
            $this->command->error('tahu_stihs.csv not found!');
            return;
        }

        $handle = fopen($csvFile, 'r');

        // Skip header
        fgetcsv($handle);

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 2 && !empty($row[0])) {
                TahuStih::updateOrCreate(
                    ['id' => $row[0]],
                    [
                        'sumber' => $row[1],
                    ]
                );
                $count++;
            }
        }

        fclose($handle);

        $this->command->info("Imported {$count} tahu_stihs successfully!");
    }
}
