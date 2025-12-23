<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Province;
use App\Models\City;
use App\Models\HighSchool;
use App\Models\VocationalHighSchool;
use App\Models\MadrasahAliyah;
use App\Models\TahuStih;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    public function index()
    {
        return view('pemetaan.import');
    }

    public function process(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:xlsx|max:20480', // 20MB max per file (or total logic in JS)
        ], [
            'files.required' => 'Mohon pilih minimal satu file excel',
            'files.*.mimes' => 'Format file harus .xlsx',
        ]);

        $files = $request->file('files');
        
        $totalImported = 0;
        $totalSkipped = 0;
        $allErrors = [];

        DB::beginTransaction();

        try {
            foreach ($files as $index => $file) {
                $importer = new \App\Imports\MahasiswaImport();
                
                \Maatwebsite\Excel\Facades\Excel::import($importer, $file);
                
                $totalImported += $importer->importedCount;
                $totalSkipped += $importer->skippedCount;
                
                // Prefix errors with filename
                $fileName = $file->getClientOriginalName();
                foreach ($importer->errors as $err) {
                    $allErrors[] = "[{$fileName}] " . $err;
                }
            }

            DB::commit();

            $message = "Import selesai! Total Berhasil: {$totalImported}, Gagal: {$totalSkipped}";
            
            return redirect()->route('pemetaan.form.tabel')->with([
                'success' => $message,
                'import_errors' => $allErrors,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /* ===================== HELPERS ===================== */

    private function mapProvinsi(string $provinsiName): ?string
    {
        // 1. Direct Match (LIKE %...%)
        $province = Province::where('province', 'LIKE', "%{$provinsiName}%")
            ->orWhere('province', 'LIKE', str_replace(' ', '%', $provinsiName))
            ->first();
        
        if ($province) return $province->province_code;

        // 2. Word-based Match (All words in input must exist in DB field)
        $words = explode(' ', $provinsiName);
        if (count($words) > 1) {
            $province = Province::where(function($query) use ($words) {
                foreach ($words as $word) {
                    if (strlen($word) > 2) { // Skip short words like 'di', 'ke' if any
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
        // 1. Direct Match
        $city = City::where('province_code', $provinceCode)
            ->where(function($query) use ($kotaName) {
                $query->where('city', 'LIKE', "%{$kotaName}%")
                      ->orWhere('city', 'LIKE', str_replace(' ', '%', $kotaName));
            })
            ->first();

        if ($city) return $city->city_code;

        // 2. Word-based Match
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

    private function extractNpsn(string $value): ?string
    {
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

    private function mapTahuStih(string $source): ?string
    {
        // Normalisasi input terlebih dahulu
        $source = strtolower(trim($source));
        
        // Mapping berdasarkan kata kunci
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
        
        // Cek mapping langsung
        foreach ($mappings as $keyword => $id) {
            if (strpos($source, $keyword) !== false) {
                return $id;
            }
        }
        
        // Jika tidak cocok, coba cari di database
        $found = TahuStih::where('sumber', 'LIKE', "%{$source}%")->first();
        if ($found) {
            return $found->id;
        }
        
        // Default ke 'Lainnya'
        return 'TH009';
    }

    /**
     * Preview file data before import
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,json,txt|max:5120',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        $rows = [];

        try {
            // ============================
            // PARSE FILE
            // ============================
            if (in_array($extension, ['csv', 'txt'])) {
                $csv = array_map('str_getcsv', file($file));
                $header = array_map('trim', array_shift($csv));

                // Take only first 5 rows for preview
                $previewRows = array_slice($csv, 0, 5);
                
                foreach ($previewRows as $row) {
                    if (count($row) === count($header)) {
                        $rows[] = array_combine($header, $row);
                    }
                }

                $columns = $header;

            } else {
                $jsonData = json_decode(file_get_contents($file), true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json(['success' => false, 'message' => 'Format JSON tidak valid']);
                }

                // Take only first 5 rows for preview
                $rows = array_slice($jsonData, 0, 5);
                $columns = !empty($rows) ? array_keys($rows[0]) : [];
            }

            if (empty($rows)) {
                return response()->json(['success' => false, 'message' => 'File tidak berisi data yang valid']);
            }

            return response()->json([
                'success' => true,
                'data' => $rows,
                'columns' => $columns,
                'total_preview' => count($rows)
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
