@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8 space-y-8">

    <!-- HERO -->
    <div class="bg-gradient-to-r from-[#8f1722] to-[#b12834] rounded-2xl p-8 text-white shadow-lg mt-20">
        <h1 class="text-2xl font-bold mb-1">Selamat Datang, Admin!</h1>
        <p class="text-sm text-white/90">
            Dashboard Overview – Sistem Penerimaan Mahasiswa Baru STIH
        </p>
    </div>

    <!-- STAT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $stats = [
                ['Total Siswa', $totalSiswa, 'bg-red-50 text-red-600', 'fas fa-users'],
                ['Aktivitas Promosi', $aktivitasPromosi, 'bg-green-50 text-green-600', 'fas fa-bullhorn'],
                ['Sekolah Terdata', $sekolahTerdata, 'bg-blue-50 text-blue-600', 'fas fa-school'],
                ['Non Beasiswa', $nonBeasiswa, 'bg-yellow-50 text-yellow-600', 'fas fa-user-graduate'],
            ];
        @endphp

        @foreach($stats as [$title, $value, $style, $icon])
            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition">
                <p class="text-xs uppercase tracking-wide text-gray-500">{{ $title }}</p>
                <div class="flex items-center justify-between mt-2">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $value }}</h2>
                    <div class="w-10 h-10 rounded-lg {{ $style }} flex items-center justify-center">
                        <i class="{{ $icon }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- AKSES CEPAT -->
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-bolt mr-2 text-blue-600"></i>
            Akses Cepat
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('pemetaan.form') }}" class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 transition">
                        <i class="fas fa-edit"></i>
                    </div>
                </div>
                <h3 class="font-semibold text-gray-800">Input Pemetaan</h3>
                <p class="text-sm text-gray-500 mt-1">Tambah data mahasiswa</p>
            </a>

            <a href="{{ route('pemetaan.form.tabel') }}" class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center group-hover:bg-green-100 transition">
                        <i class="fas fa-table"></i>
                    </div>
                </div>
                <h3 class="font-semibold text-gray-800">Data Pemetaan</h3>
                <p class="text-sm text-gray-500 mt-1">Lihat seluruh data</p>
            </a>
            
            <a href="{{ route('pemetaan.import') }}" class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-100 transition">
                        <i class="fas fa-file-import"></i>
                    </div>
                </div>
                <h3 class="font-semibold text-gray-800">Import Data</h3>
                <p class="text-sm text-gray-500 mt-1">Import Data CSV/JSON</p>
            </a>

            <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition group cursor-pointer">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-100 transition">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <h3 class="font-semibold text-gray-800">Statistik</h3>
                <p class="text-sm text-gray-500 mt-1">Analisis pendaftaran</p>
            </div>

        </div>
    </div>

    <!-- AKTIVITAS TERBARU -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="font-semibold text-gray-800">
                <i class="fas fa-clock mr-2 text-blue-600"></i>
                Aktivitas Terbaru
            </h2>
        </div>

        <div class="divide-y">
            @forelse($aktivitasTerbaru as $item)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mr-3">
                            <i class="fas fa-user-plus text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $item->nama_siswa }}</p>
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-id-card mr-1"></i>
                                NIM: {{ $item->nim }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <span class="text-xs px-3 py-1 rounded-full
                            {{ $item->sumber_beasiswa == 'beasiswa'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-blue-100 text-blue-700' }}">
                            <i class="fas {{ $item->sumber_beasiswa == 'beasiswa' ? 'fa-star' : 'fa-close' }} mr-1"></i>
                            {{ ucfirst($item->sumber_beasiswa) }}
                        </span>

                        <span class="text-sm text-gray-400">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ $item->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="px-6 py-4 text-gray-500 text-sm text-center">
                    <i class="fas fa-inbox text-2xl mb-2 block text-gray-300"></i>
                    Belum ada data pemetaan
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
