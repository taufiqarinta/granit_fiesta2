<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Tambah Master Doorprize Kehadiran') }}
        </h2>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>

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

        .lokasi-row {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('masterdoorprizekehadiran.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="nama_doorprize" class="block text-gray-700 text-sm font-bold mb-2">Nama Doorprize:</label>
                            <input type="text" name="nama_doorprize" id="nama_doorprize" value="{{ old('nama_doorprize') }}" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nama_doorprize') border-red-500 @enderror"
                                   placeholder="Masukkan nama doorprize kehadiran">
                            @error('nama_doorprize')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="nama_file" class="block text-gray-700 text-sm font-bold mb-2">Gambar Doorprize:</label>
                            <input type="file" name="nama_file" id="nama_file" accept="image/*"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nama_file') border-red-500 @enderror">
                            <p class="text-gray-500 text-xs mt-1">Format: jpeg, png, jpg, gif, svg | Maks: 2MB</p>
                            @error('nama_file')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="batas_jam_kehadiran" class="block text-gray-700 text-sm font-bold mb-2">Batas Jam Kehadiran:</label>
                            <input type="time" name="batas_jam_kehadiran" id="batas_jam_kehadiran" value="{{ old('batas_jam_kehadiran', '18:00') }}"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('batas_jam_kehadiran') border-red-500 @enderror">
                            <p class="text-gray-500 text-xs mt-1">Batas waktu kehadiran (toko hadir <strong>pada atau sebelum</strong> jam ini) yang berhak ikut undian hadiah ini</p>
                            @error('batas_jam_kehadiran')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Section Lokasi Event -->
                        <div class="mb-6 border-t pt-4">
                            <h3 class="text-lg font-semibold mb-4">Konfigurasi Lokasi Event</h3>
                            
                            @if($errors->has('lokasi'))
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                    {{ $errors->first('lokasi') }}
                                </div>
                            @endif

                            <div id="lokasi-container">
                                <div class="lokasi-row flex gap-4 mb-3 items-end" id="lokasi-row-0">
                                    <div class="flex-1">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi Event</label>
                                        <select name="lokasi[0][lokasi_event]" 
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('lokasi.0.lokasi_event') border-red-500 @enderror">
                                            <option value="">Pilih Lokasi Event</option>
                                            @foreach($lokasiEvents as $lokasi)
                                                <option value="{{ $lokasi->nama_lokasi }}" {{ old('lokasi.0.lokasi_event') == $lokasi->nama_lokasi ? 'selected' : '' }}>
                                                    {{ $lokasi->nama_lokasi }} 
                                                    @if($lokasi->tanggal)
                                                        ({{ \Carbon\Carbon::parse($lokasi->tanggal)->format('d/m/Y') }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('lokasi.0.lokasi_event')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Doorprize</label>
                                        <input type="number" name="lokasi[0][jumlah_doorprize]" 
                                               value="{{ old('lokasi.0.jumlah_doorprize') }}"
                                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('lokasi.0.jumlah_doorprize') border-red-500 @enderror"
                                               placeholder="Jumlah" min="0">
                                        @error('lokasi.0.jumlah_doorprize')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex items-center">
                                        <button type="button" 
                                                onclick="removeLokasi(0)" 
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="addLokasi()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-2">
                                + Tambah Lokasi
                            </button>
                            <p class="text-gray-500 text-xs mt-2">* Setiap lokasi bisa memiliki jumlah doorprize yang berbeda</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('masterdoorprizekehadiran.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Kembali
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let lokasiIndex = 1;

        function addLokasi() {
            const container = document.getElementById('lokasi-container');
            const newRow = document.createElement('div');
            newRow.className = 'lokasi-row flex gap-4 mb-3 items-end';
            newRow.id = `lokasi-row-${lokasiIndex}`;
            
            // Ambil option dari select pertama
            const firstSelect = document.querySelector('select[name="lokasi[0][lokasi_event]"]');
            const options = firstSelect ? firstSelect.innerHTML : '';
            
            newRow.innerHTML = `
                <div class="flex-1">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi Event</label>
                    <select name="lokasi[${lokasiIndex}][lokasi_event]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        ${options}
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Doorprize</label>
                    <input type="number" name="lokasi[${lokasiIndex}][jumlah_doorprize]" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           placeholder="Jumlah" min="0">
                </div>
                <div class="flex items-center">
                    <button type="button" onclick="removeLokasi(${lokasiIndex})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            `;
            container.appendChild(newRow);
            lokasiIndex++;
        }

        function removeLokasi(index) {
            const row = document.getElementById(`lokasi-row-${index}`);
            if (row) {
                const rows = document.querySelectorAll('.lokasi-row');
                if (rows.length > 1) {
                    row.remove();
                } else {
                    alert('Minimal harus ada 1 lokasi event!');
                }
            }
        }
    </script>
</x-app-layout>