<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Agen Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    
                    <form method="POST" action="{{ route('daftaragen.store') }}" id="agenForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

                            <!-- Kode Agen -->
                            <div>
                                <label for="kode_agen" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kode Agen <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="kode_agen" 
                                    id="kode_agen" 
                                    value="{{ $kodeAgen }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kode_agen') border-red-500 @enderror"
                                    readonly>
                                @error('kode_agen')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama Agen -->
                            <div>
                                <label for="nama_agen" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Agen <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="nama_agen" 
                                    id="nama_agen" 
                                    value="{{ old('nama_agen') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nama_agen') border-red-500 @enderror"
                                    style="text-transform: uppercase;"
                                    oninput="this.value = this.value.toUpperCase();"
                                    required>
                                @error('nama_agen')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <!-- Alamat Lengkap (Full Width) -->
                        <div class="mt-4 md:mt-6">
                            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat Lengkap <span class="text-red-500">*</span>
                            </label>
                            <textarea name="alamat" 
                                id="alamat" 
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('alamat') border-red-500 @enderror"
                                style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase();"
                                required>{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Provinsi dan Kota dalam satu baris -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mt-4 md:mt-6">
                            <!-- Provinsi -->
                            <div>
                                <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-2">
                                    Provinsi <span class="text-red-500">*</span>
                                </label>
                                <select name="provinsi" 
                                    id="provinsi" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('provinsi') border-red-500 @enderror"
                                    required>
                                    <option value="">- Pilih Provinsi -</option>
                                    @foreach ($provinsis as $prov)
                                        <option value="{{ $prov->kode }}" {{ old('provinsi') == $prov->kode ? 'selected' : '' }}>
                                            {{ $prov->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('provinsi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Kota -->
                            <div>
                                <label for="kota" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kota <span class="text-red-500">*</span>
                                </label>
                                <select name="kota" 
                                    id="kota" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kota') border-red-500 @enderror"
                                    required>
                                    <option value="">- Pilih Kota -</option>
                                    @if(old('kota'))
                                        <!-- Opsi kota akan dimuat via JavaScript -->
                                    @endif
                                </select>
                                @error('kota')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mt-4 md:mt-6">

                            <!-- PIC -->
                            <div>
                                <label for="pic" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama PIC <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="pic" 
                                    id="pic" 
                                    value="{{ old('pic') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('pic') border-red-500 @enderror"
                                    style="text-transform: uppercase;"
                                    oninput="this.value = this.value.toUpperCase();"
                                    required>
                                @error('pic')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nomor PIC -->
                            <div>
                                <label for="nomor_pic" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor HP PIC<span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="nomor_pic" 
                                    id="nomor_pic" 
                                    value="{{ old('nomor_pic') }}"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nomor_pic') border-red-500 @enderror"
                                    required>
                                @error('nomor_pic')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Lokasi Event -->
                            <div>
                                <label for="lokasi_event" class="block text-sm font-medium text-gray-700 mb-2">
                                    Lokasi Event <span class="text-red-500">*</span>
                                </label>
                                <select name="lokasi_event" 
                                    id="lokasi_event" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('lokasi_event') border-red-500 @enderror"
                                    required>
                                    <option value="">Pilih Lokasi Event</option>
                                    @foreach($lokasiEvents as $lokasi)
                                        <option value="{{ $lokasi->nama_lokasi }}" {{ old('lokasi_event') == $lokasi->nama_lokasi ? 'selected' : '' }}>
                                            {{ $lokasi->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('lokasi_event')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <!-- Merk yang Dimiliki -->
                        <div class="mt-6 md:mt-8">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Merk yang Dimiliki <span class="text-red-500">*</span>
                            </label>
                            <div class="border border-gray-300 rounded-lg bg-gray-50 p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-80 overflow-y-auto p-2">
                                    @foreach($merks as $merk)
                                        <div class="flex items-start p-3 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors duration-200">
                                            <input type="checkbox" 
                                                name="merks[]" 
                                                id="merk-{{ $merk->id }}" 
                                                value="{{ $merk->id }}"
                                                class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mt-0.5 flex-shrink-0"
                                                {{ in_array($merk->id, old('merks', [])) ? 'checked' : '' }}>
                                            <label for="merk-{{ $merk->id }}" class="ml-3 text-sm text-gray-700 leading-5">
                                                <span class="font-medium text-gray-900 block">{{ $merk->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('merks')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('merks.*')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200">
                            <button type="submit" 
                                class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                                Simpan
                            </button>
                            <a href="{{ route('daftaragen.index') }}" 
                                class="w-full sm:w-auto px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-center">
                                Batal
                            </a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinsiSelect = document.getElementById('provinsi');
            const kotaSelect = document.getElementById('kota');
            
            // Jika ada provinsi yang sudah dipilih sebelumnya (misalnya karena validasi gagal)
            // maka muat daftar kota untuk provinsi tersebut
            if (provinsiSelect.value) {
                loadKota(provinsiSelect.value);
            }
            
            // Event listener untuk perubahan provinsi
            provinsiSelect.addEventListener('change', function() {
                const provinsiKode = this.value;
                loadKota(provinsiKode);
            });
            
            function loadKota(provinsiKode) {
                kotaSelect.innerHTML = '<option value="">Memuat...</option>';
                kotaSelect.disabled = true;
                
                if (provinsiKode) {
                    fetch(`/kabupaten?kode_provinsi=${provinsiKode}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            kotaSelect.innerHTML = '<option value="">- Pilih Kota -</option>';
                            data.forEach(item => {
                                const option = document.createElement('option');
                                option.value = item.kode;
                                option.textContent = item.nama;
                                // Jika ini adalah nilai yang dipilih sebelumnya, set sebagai selected
                                if (item.kode === '{{ old('kota') }}') {
                                    option.selected = true;
                                }
                                kotaSelect.appendChild(option);
                            });
                            kotaSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching kota:', error);
                            kotaSelect.innerHTML = '<option value="">- Error memuat data -</option>';
                            kotaSelect.disabled = false;
                        });
                } else {
                    kotaSelect.innerHTML = '<option value="">- Pilih Kota -</option>';
                    kotaSelect.disabled = false;
                }
            }
        });
    </script>
    
</x-app-layout>