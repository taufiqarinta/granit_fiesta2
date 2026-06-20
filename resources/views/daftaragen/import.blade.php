<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Import Data Agen') }}
            </h2>
            <a href="{{ route('daftaragen.index') }}" 
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Informasi Import -->
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">Panduan Import Excel:</h3>
                        <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
                            <li>Download template Excel terlebih dahulu untuk mendapatkan format yang benar</li>
                            <li>Kolom yang wajib diisi: <strong>KODE Agen</strong> dan <strong>NAMA Agen</strong></li>
                            <li>Kolom <strong>Checkin</strong> diisi dengan "Ya" atau "Tidak"</li>
                            <li>Isi "Ya" pada kolom Checkin akan mengisi data Check in, "Tidak" akan dikosongkan (null)</li>
                            <li>Kolom yang kosong akan otomatis terisi NULL di database</li>
                            <li>Maksimal ukuran file: 5MB (format .xlsx, .xls, .csv)</li>
                        </ul>
                    </div>

                    <!-- Form Import -->
                    <form action="{{ route('daftaragen.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <!-- <div class="mb-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div> -->
                            
                            <div class="mb-4">
                                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih File Excel
                                </label>
                                <input type="file" 
                                       name="file" 
                                       id="file" 
                                       accept=".xlsx,.xls,.csv"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                       required>
                                @error('file')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                                    Import Data
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Download Template -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('daftaragen.template') }}" 
                           class="text-blue-600 hover:text-blue-800 underline">
                            📥 Download Template Excel
                        </a>
                    </div>

                    <!-- Tampilkan Errors jika ada -->
                    @if(session('import_errors'))
                        <div class="mt-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <h4 class="font-semibold text-yellow-800 mb-2">Data yang gagal diimport:</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead>
                                            <tr class="bg-yellow-100">
                                                <th class="px-3 py-2 text-left">Baris</th>
                                                <th class="px-3 py-2 text-left">Kode Agen</th>
                                                <th class="px-3 py-2 text-left">Error</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(session('import_errors') as $error)
                                                <tr class="border-b border-yellow-100">
                                                    <td class="px-3 py-2">{{ $error['row'] }}</td>
                                                    <td class="px-3 py-2">{{ $error['kode_agen'] }}</td>
                                                    <td class="px-3 py-2 text-red-600">{{ $error['error'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>