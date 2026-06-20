<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Detail Data Agen') }}
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
        
        .max-w-7xl {
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    
                    <!-- Data Agen -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Informasi Utama -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Informasi Utama</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Kode Agen</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $agen->kode_agen }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Nama Agen</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $agen->nama_agen }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Status</label>
                                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $agen->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $agen->status ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>

                        <!-- Informasi Lokasi -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Informasi Lokasi</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Alamat Lengkap</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $agen->alamat }}</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <!-- <div>
                                    <label class="block text-sm font-medium text-gray-600">Provinsi</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $agen->provinsi_nama ?? $agen->provinsi }}</p>
                                </div> -->
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Kota</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $agen->kota }}</p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Lokasi Event</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $agen->lokasi_event }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Kontak -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Informasi Kontak</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Nama PIC</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $agen->pic }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Nomor HP PIC</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $agen->nomor_pic }}</p>
                            </div>
                        </div>

                        <!-- Informasi Kehadiran -->
                        <!-- <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Informasi Kehadiran</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Status Kehadiran</label>
                                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $agen->hadir ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $agen->hadir ? 'Hadir' : 'Belum Hadir' }}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Jumlah Kehadiran</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $agen->jumlah_kehadiran }}</p>
                            </div>
                        </div> -->
                    </div>

                    <!-- Merk yang Dimiliki -->
                    <div class="mb-6">
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Merk yang Dimiliki</h3>
                            
                            @if($merks->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($merks as $merk)
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h4 class="font-medium text-gray-900 text-sm">{{ $merk->name }}</h4>
                                                    @if($merk->code_item)
                                                        <p class="text-xs text-gray-500 mt-1">Kode: {{ $merk->code_item }}</p>
                                                    @endif
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Merk
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Summary -->
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mt-3">
                                    <p class="text-sm text-gray-600">
                                        Total <span class="font-semibold text-gray-800">{{ $merks->count() }}</span> merk yang dimiliki
                                    </p>
                                </div>
                            @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                                    <p class="text-sm text-yellow-700">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        Belum ada merk yang dimiliki oleh agen ini
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('daftaragen.edit', $agen->id) }}" 
                            class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                            Edit Data
                        </a>
                        <form action="{{ route('daftaragen.destroy', $agen->id) }}" method="POST" class="flex-1" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" 
                                onclick="confirmDelete()"
                                class="w-full px-3 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700">
                                Hapus Data
                            </button>
                        </form>
                        <a href="{{ route('daftaragen.index') }}" 
                            class="w-full sm:w-auto px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-center">
                            Kembali
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            if (confirm('Apakah Anda yakin ingin menghapus data agen ini?')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
    
</x-app-layout>