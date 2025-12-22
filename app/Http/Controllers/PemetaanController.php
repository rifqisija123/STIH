<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\HighSchool;
use App\Models\MadrasahAliyah;
use App\Models\Mahasiswa;
use App\Models\Province;
use App\Models\TahuStih;
use App\Models\VocationalHighSchool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemetaanController extends Controller
{
    /**
     * Display the pemetaan form page.
     */
    public function form()
    {
        $provinces = Province::orderBy('province')->get();
        $tahuStihOptions = TahuStih::all();

        return view('pemetaan.form.index', [
            'provinces' => $provinces,
            'tahuStihOptions' => $tahuStihOptions,
        ]);
    }

    /**
     * Get schools with pagination for Select2 AJAX.
     */
    public function getSchools(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $searchTerm = $search ? "%{$search}%" : '%';

        $query = "
            SELECT DISTINCT CONCAT(nama, ' [', npsn, ']') as school_text, npsn, nama, kode_provinsi, kode_kabkot
            FROM (
                SELECT npsn, nama, kode_provinsi, kode_kabkot FROM high_school WHERE npsn IS NOT NULL AND nama IS NOT NULL AND nama LIKE ?
                UNION
                SELECT npsn, nama, kode_provinsi, kode_kabkot FROM madrasah_aliyah WHERE npsn IS NOT NULL AND nama IS NOT NULL AND nama LIKE ?
                UNION
                SELECT npsn, nama, kode_provinsi, kode_kabkot FROM vocational_high_school WHERE npsn IS NOT NULL AND nama IS NOT NULL AND nama LIKE ?
            ) AS all_schools
            ORDER BY nama ASC
            LIMIT ? OFFSET ?
        ";

        $bindings = $search
            ? [$searchTerm, $searchTerm, $searchTerm, $perPage, $offset]
            : ['%', '%', '%', $perPage, $offset];

        $results = DB::select($query, $bindings);

        $formattedResults = collect($results)->map(function ($item) {
            return [
                'id' => $item->school_text,
                'text' => $item->school_text,
                'province_code' => $item->kode_provinsi,
                'city_code' => $item->kode_kabkot,
            ];
        })->values();

        $countQuery = "
            SELECT COUNT(DISTINCT CONCAT(nama, ' [', npsn, ']')) as total
            FROM (
                SELECT npsn, nama FROM high_school WHERE npsn IS NOT NULL AND nama IS NOT NULL AND nama LIKE ?
                UNION
                SELECT npsn, nama FROM madrasah_aliyah WHERE npsn IS NOT NULL AND nama IS NOT NULL AND nama LIKE ?
                UNION
                SELECT npsn, nama FROM vocational_high_school WHERE npsn IS NOT NULL AND nama IS NOT NULL AND nama LIKE ?
            ) AS all_schools
        ";

        $countBindings = $search ? [$searchTerm, $searchTerm, $searchTerm] : ['%', '%', '%'];
        $totalResult = DB::selectOne($countQuery, $countBindings);
        $total = $totalResult->total ?? 0;
        $hasMore = ($offset + $perPage) < $total;

        return response()->json([
            'results' => $formattedResults,
            'pagination' => [
                'more' => $hasMore,
            ],
        ]);
    }

    /**
     * Get cities by province code (API endpoint).
     */
    public function getCities(Request $request)
    {
        try {
            $provinceCode = $request->query('province_code');

            if (! $provinceCode) {
                return response()->json([], 200);
            }

            $cities = City::where('province_code', $provinceCode)
                ->orderBy('city')
                ->get()
                ->map(function ($city) {
                    return [
                        'city_code' => $city->city_code,
                        'city' => $city->city,
                    ];
                });

            return response()->json($cities->values()->all(), 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch cities',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store the pemetaan form data.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:10|unique:mahasiswas,nisn',
            'nama_siswa' => 'required|string|max:255',
            'tahun_lulus' => 'required|integer|digits:4|min:2000|max:'.(date('Y') + 1),
            'asal_sekolah' => 'required|string',
            'provinsi' => 'required|string',
            'kota' => 'required|string',
            'tanggal_daftar' => 'required|date',
            'tahu_stih_darimana' => 'required|string',
            'sumber_beasiswa' => 'required|in:beasiswa,non_beasiswa',
        ]);

        // Extract NPSN from "Name [NPSN]" format
        if (preg_match('/\[(\d+)\]$/', $validated['asal_sekolah'], $matches)) {
            $validated['asal_sekolah'] = $matches[1];
        }

        Mahasiswa::create($validated);

        return redirect()->route('pemetaan.form')->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Display the tabel data pemetaan.
     */
    public function tabel(Request $request)
    {
        $query = Mahasiswa::with(['province', 'city', 'highSchool', 'madrasahAliyah', 'vocationalHighSchool', 'tahuStih'])
            ->orderBy('created_at', 'desc');

        // Filter by Search (Name or NISN)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_siswa', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Filter by Date
        if ($request->filled('date')) {
            $query->whereDate('tanggal_daftar', $request->date);
        }

        // Filter by Beasiswa
        if ($request->filled('beasiswa') && $request->beasiswa !== 'all') {
            $query->where('sumber_beasiswa', $request->beasiswa);
        }

        // Filter by Province
        if ($request->filled('province_code')) {
            $query->where('provinsi', $request->province_code);
        }

        // Filter by City
        if ($request->filled('city_code')) {
            $query->where('kota', $request->city_code);
        }

        $mahasiswas = $query->paginate(10)->withQueryString();

        // Pre-load all schools for all mahasiswas to avoid N+1 queries
        $npsnList = $mahasiswas->pluck('asal_sekolah')->filter()->unique()->toArray();
        
        // Load all schools in one query per type
        $highSchools = HighSchool::whereIn('npsn', $npsnList)->get()->keyBy('npsn');
        $madrasahAliyah = MadrasahAliyah::whereIn('npsn', $npsnList)->get()->keyBy('npsn');
        $vocationalHighSchools = VocationalHighSchool::whereIn('npsn', $npsnList)->get()->keyBy('npsn');
        
        // Combine all schools into one collection for easy lookup
        $allSchools = $highSchools->merge($madrasahAliyah)->merge($vocationalHighSchools);

        // Get Provinces for filter
        $provinces = Province::orderBy('province')->get();

        // Get Cities if province is selected
        $cities = [];
        if ($request->filled('province_code')) {
            $cities = City::where('province_code', $request->province_code)
                ->orderBy('city')
                ->get();
        }

        return view('pemetaan.form.tabel.index', [
            'mahasiswas' => $mahasiswas,
            'provinces' => $provinces,
            'cities' => $cities,
            'allSchools' => $allSchools,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $provinces = Province::orderBy('province')->get();
        $tahuStihOptions = TahuStih::all();

        // Get school name for selected value
        $mahasiswa->load(['highSchool', 'madrasahAliyah', 'vocationalHighSchool']);
        $school = $mahasiswa->highSchool ?? $mahasiswa->madrasahAliyah ?? $mahasiswa->vocationalHighSchool;

        // Fallback: Try to find school manually if relationship failed
        if (! $school && $mahasiswa->asal_sekolah) {
            $npsn = $mahasiswa->asal_sekolah;

            $school = HighSchool::where('npsn', $npsn)->first();
            if (! $school) {
                $school = MadrasahAliyah::where('npsn', $npsn)->first();
            }
            if (! $school) {
                $school = VocationalHighSchool::where('npsn', $npsn)->first();
            }
        }

        $selectedSchool = '';
        $schoolProvinceCode = null;
        $schoolCityCode = null;

        if ($school) {
            $selectedSchool = (is_object($school) && isset($school->nama))
                ? $school->nama.' ['.$school->npsn.']'
                : '';

            if (is_object($school)) {
                $schoolProvinceCode = $school->kode_provinsi ?? $school->province_code ?? null;
                $schoolCityCode = $school->kode_kabkot ?? $school->city_code ?? null;
            }
        } elseif ($mahasiswa->asal_sekolah) {
            $selectedSchool = 'Sekolah ['.$mahasiswa->asal_sekolah.']';
        }

        $defaultProvinsi = $mahasiswa->provinsi ?: $schoolProvinceCode;
        $defaultKota = $mahasiswa->kota ?: $schoolCityCode;

        $cities = [];
        if ($defaultProvinsi) {
            $cities = City::where('province_code', $defaultProvinsi)
                ->orderBy('city')
                ->get();
        }

        return view('pemetaan.form.edit', [
            'mahasiswa' => $mahasiswa,
            'provinces' => $provinces,
            'tahuStihOptions' => $tahuStihOptions,
            'selectedSchool' => $selectedSchool,
            'cities' => $cities,
            'defaultProvinsi' => $defaultProvinsi,
            'defaultKota' => $defaultKota,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        $validated = $request->validate([
            'nisn' => 'required|string|max:10|unique:mahasiswas,nisn,'.$id,
            'nama_siswa' => 'required|string|max:255',
            'tahun_lulus' => 'required|integer|digits:4|min:2000|max:'.(date('Y') + 1),
            'asal_sekolah' => 'required|string',
            'provinsi' => 'required|string',
            'kota' => 'required|string',
            'tanggal_daftar' => 'required|date',
            'tahu_stih_darimana' => 'required|string',
            'sumber_beasiswa' => 'required|in:beasiswa,non_beasiswa',
        ]);

        // Extract NPSN from "Name [NPSN]" format
        if (preg_match('/\[(\d+)\]$/', $validated['asal_sekolah'], $matches)) {
            $validated['asal_sekolah'] = $matches[1];
        }

        $mahasiswa->update($validated);

        return redirect()->route('pemetaan.form.tabel')->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->delete();

        return redirect()->route('pemetaan.form.tabel')->with('success', 'Data berhasil dihapus!');
    }

    /**
     * Show import form
     */
    public function showImport()
    {
        return view('pemetaan.import');
    }

    /**
     * Process file import (CSV/JSON)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,json,txt|max:5120', // Max 5MB
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $imported = 0;
        $errors = [];

        try {
            if (in_array($extension, ['csv', 'txt'])) {
                $imported = $this->importCsv($file, $errors);
            } elseif ($extension === 'json') {
                $imported = $this->importJson($file, $errors);
            }

            $message = "Berhasil import {$imported} data.";
            if (!empty($errors)) {
                $message .= " Terdapat " . count($errors) . " error.";
            }

            return back()->with('success', $message)->with('import_errors', $errors);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Import CSV file
     */
    private function importCsv($file, &$errors)
    {
        $imported = 0;
        $path = $file->getRealPath();
        
        if (($handle = fopen($path, 'r')) !== FALSE) {
            $header = fgetcsv($handle, 1000, ','); // Read header
            $rowNumber = 1;

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $rowNumber++;
                
                if (count($data) != count($header)) {
                    $errors[] = "Baris {$rowNumber}: Jumlah kolom tidak sesuai";
                    continue;
                }

                $row = array_combine($header, $data);
                
                if ($this->validateAndSaveRow($row, $rowNumber, $errors)) {
                    $imported++;
                }
            }
            fclose($handle);
        }

        return $imported;
    }

    /**
     * Import JSON file
     */
    private function importJson($file, &$errors)
    {
        $imported = 0;
        $content = file_get_contents($file->getRealPath());
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Format JSON tidak valid: ' . json_last_error_msg());
        }

        foreach ($data as $index => $row) {
            $rowNumber = $index + 1;
            
            if ($this->validateAndSaveRow($row, $rowNumber, $errors)) {
                $imported++;
            }
        }

        return $imported;
    }

    /**
     * Validate and save a single row
     */
    private function validateAndSaveRow($row, $rowNumber, &$errors)
    {
        try {
            // Required fields mapping
            $fieldMapping = [
                'nama' => ['nama', 'name', 'nama_mahasiswa'],
                'nim' => ['nim', 'nomor_induk'],
                'kode_provinsi' => ['kode_provinsi', 'provinsi_code', 'province_code'],
                'kode_kabkot' => ['kode_kabkot', 'kabkot_code', 'city_code'],
                'asal_sekolah' => ['asal_sekolah', 'sekolah', 'school', 'npsn'],
                'tahun_masuk' => ['tahun_masuk', 'year', 'tahun'],
                'tanggal_daftar' => ['tanggal_daftar', 'tanggal', 'date'],
                'tahu_stih_darimana' => ['tahu_stih_darimana', 'sumber_info', 'source'],
                'sumber_beasiswa' => ['sumber_beasiswa', 'beasiswa', 'scholarship']
            ];

            $validated = [];
            
            // Map fields
            foreach ($fieldMapping as $dbField => $possibleKeys) {
                $value = null;
                foreach ($possibleKeys as $key) {
                    if (isset($row[$key]) && !empty($row[$key])) {
                        $value = $row[$key];
                        break;
                    }
                }
                
                if ($value !== null) {
                    $validated[$dbField] = $value;
                }
            }

            // Validate required fields
            $required = ['nama', 'nim', 'kode_provinsi', 'kode_kabkot', 'asal_sekolah', 'tahun_masuk', 'tanggal_daftar', 'tahu_stih_darimana', 'sumber_beasiswa'];
            
            foreach ($required as $field) {
                if (!isset($validated[$field]) || empty($validated[$field])) {
                    $errors[] = "Baris {$rowNumber}: Field '{$field}' harus diisi";
                    return false;
                }
            }

            // Validate date format
            if (!strtotime($validated['tanggal_daftar'])) {
                $errors[] = "Baris {$rowNumber}: Format tanggal tidak valid";
                return false;
            } else {
                $validated['tanggal_daftar'] = date('Y-m-d', strtotime($validated['tanggal_daftar']));
            }

            // Validate sumber_beasiswa
            if (!in_array($validated['sumber_beasiswa'], ['beasiswa', 'non_beasiswa'])) {
                $errors[] = "Baris {$rowNumber}: Sumber beasiswa harus 'beasiswa' atau 'non_beasiswa'";
                return false;
            }

            // Check if NIM already exists
            if (Mahasiswa::where('nim', $validated['nim'])->exists()) {
                $errors[] = "Baris {$rowNumber}: NIM '{$validated['nim']}' sudah ada";
                return false;
            }

            // Create the record
            Mahasiswa::create($validated);
            return true;

        } catch (\Exception $e) {
            $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
            return false;
        }
    }
}
