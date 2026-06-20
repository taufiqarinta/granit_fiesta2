<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Generate QR Code Agen') }}
            </h2>
        </div>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Filter Form -->
                    <div class="mb-8 bg-gray-50 p-6 rounded-lg">
                        <form action="{{ route('daftaragen.generate-qr') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                            <div class="flex-1">
                                <label for="lokasi_event" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Lokasi Event:
                                </label>
                                <select id="lokasi_event" name="lokasi_event" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="semua">-- Pilih Lokasi Event --</option>
                                    @foreach($lokasiEvents as $lokasi)
                                        <option value="{{ $lokasi->nama_lokasi }}" 
                                            {{ ($selectedLokasi ?? '') == $lokasi->nama_lokasi ? 'selected' : '' }}>
                                            {{ $lokasi->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="flex space-x-2">
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Tampilkan Agen
                                </button>
                                
                                @if($selectedLokasi && $selectedLokasi != 'semua' && $agens->count() > 0)
                                    <a href="{{ route('daftaragen.export-qr-pdf', ['lokasi_event' => $selectedLokasi]) }}" 
                                       target="_blank"
                                       class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Export PDF ({{ $agens->count() }} Agen)
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Preview QR Codes -->
                    @if($selectedLokasi && $selectedLokasi != 'semua')
                        @if($agens->count() > 0)
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                    Preview QR Code - {{ $selectedLokasi }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    Menampilkan {{ $agens->count() }} agen aktif
                                </p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($agens as $agen)
                                    <div class="border rounded-lg p-4 bg-white shadow-sm">
                                        <div class="flex flex-col items-center text-center">
                                            <div class="mb-2 flex justify-center">
                                                {!! QrCode::size(150)->generate($agen->kode_agen) !!}
                                            </div>
                                            <div class="font-bold text-lg">{{ $agen->kode_agen }}</div>
                                            <div class="text-sm text-gray-600">{{ $agen->nama_agen }}</div>
                                            <div class="text-xs text-gray-500 mt-1">{{ $agen->alamat }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($selectedLokasi)
                            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                                Tidak ada agen aktif di lokasi event {{ $selectedLokasi }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
