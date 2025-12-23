@extends('layouts.app')

@section('title', 'Import Data - STIH')

@push('styles')
<style>
    html, body {
        max-width: 100%;
        overflow-x: hidden;
    }

    .file-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 0.75rem;
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.25s ease;
        cursor: pointer;
        background: #f9fafb;
        position: relative;
    }

    .file-upload-area:hover,
    .file-upload-area.dragover {
        border-color: #b2202c;
        background: #fef2f2;
    }

    .file-input {
        position: absolute;
        inset: 0;
        opacity: 0;
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
        background: white;
    }

    .format-card pre {
        max-width: 100%;
        white-space: pre-wrap;
        word-break: break-word;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 overflow-x-hidden">

        {{-- HEADER --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6 mt-20">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Import Data Mahasiswa</h1>
                    <p class="mt-2 text-gray-600">
                        Upload file Excel untuk import data mahasiswa secara batch
                    </p>
                </div>
                <a href="{{ route('pemetaan.form') }}"
                   class="bg-gray-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                    Kembali
                </a>
            </div>
        </div>

        {{-- NOTIFICATIONS --}}
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('import_errors') && count(session('import_errors')) > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 mb-2">
                            Beberapa data tidak berhasil diimport:
                        </h3>
                        <div class="text-sm text-yellow-700 max-h-32 overflow-y-auto">
                            @foreach (session('import_errors') as $error)
                                <p class="mb-1">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- UPLOAD --}}
            <div class="min-w-0 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload File Excel</h2>

                <form action="{{ route('pemetaan.import.process') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      id="uploadForm">
                    @csrf

                    <div class="file-upload-area" id="uploadArea">
                        <input type="file"
                               name="files[]"
                               id="fileInput"
                               class="file-input"
                               accept=".xlsx"
                               multiple
                               required>

                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            Pilih file atau drag & drop
                        </h3>
                        <p class="text-gray-500 mb-4">
                            Excel (maks 20MB)
                        </p>
                        
                        <!-- File List Container -->
                        <div id="fileList" class="mt-4 text-left space-y-2 hidden">
                            <!-- Helper text -->
                            <p class="text-xs text-gray-500 mb-2 font-medium">File yang akan diupload:</p>
                            <!-- List items will be injected here -->
                        </div>
                        
                        <p class="text-sm text-gray-400 file-name-placeholder mt-2">
                            Tidak ada file dipilih
                        </p>
                    </div>

                    <button type="submit"
                            id="uploadBtn"
                            disabled
                            class="w-full mt-4 bg-gray-400 text-white px-4 py-3 rounded-lg font-medium transition disabled:cursor-not-allowed">
                        <i class="fas fa-upload mr-2"></i>
                        Upload dan Import Data
                    </button>

                    {{-- PREVIEW --}}
                    <button type="button"
                            id="previewBtn"
                            class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium hidden">
                        <i class="fas fa-eye mr-2"></i>
                        Preview Data
                    </button>
                </form>

                {{-- PREVIEW DATA SECTION --}}
                <div id="previewWrapper" class="mt-6 hidden">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">
                        <i class="fas fa-eye text-blue-600 mr-2"></i>
                        Preview Data (5 baris pertama)
                    </h3>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50" id="previewHead">
                                <!-- Dynamic header will be inserted here -->
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="previewBody">
                                <!-- Dynamic data will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- FORMAT --}}
            <div class="min-w-0 bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Panduan Format File</h2>

                <div class="format-card">
                    <h4 class="text-red-600 font-semibold mb-2">
                        <i class="fas fa-file-csv mr-1"></i> Format Excel
                    </h4>

                    <div class="text-xs bg-gray-100 p-2 rounded break-words text-gray-700">
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Unduh template Excel yang tersedia.</li>
                            <li>Isi data mahasiswa sesuai kolom.</li>
                            <li>Judul kolom yang sudah disediakan tidak boleh dihapus atau dirubah.</li>
                            <li>Format tanggal: DD-MM-YYYY.</li>
                            <li>Kolom 'nim' harus 10 digit angka.</li>
                            <li>Kolom 'provinsi' & 'kota' harus nama wilayah.</li>
                            <li>Kolom 'sumber_beasiswa' diisi beasiswa atau non beasiswa.</li>
                            <li>Simpan sebagai .xlsx & upload.</li>
                        </ol>
                    </div>

                    <a href="{{ asset('examples/Data_Olahan.xlsx') }}"
                       download
                       class="mt-2 inline-flex items-center text-sm text-red-600 hover:underline">
                        <i class="fas fa-download mr-1"></i>
                        Download template Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('fileInput');
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadArea = document.getElementById('uploadArea');
    const fileListContainer = document.getElementById('fileList');
    const placeholderText = document.querySelector('.file-name-placeholder');

    const previewWrapper = document.getElementById('previewWrapper');
    const previewHead = document.getElementById('previewHead');
    const previewBody = document.getElementById('previewBody');

    // DataTransfer object to hold accumulated files
    const dataTransfer = new DataTransfer();

    function updateUI() {
        const files = dataTransfer.files;
        
        // Update input files
        fileInput.files = files;

        // Calculate size
        const totalSize = Array.from(files).reduce((acc, file) => acc + file.size, 0);
        const maxSize = 20 * 1024 * 1024; // 20MB

        // Reset UI
        fileListContainer.innerHTML = '<p class="text-xs text-gray-500 mb-2 font-medium">File yang akan diupload:</p>';
        
        if (files.length > 0) {
            fileListContainer.classList.remove('hidden');
            if (placeholderText) placeholderText.classList.add('hidden');
            
            Array.from(files).forEach((file, index) => {
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between text-sm bg-white p-2 rounded border border-gray-200 shadow-sm';
                item.innerHTML = `
                    <div class="flex items-center truncate mr-2">
                        <i class="fas fa-file-excel text-green-600 mr-2"></i>
                        <span class="truncate text-gray-700">${file.name}</span>
                        <span class="text-xs text-gray-400 ml-2">(${(file.size/1024).toFixed(1)} KB)</span>
                    </div>
                `;
                fileListContainer.appendChild(item);
            });

            // Validation
            if (totalSize > maxSize) {
                alert('Total ukuran file melebihi 20MB');
                uploadBtn.disabled = true;
                uploadBtn.classList.replace('bg-red-600', 'bg-gray-400');
                uploadBtn.classList.remove('hover:bg-red-700');
            } else {
                uploadBtn.disabled = false;
                uploadBtn.classList.replace('bg-gray-400', 'bg-red-600');
                uploadBtn.classList.add('hover:bg-red-700');
                
                // Trigger preview for first file
                if (files.length > 0) previewFile(files[0]);
            }
        } else {
            fileListContainer.classList.add('hidden');
            if (placeholderText) placeholderText.classList.remove('hidden');
            uploadBtn.disabled = true;
            uploadBtn.classList.replace('bg-red-600', 'bg-gray-400');
            uploadBtn.classList.remove('hover:bg-red-700');
        }
    }

    fileInput.addEventListener('change', () => {
        // Add new files to DataTransfer
        Array.from(fileInput.files).forEach(file => {
            // Optional: avoid duplicates by name/size check?
            // For now, simple accumulation as requested
            dataTransfer.items.add(file);
        });
        
        updateUI();
    });

    // Drag & Drop
    ['dragover','dragleave','drop'].forEach(evt =>
        uploadArea.addEventListener(evt, e => e.preventDefault())
    );

    uploadArea.addEventListener('dragover', () => uploadArea.classList.add('dragover'));
    uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));

    uploadArea.addEventListener('drop', e => {
        uploadArea.classList.remove('dragover');
        const droppedFiles = e.dataTransfer.files;
        
        Array.from(droppedFiles).forEach(file => {
            if (file.type.includes('sheet') || file.name.endsWith('.xlsx')) {
                 dataTransfer.items.add(file);
            }
        });
        
        updateUI();
    });

    async function previewFile(file) {
        const formData = new FormData();
        formData.append('file', file);

        previewWrapper.classList.add('hidden');
        previewHead.innerHTML = '';
        previewBody.innerHTML = '';

        try {
            const res = await fetch("{{ route('pemetaan.import.preview') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: formData
            });

            const json = await res.json();
            if (!json.success || !json.data || json.data.length === 0) return;

            previewHead.innerHTML =
                `<tr>${json.columns.map(c => `<th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">${c}</th>`).join('')}</tr>`;

            previewBody.innerHTML = '';
            json.data.forEach(row => {
                previewBody.innerHTML +=
                    `<tr>${json.columns.map(c =>
                        `<td class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">${row[c] ?? '-'}</td>`
                    ).join('')}</tr>`;
            });

            previewWrapper.classList.remove('hidden');

        } catch (e) {
            console.error(e);
        }
    }

    document.getElementById('uploadForm').addEventListener('submit', () => {
        // Ensure input has all files
        fileInput.files = dataTransfer.files; 
        
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengupload...';
        uploadBtn.disabled = true;
    });
});
</script>
@endpush
