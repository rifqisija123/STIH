@extends('layouts.app')

@section('title', 'Tabel Data Pemetaan - STIH')

@push('styles')
    <style>
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 10px 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.875rem;
        }
        
        th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: 0.8125rem;
        }
        
        tr:hover {
            background-color: #f9fafb;
        }
    </style>
@endpush

@section('content')
    <!-- Page Heading Card -->
    <div class="bg-gradient-to-r from-primary via-[#a02835] to-[#821620] rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 p-6 mb-6 mt-20">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-1">Tabel Data Pemetaan</h1>
                <p class="text-white text-opacity-90 text-sm">Daftar data siswa yang telah diinput</p>
            </div>
            <div class="flex items-center gap-3 mt-4 sm:mt-0">
                <a href="{{ route('pemetaan.import') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm text-white text-sm font-medium rounded-lg shadow-sm transition duration-200">
                    <i class="fas fa-upload text-sm text-white mr-2"></i> 
                    Import Data
                </a>
                <a href="{{ route('pemetaan.form') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm text-white text-sm font-medium rounded-lg shadow-sm transition duration-200">
                    <i class="fas fa-plus text-sm text-white mr-2"></i> 
                    Tambah Data
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 border-l-4 border-green-500">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                <span class="text-green-700 text-sm">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-500">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                <span class="text-red-700 text-sm">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl shadow-md border border-gray-100 p-8 mb-6">
        <div class="flex items-center mb-6">
            <div class="flex items-center justify-center w-10 h-10 bg-primary rounded-xl mr-3 shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Filter Data</h3>
                <p class="text-sm text-gray-500">Cari dan filter data mahasiswa</p>
            </div>
        </div>
        
        <form action="{{ route('pemetaan.form.tabel') }}" method="GET" id="filterForm" class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Search Input -->
            <div class="space-y-2">
                <label for="search" class="block text-sm font-semibold text-gray-700">
                    <i class="fas fa-search text-primary mr-1.5"></i>
                    Cari Nama / NISN
                </label>
                <div class="relative group">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        class="w-full px-4 py-3.5 pl-12 bg-white border-2 border-gray-200 rounded-xl text-sm
                               focus:border-primary focus:ring-4 focus:ring-primary/10 focus:outline-none
                               transition-all duration-200 placeholder-gray-400
                               hover:border-gray-300 hover:shadow-sm" 
                        placeholder="Ketik nama atau NISN..."
                        oninput="debounceSubmit()">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    @if(request('search'))
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-primary/10 text-primary">
                                Aktif
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Date Filter -->
            <div class="space-y-2">
                <label for="date" class="block text-sm font-semibold text-gray-700">
                    <i class="fas fa-calendar-alt text-primary mr-1.5"></i>
                    Tanggal Daftar
                </label>
                <div class="relative group">
                    <input type="text" name="date" id="date" value="{{ request('date') }}" 
                        class="w-full px-4 py-3.5 pl-12 bg-white border-2 border-gray-200 rounded-xl text-sm
                               focus:border-primary focus:ring-4 focus:ring-primary/10 focus:outline-none
                               transition-all duration-200 placeholder-gray-400 cursor-pointer
                               hover:border-gray-300 hover:shadow-sm"
                        placeholder="Pilih tanggal pendaftaran">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    @if(request('date'))
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-primary/10 text-primary">
                                Aktif
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Province Filter -->
            <div class="space-y-2">
                <label for="province_code" class="block text-sm font-semibold text-gray-700">
                    <i class="fas fa-map-marker-alt text-primary mr-1.5"></i>
                    Provinsi
                </label>
                <div class="relative group">
                    <select name="province_code" id="province_code" onchange="document.getElementById('city_code').value=''; this.form.submit()"
                        class="w-full px-4 py-3.5 pl-12 bg-white border-2 border-gray-200 rounded-xl text-sm
                               focus:border-primary focus:ring-4 focus:ring-primary/10 focus:outline-none
                               transition-all duration-200 cursor-pointer appearance-none
                               hover:border-gray-300 hover:shadow-sm">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->province_code }}" {{ request('province_code') == $province->province_code ? 'selected' : '' }}>
                                {{ $province->province }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- City Filter -->
            <div class="space-y-2">
                <label for="city_code" class="block text-sm font-semibold text-gray-700">
                    <i class="fas fa-building text-primary mr-1.5"></i>
                    Kabupaten/Kota
                </label>
                <div class="relative group">
                    <select name="city_code" id="city_code" onchange="this.form.submit()"
                        class="w-full px-4 py-3.5 pl-12 bg-white border-2 border-gray-200 rounded-xl text-sm
                               focus:border-primary focus:ring-4 focus:ring-primary/10 focus:outline-none
                               transition-all duration-200 cursor-pointer appearance-none
                               hover:border-gray-300 hover:shadow-sm disabled:bg-gray-50 disabled:text-gray-400"
                        {{ !request('province_code') ? 'disabled' : '' }}>
                        <option value="">Semua Kota</option>
                        @if(isset($cities) && count($cities) > 0)
                            @foreach($cities as $city)
                                <option value="{{ $city->city_code }}" {{ request('city_code') == $city->city_code ? 'selected' : '' }}>
                                    {{ $city->city }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Beasiswa Filter -->
            <div class="space-y-2">
                <label for="beasiswa" class="block text-sm font-semibold text-gray-700">
                    <i class="fas fa-graduation-cap text-primary mr-1.5"></i>
                    Status Beasiswa
                </label>
                <div class="relative group">
                    <select name="beasiswa" id="beasiswa" onchange="this.form.submit()"
                        class="w-full px-4 py-3.5 pl-12 bg-white border-2 border-gray-200 rounded-xl text-sm
                               focus:border-primary focus:ring-4 focus:ring-primary/10 focus:outline-none
                               transition-all duration-200 cursor-pointer appearance-none
                               hover:border-gray-300 hover:shadow-sm">
                        <option value="all" {{ request('beasiswa') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="beasiswa" {{ request('beasiswa') == 'beasiswa' ? 'selected' : '' }}>Beasiswa</option>
                        <option value="non_beasiswa" {{ request('beasiswa') == 'non_beasiswa' ? 'selected' : '' }}>Non Beasiswa</option>
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Reset Button -->
        @if(request('search') || request('date') || (request('beasiswa') && request('beasiswa') !== 'all') || request('province_code') || request('city_code'))
            <div class="mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('pemetaan.form.tabel') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-50 to-red-100 text-red-700 rounded-xl text-sm font-semibold hover:from-red-100 hover:to-red-200 transition-all duration-200 shadow-sm hover:shadow border border-red-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Hapus Semua Filter
                </a>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Flatpickr
            flatpickr("#date", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                allowInput: true,
                onChange: function(selectedDates, dateStr, instance) {
                    document.getElementById('filterForm').submit();
                }
            });
        });

        // Debounce function for search input
        let timeout = null;
        function debounceSubmit() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                document.getElementById('filterForm').submit();
            }, 800); // 800ms delay
        }
    </script>
    @endpush

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-200">
        <div class="p-6">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Tahun Lulus</th>
                            <th>Asal Sekolah</th>
                            <th>Provinsi</th>
                            <th>Kota</th>
                            <th>Tanggal Daftar</th>
                            <th>Tahu STIH Darimana</th>
                            <th>Beasiswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mahasiswas as $index => $mahasiswa)
                            <tr>
                                <td>{{ $mahasiswas->firstItem() + $index }}</td>
                                <td>{{ $mahasiswa->nisn }}</td>
                                <td>{{ $mahasiswa->nama_siswa }}</td>
                                <td>{{ $mahasiswa->tahun_lulus }}</td>
                                <td>
                                    @php
                                        $npsn = $mahasiswa->asal_sekolah;
                                        // Try relationship first
                                        $school = $mahasiswa->highSchool ?? $mahasiswa->madrasahAliyah ?? $mahasiswa->vocationalHighSchool;
                                        
                                        // If not found, try from pre-loaded schools collection
                                        if (!$school && $npsn && isset($allSchools) && $allSchools->has($npsn)) {
                                            $school = $allSchools->get($npsn);
                                        }
                                    @endphp
                                    @if($school && isset($school->nama))
                                        {{ $school->nama }}
                                    @elseif($npsn)
                                        <span class="text-gray-400 text-sm" title="Sekolah dengan NPSN {{ $npsn }} tidak ditemukan di database">
                                            {{ $npsn }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>{{ $mahasiswa->province->province ?? '-' }}</td>
                                <td>{{ $mahasiswa->city->city ?? '-' }}</td>
                                <td>{{ $mahasiswa->tanggal_daftar->format('d/m/Y') }}</td>
                                <td>{{ $mahasiswa->tahuStih->sumber ?? '-' }}</td>
                                <td>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $mahasiswa->sumber_beasiswa == 'beasiswa' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $mahasiswa->sumber_beasiswa == 'beasiswa' ? 'Beasiswa' : 'Non Beasiswa' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a 
                                            href="{{ route('pemetaan.form.edit', $mahasiswa->id) }}" 
                                            class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <button 
                                            onclick="deleteMahasiswa({{ $mahasiswa->id }})"
                                            class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-8 text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>Belum ada data yang diinput</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($mahasiswas->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $mahasiswas->firstItem() }} sampai {{ $mahasiswas->lastItem() }} dari {{ $mahasiswas->total() }} data
                </div>
                <div class="flex items-center gap-2">
                    @if($mahasiswas->onFirstPage())
                        <span class="px-3 py-2 text-sm text-gray-400 cursor-not-allowed">Sebelumnya</span>
                    @else
                        @php
                            $prevParams = request()->all();
                            $prevParams['page'] = $mahasiswas->currentPage() - 1;
                        @endphp
                        <a href="{{ route('pemetaan.form.tabel', $prevParams) }}" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">Sebelumnya</a>
                    @endif
                    
                    @php
                        $start = max(1, $mahasiswas->currentPage() - 2);
                        $end = min($mahasiswas->lastPage(), $mahasiswas->currentPage() + 2);
                        
                        if ($mahasiswas->currentPage() <= 3) {
                            $end = min(5, $mahasiswas->lastPage());
                        }
                        if ($mahasiswas->currentPage() >= $mahasiswas->lastPage() - 2) {
                            $start = max(1, $mahasiswas->lastPage() - 4);
                        }
                    @endphp

                    @if($start > 1)
                        @php
                            $firstParams = request()->all();
                            $firstParams['page'] = 1;
                        @endphp
                        <a href="{{ route('pemetaan.form.tabel', $firstParams) }}" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">1</a>
                        @if($start > 2)
                            <span class="px-2 py-2 text-sm text-gray-500">...</span>
                        @endif
                    @endif

                    @foreach($mahasiswas->getUrlRange($start, $end) as $page => $url)
                        @if($page == $mahasiswas->currentPage())
                            <span class="px-3 py-2 text-sm bg-primary text-white rounded-lg font-semibold">{{ $page }}</span>
                        @else
                            @php
                                $pageParams = request()->all();
                                $pageParams['page'] = $page;
                            @endphp
                            <a href="{{ route('pemetaan.form.tabel', $pageParams) }}" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($end < $mahasiswas->lastPage())
                        @if($end < $mahasiswas->lastPage() - 1)
                            <span class="px-2 py-2 text-sm text-gray-500">...</span>
                        @endif
                        @php
                            $lastParams = request()->all();
                            $lastParams['page'] = $mahasiswas->lastPage();
                        @endphp
                        <a href="{{ route('pemetaan.form.tabel', $lastParams) }}" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">{{ $mahasiswas->lastPage() }}</a>
                    @endif
                    
                    @if($mahasiswas->hasMorePages())
                        @php
                            $nextParams = request()->all();
                            $nextParams['page'] = $mahasiswas->currentPage() + 1;
                        @endphp
                        <a href="{{ route('pemetaan.form.tabel', $nextParams) }}" class="px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">Selanjutnya</a>
                    @else
                        <span class="px-3 py-2 text-sm text-gray-400 cursor-not-allowed">Selanjutnya</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Hapus Data</h3>
            <p class="text-sm text-gray-600 text-center mb-6">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button 
                    onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function deleteMahasiswa(id) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            form.action = `/pemetaan/form/${id}`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
@endsection

