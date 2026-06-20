<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Detail Toko') }}
        </h2>
    </x-slot>

    <style>
        body::after {
            content: "";
            position: fixed;
            right: 20px;
            bottom: 20px;
            width: 240px;
            height: 240px;
            background-image: url('{{ asset('corner.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.1;
            pointer-events: none;
            z-index: 5;
        }
        
        .max-w-4xl {
            position: relative;
            z-index: 10;
        }
        
        table {
            background: white;
            position: relative;
            z-index: 15;
        }
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    
                    <!-- Header with Actions -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b border-gray-200 gap-3">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $daftartoko->nama_toko }}</h3>
                            <p class="text-gray-600 mt-1">{{ $daftartoko->kode_toko }}</p>
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <a href="{{ route('daftartoko.edit', $daftartoko) }}" 
                                class="flex-1 sm:flex-none px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-center text-sm"
                                style="background-color: #f1c40f; color: white; padding: 5px 10px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer;">
                                Edit
                            </a>
                            <a href="{{ route('daftartoko.index') }}" 
                                class="flex-1 sm:flex-none px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-center text-sm">
                                Kembali
                            </a>
                        </div>
                    </div>

                    <!-- Detail Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Informasi Dasar -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Kode Toko</label>
                                <p class="text-base text-gray-900 font-mono">{{ $daftartoko->kode_toko }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Toko</label>
                                <p class="text-base text-gray-900">{{ $daftartoko->nama_toko }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Kode Agen</label>
                                <p class="text-base text-gray-900 font-mono">{{ $daftartoko->kode_agen }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nama Agen</label>
                                <p class="text-base text-gray-900">{{ $daftartoko->nama_agen }}</p>
                            </div>
                        </div>

                        <!-- Informasi Lokasi -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Lokasi</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Alamat Lengkap</label>
                                <p class="text-base text-gray-900">{{ $daftartoko->alamat }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Provinsi</label>
                                    <p class="text-base text-gray-900">{{ $provinsi->nama ?? $daftartoko->provinsi }}</p>
                                </div> -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Kota</label>
                                    <p class="text-base text-gray-900">{{ $daftartoko->kota }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Lokasi Event</label>
                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                    {{ $daftartoko->lokasi_event }}
                                </span>
                            </div>
                        </div>

                        <!-- Informasi Kontak -->
                        <div class="space-y-4 md:col-span-2">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kontak</h4>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Nama PIC</label>
                                    <p class="text-base text-gray-900">{{ $daftartoko->pic }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Nomor HP PIC</label>
                                    <p class="text-base text-gray-900">
                                        <a href="tel:{{ $daftartoko->nomor_pic }}" class="text-indigo-600 hover:text-indigo-800">
                                            {{ $daftartoko->nomor_pic }}
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">Nama Sales</label>
                                    <p class="text-base text-gray-900">{{ $daftartoko->nama_sales }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Status & Waktu -->
                        <div class="space-y-4 md:col-span-2 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <label class="block font-medium text-gray-500 mb-1">Status</label>
                                    @if($daftartoko->status == 1)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <label class="block font-medium text-gray-500 mb-1">Dibuat pada</label>
                                    <p class="text-gray-700">{{ $daftartoko->created_at->format('d F Y H:i') }}</p>
                                </div>
                                <div>
                                    <label class="block font-medium text-gray-500 mb-1">Terakhir diupdate</label>
                                    <p class="text-gray-700">{{ $daftartoko->updated_at->format('d F Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Delete Button -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <form action="{{ route('daftartoko.destroy', $daftartoko) }}" 
                            method="POST" 
                            onsubmit="return confirm('Yakin ingin menghapus data toko ini? Tindakan ini tidak dapat dibatalkan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                Hapus Toko
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>