@extends('layouts.app')

@section('title', 'Edit Data Pemetaan - STIH')

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        .form-input {
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(178, 32, 44, 0.1);
            border-color: #b2202c;
        }
        
        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 48px;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal;
            padding: 0 0 0 12px;
            color: #374151;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 48px;
            top: 0;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #b2202c;
            box-shadow: 0 0 0 3px rgba(178, 32, 44, 0.1);
        }
        
        .select2-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            z-index: 9999;
        }
        
        .select2-container--default .select2-results__option {
            padding: 8px 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #b2202c;
        }
        
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem;
        }
        
        /* Fix dropdown width to prevent text wrapping */
        .select2-container {
            width: 100% !important;
        }
        
        .select2-dropdown {
            min-width: 100%;
            width: auto !important;
        }
        
        .select2-results__option--load-more {
            text-align: center !important;
            padding: 10px !important;
        }
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 0.875rem;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 48px;
            top: 0;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #b2202c;
            box-shadow: 0 0 0 3px rgba(178, 32, 44, 0.1);
        }
        
        .select2-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            z-index: 9999;
        }
        
        .select2-container--default .select2-results__option {
            padding: 8px 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 0.875rem;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #b2202c;
        }
        
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem;
        }
        
        /* Fix dropdown width to prevent text wrapping */
        .select2-container {
            width: 100% !important;
        }
        
        .select2-dropdown {
            min-width: 100%;
            width: auto !important;
        }
        
        .select2-results__option--load-more {
            text-align: center !important;
            padding: 10px !important;
        }
    </style>
@endpush

