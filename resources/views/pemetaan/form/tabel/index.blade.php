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

        /* Suggestions dropdown styles */
        .suggestions-dropdown {
            scrollbar-width: thin;
            scrollbar-color: #e5e7eb #f9fafb;
        }
        
        .suggestions-dropdown::-webkit-scrollbar {
            width: 6px;
        }
        
        .suggestions-dropdown::-webkit-scrollbar-track {
            background: #f9fafb;
            border-radius: 3px;
        }
        
        .suggestions-dropdown::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }
        
        .suggestions-dropdown::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .suggestion-item {
            transition: all 0.15s ease-in-out;
        }

        .suggestion-item:hover {
            background-color: #f3f4f6 !important;
            transform: translateX(2px);
        }

        .suggestion-item.active {
            background-color: #e5e7eb !important;
        }

        mark {
            background-color: #fef3c7 !important;
            color: #92400e;
            padding: 1px 2px;
            border-radius: 2px;
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

    @if (session('import_errors') && count(session('import_errors')) > 0)
        <div class="mb-6 p-4 rounded-lg bg-yellow-50 border-l-4 border-yellow-500 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                </div>
                <div class="ml-3 w-full">
                    <h3 class="text-sm font-medium text-yellow-800 mb-2">
                        Ditemukan masalah pada beberapa baris data:
                    </h3>
                    <div class="text-sm text-yellow-700 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach (session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
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
                    Cari Nama / NIM
                </label>
                <div class="relative group">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        class="w-full px-4 py-3.5 pl-12 bg-white border-2 border-gray-200 rounded-xl text-sm
                               focus:border-primary focus:ring-4 focus:ring-primary/10 focus:outline-none
                               transition-all duration-200 placeholder-gray-400
                               hover:border-gray-300 hover:shadow-sm" 
                        placeholder="Ketik nama atau NIM..."
                        autocomplete="off"
                        oninput="handleSearchInput(this)"
                        onkeydown="handleSearchKeydown(event)"
                        onfocus="showSearchSuggestions()"
                        onblur="hideSearchSuggestions()">
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
                    <!-- Search Suggestions Dropdown -->
                    <div id="searchSuggestions" class="suggestions-dropdown absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden z-20 max-h-60 overflow-y-auto">
                        <div class="p-2 text-sm text-gray-500 text-center">
                            Mulai mengetik untuk mencari...
                        </div>
                    </div>
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
                <label for="province_search" class="block text-sm font-semibold text-gray-700">
                    <i class="fas fa-map-marker-alt text-primary mr-1.5"></i>
                    Provinsi
                </label>
                <div class="relative group">
                    <input type="hidden" name="province_code" id="province_code" value="{{ request('province_code') }}">
                    <input type="text" id="province_search" 
                        value="{{ request('province_code') ? $provinces->where('province_code', request('province_code'))->first()?->province : '' }}"
                        class="w-full px-4 py-3.5 pl-12 bg-white border-2 border-gray-200 rounded-xl text-sm
                               focus:border-primary focus:ring-4 focus:ring-primary/10 focus:outline-none
                               transition-all duration-200 placeholder-gray-400
                               hover:border-gray-300 hover:shadow-sm"
                        placeholder="Ketik nama provinsi..."
                        autocomplete="off"
                        oninput="handleProvinceSearch(this)"
                        onkeydown="handleProvinceKeydown(event)"
                        onfocus="showProvinceSuggestions()"
                        onblur="hideProvinceSuggestions()">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    @if(request('province_code'))
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-primary/10 text-primary">
                                Aktif
                            </span>
                        </div>
                    @endif
                    <!-- Province Suggestions Dropdown -->
                    <div id="provinceSuggestions" class="suggestions-dropdown absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden z-20 max-h-60 overflow-y-auto">
                        <div class="p-2 text-sm text-gray-500 text-center">
                            Mulai mengetik untuk mencari provinsi...
                        </div>
                    </div>
                </div>
            </div>

            <!-- City Filter -->
            <div class="space-y-2">
                <label for="city_search" class="block text-sm font-semibold text-gray-700">
                    <i class="fas fa-building text-primary mr-1.5"></i>
                    Kabupaten/Kota
                </label>
                <div class="relative group">
                    <input type="hidden" name="city_code" id="city_code" value="{{ request('city_code') }}">
                    <input type="text" id="city_search" 
                        value="{{ request('city_code') && isset($cities) && count($cities) > 0 ? $cities->where('city_code', request('city_code'))->first()?->city : '' }}"
                        class="w-full px-4 py-3.5 pl-12 bg-white border-2 border-gray-200 rounded-xl text-sm
                               focus:border-primary focus:ring-4 focus:ring-primary/10 focus:outline-none
                               transition-all duration-200 placeholder-gray-400
                               hover:border-gray-300 hover:shadow-sm {{ !request('province_code') ? 'disabled:bg-gray-50 disabled:text-gray-400' : '' }}"
                        placeholder="{{ !request('province_code') ? 'Pilih provinsi dulu...' : 'Ketik nama kota/kabupaten...' }}"
                        autocomplete="off"
                        {{ !request('province_code') ? 'disabled' : '' }}
                        oninput="handleCitySearch(this)"
                        onkeydown="handleCityKeydown(event)"
                        onfocus="showCitySuggestions()"
                        onblur="hideCitySuggestions()">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    @if(request('city_code'))
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-primary/10 text-primary">
                                Aktif
                            </span>
                        </div>
                    @endif
                    <!-- City Suggestions Dropdown -->
                    <div id="citySuggestions" class="suggestions-dropdown absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden z-20 max-h-60 overflow-y-auto">
                        <div class="p-2 text-sm text-gray-500 text-center">
                            Pilih provinsi dulu...
                        </div>
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
        // Data for filters
        const provinces = @json($provinces->map(function($p) { return ['code' => $p->province_code, 'name' => $p->province]; }));
        const allCities = @json($allCities->map(function($c) { return ['code' => $c->city_code, 'name' => $c->city, 'province_code' => $c->province_code]; }));
        let searchData = [];
        let currentFocus = -1;

        // Search variables
        let searchTimeout = null;
        let provinceTimeout = null;
        let cityTimeout = null;

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

            // Load search suggestions
            loadSearchSuggestions();
        });

        // Load search suggestions from server
        function loadSearchSuggestions() {
            fetch('{{ route("pemetaan.form.tabel") }}?get_suggestions=1')
                .then(response => response.json())
                .then(data => {
                    searchData = data.suggestions || [];
                })
                .catch(error => {
                    console.error('Error loading suggestions:', error);
                });
        }

        // Search Input Functions
        function handleSearchInput(input) {
            clearTimeout(searchTimeout);
            const query = input.value.toLowerCase().trim();
            
            if (query.length < 1) {
                hideSearchSuggestions();
                return;
            }

            searchTimeout = setTimeout(() => {
                showSearchSuggestions(query);
            }, 300);
        }

        function showSearchSuggestions(query = '') {
            const dropdown = document.getElementById('searchSuggestions');
            
            if (!query) {
                dropdown.innerHTML = '<div class="p-2 text-sm text-gray-500 text-center">Mulai mengetik untuk mencari...</div>';
                dropdown.classList.remove('hidden');
                return;
            }

            // Filter suggestions based on query
            const filtered = searchData.filter(item => 
                item.nama_siswa.toLowerCase().includes(query) ||
                item.nim.toLowerCase().includes(query)
            ).slice(0, 10);

            if (filtered.length === 0) {
                dropdown.innerHTML = '<div class="p-2 text-sm text-gray-500 text-center">Tidak ada hasil ditemukan</div>';
            } else {
                dropdown.innerHTML = filtered.map((item, index) => 
                    `<div class="suggestion-item p-2 cursor-pointer hover:bg-gray-100 border-b border-gray-100 last:border-b-0" 
                          onclick="selectSearchSuggestion('${item.nama_siswa}', '${item.nim}')" 
                          data-index="${index}">
                        <div class="font-medium text-sm">${highlightMatch(item.nama_siswa, query)}</div>
                        <div class="text-xs text-gray-500">NIM: ${highlightMatch(item.nim, query)}</div>
                    </div>`
                ).join('');
            }
            
            dropdown.classList.remove('hidden');
            currentFocus = -1;
        }

        function selectSearchSuggestion(name, nim) {
            const input = document.getElementById('search');
            input.value = name;
            hideSearchSuggestions();
            document.getElementById('filterForm').submit();
        }

        function hideSearchSuggestions() {
            setTimeout(() => {
                document.getElementById('searchSuggestions').classList.add('hidden');
            }, 200);
        }

        function handleSearchKeydown(event) {
            const dropdown = document.getElementById('searchSuggestions');
            const items = dropdown.querySelectorAll('[data-index]');
            
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                currentFocus = currentFocus < items.length - 1 ? currentFocus + 1 : 0;
                setActive(items);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                currentFocus = currentFocus > 0 ? currentFocus - 1 : items.length - 1;
                setActive(items);
            } else if (event.key === 'Enter') {
                event.preventDefault();
                if (currentFocus > -1 && items[currentFocus]) {
                    items[currentFocus].click();
                } else {
                    document.getElementById('filterForm').submit();
                }
            } else if (event.key === 'Escape') {
                hideSearchSuggestions();
            }
        }

        // Province Input Functions
        function handleProvinceSearch(input) {
            clearTimeout(provinceTimeout);
            const query = input.value.toLowerCase().trim();
            
            if (query.length < 1) {
                hideProvinceSuggestions();
                // Clear province selection if input is empty
                document.getElementById('province_code').value = '';
                // Clear and disable city
                clearCitySelection();
                return;
            }

            provinceTimeout = setTimeout(() => {
                showProvinceSuggestions(query);
            }, 300);
        }

        function showProvinceSuggestions(query = '') {
            const dropdown = document.getElementById('provinceSuggestions');
            
            if (!query) {
                dropdown.innerHTML = '<div class="p-2 text-sm text-gray-500 text-center">Mulai mengetik untuk mencari provinsi...</div>';
                dropdown.classList.remove('hidden');
                return;
            }

            // Filter provinces based on query
            const filtered = provinces.filter(province => 
                province.name.toLowerCase().includes(query)
            ).slice(0, 10);

            if (filtered.length === 0) {
                dropdown.innerHTML = '<div class="p-2 text-sm text-gray-500 text-center">Tidak ada provinsi ditemukan</div>';
            } else {
                dropdown.innerHTML = filtered.map((province, index) => 
                    `<div class="suggestion-item p-2 cursor-pointer hover:bg-gray-100 border-b border-gray-100 last:border-b-0" 
                          onclick="selectProvince('${province.code}', '${province.name}')" 
                          data-index="${index}">
                        <div class="font-medium text-sm">${highlightMatch(province.name, query)}</div>
                    </div>`
                ).join('');
            }
            
            dropdown.classList.remove('hidden');
            currentFocus = -1;
        }

        function selectProvince(code, name) {
            document.getElementById('province_code').value = code;
            document.getElementById('province_search').value = name;
            hideProvinceSuggestions();
            
            // Clear city selection and enable city input
            clearCitySelection();
            enableCityInput();
            
            // Submit form to update cities
            document.getElementById('filterForm').submit();
        }

        function hideProvinceSuggestions() {
            setTimeout(() => {
                document.getElementById('provinceSuggestions').classList.add('hidden');
            }, 200);
        }

        function handleProvinceKeydown(event) {
            const dropdown = document.getElementById('provinceSuggestions');
            const items = dropdown.querySelectorAll('[data-index]');
            
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                currentFocus = currentFocus < items.length - 1 ? currentFocus + 1 : 0;
                setActive(items);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                currentFocus = currentFocus > 0 ? currentFocus - 1 : items.length - 1;
                setActive(items);
            } else if (event.key === 'Enter') {
                event.preventDefault();
                if (currentFocus > -1 && items[currentFocus]) {
                    items[currentFocus].click();
                }
            } else if (event.key === 'Escape') {
                hideProvinceSuggestions();
            }
        }

        // City Input Functions
        function handleCitySearch(input) {
            clearTimeout(cityTimeout);
            const query = input.value.toLowerCase().trim();
            const selectedProvinceCode = document.getElementById('province_code').value;
            
            if (!selectedProvinceCode) {
                hideCitySuggestions();
                return;
            }
            
            if (query.length < 1) {
                hideCitySuggestions();
                document.getElementById('city_code').value = '';
                return;
            }

            cityTimeout = setTimeout(() => {
                showCitySuggestions(query, selectedProvinceCode);
            }, 300);
        }

        function showCitySuggestions(query = '', provinceCode = '') {
            const dropdown = document.getElementById('citySuggestions');
            
            if (!provinceCode) {
                dropdown.innerHTML = '<div class="p-2 text-sm text-gray-500 text-center">Pilih provinsi dulu...</div>';
                dropdown.classList.remove('hidden');
                return;
            }
            
            if (!query) {
                dropdown.innerHTML = '<div class="p-2 text-sm text-gray-500 text-center">Mulai mengetik untuk mencari kota...</div>';
                dropdown.classList.remove('hidden');
                return;
            }

            // Filter cities based on query and province
            const filtered = allCities.filter(city => 
                city.province_code === provinceCode && 
                city.name.toLowerCase().includes(query)
            ).slice(0, 10);

            if (filtered.length === 0) {
                dropdown.innerHTML = '<div class="p-2 text-sm text-gray-500 text-center">Tidak ada kota ditemukan</div>';
            } else {
                dropdown.innerHTML = filtered.map((city, index) => 
                    `<div class="suggestion-item p-2 cursor-pointer hover:bg-gray-100 border-b border-gray-100 last:border-b-0" 
                          onclick="selectCity('${city.code}', '${city.name}')" 
                          data-index="${index}">
                        <div class="font-medium text-sm">${highlightMatch(city.name, query)}</div>
                    </div>`
                ).join('');
            }
            
            dropdown.classList.remove('hidden');
            currentFocus = -1;
        }

        function selectCity(code, name) {
            document.getElementById('city_code').value = code;
            document.getElementById('city_search').value = name;
            hideCitySuggestions();
            document.getElementById('filterForm').submit();
        }

        function hideCitySuggestions() {
            setTimeout(() => {
                document.getElementById('citySuggestions').classList.add('hidden');
            }, 200);
        }

        function handleCityKeydown(event) {
            const dropdown = document.getElementById('citySuggestions');
            const items = dropdown.querySelectorAll('[data-index]');
            
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                currentFocus = currentFocus < items.length - 1 ? currentFocus + 1 : 0;
                setActive(items);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                currentFocus = currentFocus > 0 ? currentFocus - 1 : items.length - 1;
                setActive(items);
            } else if (event.key === 'Enter') {
                event.preventDefault();
                if (currentFocus > -1 && items[currentFocus]) {
                    items[currentFocus].click();
                }
            } else if (event.key === 'Escape') {
                hideCitySuggestions();
            }
        }

        // Helper Functions
        function clearCitySelection() {
            document.getElementById('city_code').value = '';
            document.getElementById('city_search').value = '';
            document.getElementById('city_search').disabled = true;
            document.getElementById('city_search').placeholder = 'Pilih provinsi dulu...';
        }

        function enableCityInput() {
            const cityInput = document.getElementById('city_search');
            cityInput.disabled = false;
            cityInput.placeholder = 'Ketik nama kota/kabupaten...';
        }

        function setActive(items) {
            items.forEach((item, index) => {
                if (index === currentFocus) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
        }

        function highlightMatch(text, query) {
            if (!query) return text;
            const regex = new RegExp(`(${query})`, 'gi');
            return text.replace(regex, '<mark class="bg-yellow-200 px-0.5">$1</mark>');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const searchContainer = document.getElementById('search').closest('.relative');
            const provinceContainer = document.getElementById('province_search').closest('.relative');
            const cityContainer = document.getElementById('city_search').closest('.relative');
            
            if (!searchContainer.contains(event.target)) {
                hideSearchSuggestions();
            }
            if (!provinceContainer.contains(event.target)) {
                hideProvinceSuggestions();
            }
            if (!cityContainer.contains(event.target)) {
                hideCitySuggestions();
            }
        });

        // Initialize city input state on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (!document.getElementById('province_code').value) {
                clearCitySelection();
            } else {
                enableCityInput();
            }
        });
    </script>
    @endpush

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-200">
        <div class="p-6">
            <!-- Sort Control -->
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <label class="text-sm font-medium text-gray-700">Urutkan berdasarkan:</label>
                    <div class="flex items-center gap-2">
                        <select id="sortColumn" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="nim" {{ request('sort') == 'nim' ? 'selected' : '' }}>NIM</option>
                            <option value="nama_siswa" {{ request('sort') == 'nama_siswa' ? 'selected' : '' }}>Nama Siswa</option>
                            <option value="tahun_lulus" {{ request('sort') == 'tahun_lulus' ? 'selected' : '' }}>Tahun Lulus</option>
                            <option value="asal_sekolah" {{ request('sort') == 'asal_sekolah' ? 'selected' : '' }}>Asal Sekolah</option>
                            <option value="tanggal_daftar" {{ request('sort') == 'tanggal_daftar' ? 'selected' : '' }}>Tanggal Daftar</option>
                            <option value="tahu_stih" {{ request('sort') == 'tahu_stih' ? 'selected' : '' }}>Tahu STIH Darimana</option>
                            <option value="jenis_beasiswa" {{ request('sort') == 'jenis_beasiswa' ? 'selected' : '' }}>Jenis Beasiswa</option>
                        </select>
                        <button id="sortDirection" onclick="toggleSort()" 
                                class="px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors flex items-center gap-2">
                            <i id="sortIcon" class="fas {{ request('direction') == 'desc' ? 'fa-sort-down' : 'fa-sort-up' }}"></i>
                            <span id="sortText">{{ request('direction') == 'desc' ? 'Z-A' : 'A-Z' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Siswa</th>
                            <th>Tahun Lulus</th>
                            <th>Asal Sekolah</th>
                            <th>Provinsi</th>
                            <th>Kota</th>
                            <th>Tanggal Daftar</th>
                            <th>Tahu STIH Darimana</th>
                            <th>Beasiswa</th>
                            <th>Jenis Beasiswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mahasiswas as $index => $mahasiswa)
                            <tr>
                                <td>{{ $mahasiswas->firstItem() + $index }}</td>
                                <td>{{ $mahasiswa->nim }}</td>
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
                                    @if($mahasiswa->sumber_beasiswa == 'beasiswa' && $mahasiswa->jenis_beasiswa)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $mahasiswa->jenis_beasiswa == '100%' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $mahasiswa->jenis_beasiswa }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
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
                                <td colspan="12" class="text-center py-8 text-gray-500">
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

        // Universal Sort Functions
        function toggleSort() {
            const sortColumn = document.getElementById('sortColumn').value;
            const currentDirection = '{{ request('direction', 'asc') }}';
            const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            
            // Update button appearance
            const sortIcon = document.getElementById('sortIcon');
            const sortText = document.getElementById('sortText');
            
            if (newDirection === 'desc') {
                sortIcon.className = 'fas fa-sort-down';
                sortText.textContent = 'Z-A';
            } else {
                sortIcon.className = 'fas fa-sort-up';
                sortText.textContent = 'A-Z';
            }
            
            // Build URL with current filters and new sort
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort', sortColumn);
            currentUrl.searchParams.set('direction', newDirection);
            
            // Redirect to sorted page
            window.location.href = currentUrl.toString();
        }

        // Handle sort column change
        document.getElementById('sortColumn').addEventListener('change', function() {
            const sortColumn = this.value;
            const currentDirection = '{{ request('direction', 'asc') }}';
            
            // Build URL with current filters and new sort column
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort', sortColumn);
            currentUrl.searchParams.set('direction', currentDirection);
            
            // Redirect to sorted page
            window.location.href = currentUrl.toString();
        });
    </script>
@endsection

