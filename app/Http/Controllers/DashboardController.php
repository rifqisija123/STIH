<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Province;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSiswa = Mahasiswa::count();

        $aktivitasPromosi = Mahasiswa::whereNotNull('tahu_stih_darimana')->count();

        $sekolahTerdata = Mahasiswa::distinct('asal_sekolah')->count('asal_sekolah');

        $nonBeasiswa = Mahasiswa::where('sumber_beasiswa', 'non_beasiswa')->count();

        $aktivitasTerbaru = Mahasiswa::with(['province', 'city'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('pages.dashboard', compact(
            'totalSiswa',
            'aktivitasPromosi',
            'sekolahTerdata',
            'nonBeasiswa',
            'aktivitasTerbaru'
        ));
    }
}