@section('content')
    <!-- Page Heading Card -->
    <div class="bg-gradient-to-r from-primary via-[#a02835] to-[#821620] rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 p-6 mb-6 mt-20">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-1">Edit Data Pemetaan</h1>
                <p class="text-white text-opacity-90 text-sm">Ubah data siswa dengan lengkap dan benar</p>
            </div>
            <div class="flex items-center gap-3 mt-4 sm:mt-0">
                <a href="{{ route('pemetaan.form.tabel') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm text-white text-sm font-medium rounded-lg shadow-sm transition duration-200">
                    <i class="fas fa-table text-sm text-white mr-2"></i> 
                    Lihat Data
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('pemetaan.form.update', $mahasiswa->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 gap-6 mb-6">
            
            <!-- Card 1: Data Siswa -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-200">
                <div class="p-6 border-b border-gray-200">
                    <h6 class="text-lg font-bold text-primary">Data Siswa</h6>
                    <p class="text-sm text-gray-500 mt-1">Informasi dasar siswa</p>
                </div>
                <div class="p-6 space-y-5">
                    <!-- NIM -->
                    <div>
                        <label for="nim" class="block text-sm font-semibold text-gray-700 mb-2">
                            NIM <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nim" 
                            name="nim" 
                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary @error('nim') border-red-500 @enderror" 
                            placeholder="Masukkan NIM"
                            value="{{ old('nim', $mahasiswa->nim) }}"
                            required>
                        @error('nim')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Siswa -->
                    <div>
                        <label for="nama_siswa" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Siswa <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nama_siswa" 
                            name="nama_siswa" 
                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary @error('nama_siswa') border-red-500 @enderror" 
                            placeholder="Masukkan Nama Lengkap Siswa"
                            value="{{ old('nama_siswa', $mahasiswa->nama_siswa) }}"
                            required>
                        @error('nama_siswa')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tahun Lulus -->
                    <div>
                        <label for="tahun_lulus" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tahun Lulus <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="tahun_lulus" 
                            name="tahun_lulus" 
                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary @error('tahun_lulus') border-red-500 @enderror" 
                            placeholder="Contoh: 2024"
                            value="{{ old('tahun_lulus', $mahasiswa->tahun_lulus) }}"
                            min="2000"
                            max="2099"
                            required>
                        @error('tahun_lulus')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Card 2: Data Sekolah -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-200">
                <div class="p-6 border-b border-gray-200">
                    <h6 class="text-lg font-bold text-primary">Data Sekolah</h6>
                    <p class="text-sm text-gray-500 mt-1">Informasi sekolah asal</p>
                </div>
                <div class="p-6 space-y-5">
                    <!-- Asal Sekolah -->
                    <div>
                        <label for="asal_sekolah" class="block text-sm font-semibold text-gray-700 mb-2">
                            Asal Sekolah <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="asal_sekolah" 
                            name="asal_sekolah" 
                            class="form-input w-full @error('asal_sekolah') border-red-500 @enderror" 
                            required>
                            @if(old('asal_sekolah', $selectedSchool))
                                <option value="{{ old('asal_sekolah', $selectedSchool) }}" selected>{{ old('asal_sekolah', $selectedSchool) }}</option>
                            @endif
                        </select>
                        @error('asal_sekolah')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Provinsi -->
                    <div>
                        <label for="provinsi" class="block text-sm font-semibold text-gray-700 mb-2">
                            Provinsi <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="provinsi" 
                            name="provinsi" 
                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary @error('provinsi') border-red-500 @enderror" 
                            required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->province_code }}" {{ old('provinsi', $defaultProvinsi ?? $mahasiswa->provinsi) == $province->province_code ? 'selected' : '' }}>
                                    {{ $province->province }}
                                </option>
                            @endforeach
                        </select>
                        @error('provinsi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kota -->
                    <div>
                        <label for="kota" class="block text-sm font-semibold text-gray-700 mb-2">
                            Kota <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="kota" 
                            name="kota" 
                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary @error('kota') border-red-500 @enderror" 
                            required
                            @if(!isset($defaultProvinsi) || !$defaultProvinsi) disabled @endif>
                            <option value="">-- {{ isset($defaultProvinsi) && $defaultProvinsi ? 'Pilih Kota' : 'Pilih Provinsi Terlebih Dahulu' }} --</option>
                            @if(isset($cities) && count($cities) > 0)
                                @foreach($cities as $city)
                                    <option value="{{ $city->city_code }}" {{ old('kota', $defaultKota ?? $mahasiswa->kota) == $city->city_code ? 'selected' : '' }}>
                                        {{ $city->city }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('kota')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Card 3: Data Pendaftaran -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-200">
                <div class="p-6 border-b border-gray-200">
                    <h6 class="text-lg font-bold text-primary">Data Pendaftaran</h6>
                    <p class="text-sm text-gray-500 mt-1">Informasi pendaftaran</p>
                </div>
                <div class="p-6 space-y-5">
                    <!-- Tanggal Daftar -->
                    <div>
                        <label for="tanggal_daftar" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Daftar <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="tanggal_daftar" 
                            name="tanggal_daftar" 
                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary @error('tanggal_daftar') border-red-500 @enderror" 
                            value="{{ old('tanggal_daftar', $mahasiswa->tanggal_daftar) }}"
                            placeholder="Pilih tanggal"
                            readonly
                            required>
                        @error('tanggal_daftar')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tahu STIH Darimana -->
                    <div>
                        <label for="tahu_stih_darimana" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tahu STIH Darimana <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="tahu_stih_darimana" 
                            name="tahu_stih_darimana" 
                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary @error('tahu_stih_darimana') border-red-500 @enderror" 
                            required>
                            <option value="">-- Pilih Sumber Informasi --</option>
                            @foreach($tahuStihOptions as $option)
                                <option value="{{ $option->id }}" {{ old('tahu_stih_darimana', $mahasiswa->tahu_stih_darimana) == $option->id ? 'selected' : '' }}>
                                    {{ $option->sumber }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahu_stih_darimana')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sumber Beasiswa/Non -->
                    <div>
                        <label for="sumber_beasiswa" class="block text-sm font-semibold text-gray-700 mb-2">
                            Beasiswa <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="sumber_beasiswa" 
                            name="sumber_beasiswa" 
                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary @error('sumber_beasiswa') border-red-500 @enderror" 
                            required>
                            <option value="">-- Pilih Sumber Beasiswa --</option>
                            <option value="beasiswa" {{ old('sumber_beasiswa', $mahasiswa->sumber_beasiswa) == 'beasiswa' ? 'selected' : '' }}>Beasiswa</option>
                            <option value="non_beasiswa" {{ old('sumber_beasiswa', $mahasiswa->sumber_beasiswa) == 'non_beasiswa' ? 'selected' : '' }}>Non Beasiswa</option>
                        </select>
                        @error('sumber_beasiswa')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Beasiswa -->
                    <div id="jenis_beasiswa_container" style="display: {{ old('sumber_beasiswa', $mahasiswa->sumber_beasiswa) == 'beasiswa' ? 'block' : 'none' }};">
                        <label for="jenis_beasiswa" class="block text-sm font-semibold text-gray-700 mb-2">
                            Jenis Beasiswa <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="jenis_beasiswa" 
                            name="jenis_beasiswa" 
                            class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary @error('jenis_beasiswa') border-red-500 @enderror">
                            <option value="">-- Pilih Jenis Beasiswa --</option>
                            <option value="50%" {{ old('jenis_beasiswa', $mahasiswa->jenis_beasiswa) == '50%' ? 'selected' : '' }}>Beasiswa 50%</option>
                            <option value="100%" {{ old('jenis_beasiswa', $mahasiswa->jenis_beasiswa) == '100%' ? 'selected' : '' }}>Beasiswa 100%</option>
                        </select>
                        @error('jenis_beasiswa')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('pemetaan.form.tabel') }}" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-hover text-white text-sm font-medium rounded-lg shadow-sm transition duration-200">
                    <i class="fas fa-table text-sm text-white mr-2"></i>
                    Lihat Data
                </a>
                <div class="flex items-center gap-4">
                    <a href="{{ route('pemetaan.form.tabel') }}" class="px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition duration-200">
                        Batal
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-primary hover:bg-primary-hover text-white font-medium rounded-lg shadow-sm transition duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Update Data
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Load cities based on selected province
        const provinsiSelect = document.getElementById('provinsi');
        const kotaSelect = document.getElementById('kota');
        const oldProvinsi = @json(old('provinsi', $defaultProvinsi ?? $mahasiswa->provinsi));
        const oldKota = @json(old('kota', $defaultKota ?? $mahasiswa->kota));

        function loadCities(provinceCode, selectedCityCode = null) {
            // Reset kota dropdown
            kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
            kotaSelect.disabled = !provinceCode;
            
            if (!provinceCode) {
                return;
            }
            
            // Show loading state
            kotaSelect.innerHTML = '<option value="">Memuat kota...</option>';
            
            // Fetch cities from API
            fetch(`{{ url('/api/cities') }}?province_code=${provinceCode}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Referer': window.location.origin,
                },
                credentials: 'include'
            })
                .then(async response => {
                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
                    if (Array.isArray(data)) {
                        if (data.length === 0) {
                            kotaSelect.innerHTML = '<option value="">Tidak ada kota tersedia</option>';
                        } else {
                            data.forEach(city => {
                                const option = document.createElement('option');
                                option.value = city.city_code;
                                option.textContent = city.city;
                                if (selectedCityCode && selectedCityCode == city.city_code) {
                                    option.selected = true;
                                }
                                kotaSelect.appendChild(option);
                            });
                        }
                    } else {
                        console.error('Invalid data format:', data);
                        kotaSelect.innerHTML = '<option value="">Error: Format data tidak valid</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading cities:', error);
                    kotaSelect.innerHTML = '<option value="">Error memuat kota</option>';
                });
        }

        provinsiSelect.addEventListener('change', function() {
            loadCities(this.value);
        });

        // Load cities on page load if province is already selected
        document.addEventListener('DOMContentLoaded', function() {
            // Set provinsi value if not already set
            if (oldProvinsi && provinsiSelect.value !== oldProvinsi) {
                provinsiSelect.value = oldProvinsi;
            }
            
            // Load cities if province is set
            if (oldProvinsi) {
                // Enable kota select first
                kotaSelect.disabled = false;
                loadCities(oldProvinsi, oldKota);
            }
        });

        // Initialize Flatpickr for Tanggal Daftar
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#tanggal_daftar", {
                dateFormat: "Y-m-d",
                locale: "id",
                allowInput: false,
                clickOpens: true,
                maxDate: "today",
                defaultDate: "{{ old('tanggal_daftar', $mahasiswa->tanggal_daftar) }}"
            });
            
            // Initialize Select2 for Asal Sekolah dropdown with AJAX lazy loading
            var $asalSekolah = $('#asal_sekolah');
            var selectedSchool = @json($selectedSchool);
            
            // Debug: Check what data we have
            console.log('Selected School:', selectedSchool);
            console.log('Old Provinsi:', @json(old('provinsi', $mahasiswa->provinsi)));
            console.log('Old Kota:', @json(old('kota', $mahasiswa->kota)));
            
            // Set initial value BEFORE initializing Select2
            if (selectedSchool && selectedSchool.trim() !== '') {
                console.log('Setting initial school value:', selectedSchool);
                var newOption = new Option(selectedSchool, selectedSchool, true, true);
                $asalSekolah.append(newOption);
            } else {
                console.log('No school data to set');
            }
            
            $asalSekolah.select2({
                placeholder: '-- Pilih atau cari nama sekolah --',
                allowClear: false,
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route("pemetaan.schools.get") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                language: {
                    noResults: function() {
                        return "Tidak ada hasil ditemukan";
                    },
                    searching: function() {
                        return '<i class="fas fa-spinner fa-spin"></i> Mencari...';
                    },
                    inputTooShort: function() {
                        return "Ketik minimal 0 karakter untuk mencari";
                    },
                    loadingMore: function() {
                        return '';
                    }
                },
                templateResult: function(data) {
                    if (data.loading) {
                        return '<div style="text-align: center; padding: 10px;"><i class="fas fa-spinner fa-spin"></i></div>';
                    }
                    return data.text;
                }
            });
            
            // Monitor for loading more and replace with icon
            var checkLoadMore;
            $asalSekolah.on('select2:open', function() {
                checkLoadMore = setInterval(function() {
                    var $loadMore = $('.select2-results__option--load-more');
                    if ($loadMore.length) {
                        var html = $loadMore.html();
                        if (html && html.trim() !== '' && !html.includes('fa-spinner')) {
                            $loadMore.html('<i class="fas fa-spinner fa-spin"></i>');
                        }
                    }
                }, 50);
            });
            
            $asalSekolah.on('select2:close', function() {
                if (checkLoadMore) {
                    clearInterval(checkLoadMore);
                }
            });
            
            // Force dropdown width on open
            $asalSekolah.on('select2:open', function() {
                // Prevent body horizontal scroll
                $('body').css('overflow-x', 'hidden');
                
                // Get the container width
                var containerWidth = $(this).parent().width();
                
                // Find the dropdown and force its width
                setTimeout(function() {
                    var $dropdown = $('.select2-dropdown');
                    $dropdown.css({
                        'width': containerWidth + 'px',
                        'max-width': containerWidth + 'px',
                        'min-width': containerWidth + 'px',
                        'overflow-x': 'hidden',
                        'box-sizing': 'border-box'
                    });
                    
                    // Set the search container
                    $dropdown.find('.select2-search--dropdown').css({
                        'box-sizing': 'border-box',
                        'padding': '8px',
                        'overflow': 'hidden'
                    });
                    
                    // Set the search field
                    $dropdown.find('.select2-search__field').css({
                        'width': '100%',
                        'box-sizing': 'border-box'
                    });
                    
                    // Also set the results container
                    $dropdown.find('.select2-results').css({
                        'max-width': '100%',
                        'overflow-x': 'hidden',
                        'box-sizing': 'border-box'
                    });
                    
                    // Set each option to wrap text
                    $dropdown.find('.select2-results__option').css({
                        'white-space': 'normal',
                        'word-wrap': 'break-word',
                        'overflow-wrap': 'break-word',
                        'box-sizing': 'border-box'
                    });
                }, 10);
            });
            
            // Handle school selection to auto-populate province and city
            $asalSekolah.on('select2:select', function(e) {
                var data = e.params.data;
                var provinceCode = data.province_code;
                var cityCode = data.city_code;
                
                if (provinceCode) {
                    // Set province
                    provinsiSelect.value = provinceCode;
                    
                    // Trigger change to load cities, passing the city code to select it
                    loadCities(provinceCode, cityCode);
                }
            });
            
            // Restore body overflow when closed
            $asalSekolah.on('select2:close', function() {
                $('body').css('overflow-x', '');
            });
        });

        // Handle Jenis Beasiswa visibility based on Sumber Beasiswa selection
        const sumberBeasiswaSelect = document.getElementById('sumber_beasiswa');
        const jenisBeasiswaContainer = document.getElementById('jenis_beasiswa_container');
        const jenisBeasiswaSelect = document.getElementById('jenis_beasiswa');

        function toggleJenisBeasiswa() {
            if (sumberBeasiswaSelect.value === 'beasiswa') {
                jenisBeasiswaContainer.style.display = 'block';
                jenisBeasiswaSelect.setAttribute('required', '');
            } else {
                jenisBeasiswaContainer.style.display = 'none';
                jenisBeasiswaSelect.removeAttribute('required');
                jenisBeasiswaSelect.value = ''; // Clear selection
            }
        }

        sumberBeasiswaSelect.addEventListener('change', toggleJenisBeasiswa);
        
        // Initialize on page load
        toggleJenisBeasiswa();
    </script>
@endpush

