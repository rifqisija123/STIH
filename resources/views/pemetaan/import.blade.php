@extends('layouts.app')

@section('title', 'Import Data - STIH')

@push('styles')
    <style>
        .file-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 0.75rem;
            padding: 3rem 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f9fafb;
        }
        
        .file-upload-area:hover {
            border-color: #b2202c;
            background: #fef2f2;
        }
        
        .file-upload-area.dragover {
            border-color: #b2202c;
            background: #fef2f2;
            transform: scale(1.02);
        }
        
        .file-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .upload-icon {
            font-size: 3rem;
            color: #b2202c;
            margin-bottom: 1rem;
        }
        
        .format-card {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background: white;
        }
        
        .format-card h4 {
            color: #b2202c;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .error-list {
            max-height: 300px;
            overflow-y: auto;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .success-message {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        /* Responsive improvements */
        .format-card pre {
            word-wrap: break-word;
            white-space: pre-wrap;
            overflow-wrap: break-word;
        }

        /* Prevent horizontal scroll on small screens */
        @media (max-width: 640px) {
            .format-card {
                padding: 0.75rem;
            }
            
            .file-upload-area {
                padding: 2rem 1rem;
            }
            
            .upload-icon {
                font-size: 2.5rem;
            }
        }

        /* Ensure proper text wrapping for long text */
        .break-words {
            overflow-wrap: break-word;
            word-wrap: break-word;
            hyphens: auto;
        }

        /* Improved container styles */
        .max-w-4xl {
            width: 100%;
            max-width: 56rem;
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6 mt-20">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Import Data Mahasiswa</h1>
                    <p class="mt-2 text-gray-600">Upload file CSV atau JSON untuk import data mahasiswa secara batch</p>
                </div>
                <a href="{{ route('pemetaan.form') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    ← Kembali
                </a>
            </div>
        </div>

        <!-- Messages -->
        @if (session('success'))
            <div class="success-message">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="error-message">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if (session('import_errors') && count(session('import_errors')) > 0)
            <div class="error-list">
                <h4 class="font-semibold mb-3 text-red-700">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error Import ({{ count(session('import_errors')) }} item):
                </h4>
                <ul class="space-y-1">
                    @foreach (session('import_errors') as $error)
                        <li class="text-sm text-red-600">• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Upload Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload File</h2>
                
                <form action="{{ route('pemetaan.import.process') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    
                    <div class="file-upload-area" id="fileUploadArea">
                        <input type="file" name="file" id="fileInput" class="file-input" accept=".csv,.json,.txt" required>
                        <div class="upload-content">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Pilih file atau drag & drop</h3>
                            <p class="text-gray-500 mb-4">File CSV, JSON, atau TXT (maksimal 5MB)</p>
                            <div class="text-sm text-gray-400">
                                <span class="file-name">Tidak ada file dipilih</span>
                            </div>
                        </div>
                    </div>

                    @error('file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="w-full mt-4 bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg transition-colors font-medium" id="uploadBtn" disabled>
                        <i class="fas fa-upload mr-2"></i>
                        Upload dan Import Data
                    </button>
                </form>
            </div>

            <!-- Format Guide -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Panduan Format File</h2>
                
                <div class="format-card">
                    <h4><i class="fas fa-file-csv mr-2"></i>Format CSV</h4>
                    <p class="text-sm text-gray-600 mb-2">Header kolom yang dibutuhkan:</p>
                    <div class="text-xs bg-gray-100 p-2 rounded font-mono overflow-x-auto whitespace-nowrap">
                        nama,nim,kode_provinsi,kode_kabkot,asal_sekolah
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        <p class="mb-1"><strong>Kolom wajib:</strong></p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 text-gray-600">
                            <span>• nama</span>
                            <span>• nim</span>
                            <span>• kode_provinsi</span>
                            <span>• kode_kabkot</span>
                            <span>• asal_sekolah</span>
                        </div>
                    </div>
                    <a href="{{ asset('examples/contoh_import_mahasiswa.csv') }}" download class="mt-2 inline-flex items-center text-sm text-red-600 hover:text-red-700">
                        <i class="fas fa-download mr-1"></i>Download contoh CSV
                    </a>
                </div>

                <div class="format-card">
                    <h4><i class="fas fa-file-code mr-2"></i>Format JSON</h4>
                    <p class="text-sm text-gray-600 mb-2">Array objek dengan properti:</p>
                    <div class="text-xs bg-gray-100 p-2 rounded font-mono overflow-x-auto">
                        <pre class="whitespace-pre-wrap break-words">[{
                        "nama": "John Doe",
                        "nim": "12345",
                        "kode_provinsi": "11",
                        "kode_kabkot": "1101",
                        "asal_sekolah": "123456789",
                        "tahun_masuk": "2024",
                        "tanggal_daftar": "2024-01-15",
                        "tahu_stih_darimana": "sosmed",
                        "sumber_beasiswa": "beasiswa"
                        }]</pre>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        <p class="mb-1"><strong>Field yang tersedia:</strong></p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 text-gray-600">
                            <span>• nama (wajib)</span>
                            <span>• nisn/nim (wajib)</span>
                            <span>• kode_provinsi (wajib)</span>
                            <span>• kode_kabkot (wajib)</span>
                            <span>• asal_sekolah (wajib)</span>
                            <span>• tahun_masuk</span>
                            <span>• tanggal_daftar</span>
                            <span>• tahu_stih_darimana</span>
                            <span>• sumber_beasiswa</span>
                        </div>
                    </div>
                    <a href="{{ asset('examples/contoh_import_mahasiswa.json') }}" download class="mt-2 inline-flex items-center text-sm text-red-600 hover:text-red-700">
                        <i class="fas fa-download mr-1"></i>Download contoh JSON
                    </a>
                </div>

                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                    <h5 class="font-medium text-yellow-800 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Catatan Penting:
                    </h5>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Field yang tersedia juga bisa menggunakan alias (nama, name, nama_mahasiswa)</li>
                        <li>• Format tanggal: YYYY-MM-DD atau format standar lain</li>
                        <li>• Sumber beasiswa: "beasiswa" atau "non_beasiswa"</li>
                        <li>• NIM harus unik</li>
                        <li>• Semua field wajib diisi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const uploadArea = document.getElementById('fileUploadArea');
    const fileNameSpan = document.querySelector('.file-name');
    const uploadBtn = document.getElementById('uploadBtn');

    // File input change handler
    fileInput.addEventListener('change', function() {
        handleFileSelect(this.files[0]);
    });

    // Drag and drop handlers
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });

    function handleFileSelect(file) {
        if (file) {
            const allowedTypes = ['text/csv', 'application/json', 'text/plain'];
            const allowedExtensions = ['.csv', '.json', '.txt'];
            
            const fileName = file.name.toLowerCase();
            const isValidExtension = allowedExtensions.some(ext => fileName.endsWith(ext));
            
            if (file.size > 5 * 1024 * 1024) {
                alert('File terlalu besar. Maksimal 5MB.');
                resetFileInput();
                return;
            }
            
            if (!isValidExtension) {
                alert('Format file tidak didukung. Gunakan CSV, JSON, atau TXT.');
                resetFileInput();
                return;
            }
            
            fileNameSpan.textContent = file.name;
            uploadBtn.disabled = false;
            uploadBtn.classList.remove('bg-gray-400');
            uploadBtn.classList.add('bg-red-600', 'hover:bg-red-700');
        } else {
            resetFileInput();
        }
    }

    function resetFileInput() {
        fileNameSpan.textContent = 'Tidak ada file dipilih';
        uploadBtn.disabled = true;
        uploadBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
        uploadBtn.classList.add('bg-gray-400');
        fileInput.value = '';
    }

    // Form submit handler
    document.getElementById('uploadForm').addEventListener('submit', function() {
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengupload...';
        uploadBtn.disabled = true;
    });
});
</script>
@endpush