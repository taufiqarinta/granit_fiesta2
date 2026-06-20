<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Edit Data Toko') }}
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
                    
                    <form method="POST" action="{{ route('daftartoko.update', $daftartoko) }}" id="tokoForm">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

                            <!-- Kode Agen -->
                            <div>
                                <label for="kode_agen" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kode Agen <span class="text-red-500">*</span>
                                </label>
                                
                                @if($isSalesDepartment && count($agenList) > 0)
                                    <!-- Dropdown untuk department SLS -->
                                    <select name="kode_agen" 
                                            id="kode_agen" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kode_agen') border-red-500 @enderror"
                                            required>
                                        <option value="">- Pilih Agen -</option>
                                        @foreach($agenList as $agen)
                                            <option value="{{ $agen->kode_agen }}" 
                                                {{ old('kode_agen', $daftartoko->kode_agen) == $agen->kode_agen ? 'selected' : '' }}>
                                                {{ $agen->kode_agen }} - {{ $agen->nama_agen }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <!-- Input readonly untuk department lain -->
                                    <input type="text" 
                                        name="kode_agen" 
                                        id="kode_agen" 
                                        value="{{ old('kode_agen', $daftartoko->kode_agen) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kode_agen') border-red-500 @enderror bg-gray-100"
                                        style="text-transform: uppercase;"
                                        readonly>
                                @endif
                                
                                @error('kode_agen')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama Agen -->
                            <div>
                                <label for="nama_agen" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Agen <span class="text-red-500">*</span>
                                </label>
                                
                                @if($isSalesDepartment && count($agenList) > 0)
                                    <!-- Input untuk nama agen yang akan diisi otomatis atau manual -->
                                    <input type="text" 
                                        name="nama_agen" 
                                        id="nama_agen" 
                                        value="{{ old('nama_agen', $daftartoko->nama_agen) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nama_agen') border-red-500 @enderror"
                                        style="text-transform: uppercase;"
                                        oninput="this.value = this.value.toUpperCase();"
                                        required>
                                @else
                                    <!-- Input readonly untuk department lain -->
                                    <input type="text" 
                                        name="nama_agen" 
                                        id="nama_agen" 
                                        value="{{ old('nama_agen', $daftartoko->nama_agen) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nama_agen') border-red-500 @enderror bg-gray-100"
                                        style="text-transform: uppercase;"
                                        readonly>
                                @endif
                                
                                @error('nama_agen')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Kode Toko -->
                            <div>
                                <label for="kode_toko" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kode Toko <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="kode_toko" 
                                    id="kode_toko" 
                                    value="{{ old('kode_toko', $daftartoko->kode_toko) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kode_toko') border-red-500 @enderror"
                                    style="text-transform: uppercase;"
                                    oninput="this.value = this.value.toUpperCase();"
                                    readonly>
                                @error('kode_toko')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama Toko -->
                            <div>
                                <label for="nama_toko" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Toko <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="nama_toko" 
                                    id="nama_toko" 
                                    value="{{ old('nama_toko', $daftartoko->nama_toko) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nama_toko') border-red-500 @enderror"
                                    style="text-transform: uppercase;"
                                    oninput="this.value = this.value.toUpperCase();"
                                    required>
                                @error('nama_toko')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Alamat Lengkap (Full Width) -->
                            <div class="md:col-span-2">
                                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat Lengkap <span class="text-red-500">*</span>
                                </label>
                                <textarea name="alamat" 
                                    id="alamat" 
                                    rows="3"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('alamat') border-red-500 @enderror"
                                    style="text-transform: uppercase;"
                                    oninput="this.value = this.value.toUpperCase();"
                                    required>{{ old('alamat', $daftartoko->alamat) }}</textarea>
                                @error('alamat')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Provinsi dan Kota dalam satu baris -->
                            <div class="md:col-span-2 hidden">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Provinsi -->
                                    <div>
                                        <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-2">
                                            Provinsi <span class="text-red-500">*</span>
                                        </label>
                                        <select name="provinsi" 
                                            id="provinsi" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('provinsi') border-red-500 @enderror">
                                            <option value="">- Pilih Provinsi -</option>
                                            @foreach ($provinsis as $prov)
                                                <option value="{{ $prov->kode }}" {{ old('provinsi', $daftartoko->provinsi) == $prov->kode ? 'selected' : '' }}>
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
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kota') border-red-500 @enderror">
                                            <option value="">- Pilih Kota -</option>
                                            <!-- Opsi kota akan dimuat via JavaScript -->
                                            @if(old('kota') || $daftartoko->kota)
                                                <!-- Jika ada data kota yang dipilih, akan diisi via JavaScript -->
                                            @endif
                                        </select>
                                        @error('kota')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- KOTA Toko -->
                            <div>
                                <label for="kota" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kota Toko <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="kota" 
                                    id="kota" 
                                    value="{{ old('kota', $daftartoko->kota) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kota') border-red-500 @enderror"
                                    style="text-transform: uppercase;"
                                    oninput="this.value = this.value.toUpperCase();"
                                    required>
                                @error('kota')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- PIC -->
                            <div>
                                <label for="pic" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama PIC <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="pic" 
                                    id="pic" 
                                    value="{{ old('pic', $daftartoko->pic) }}"
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
                                    value="{{ old('nomor_pic', $daftartoko->nomor_pic) }}"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nomor_pic') border-red-500 @enderror"
                                    style="text-transform: uppercase;"
                                    oninput="this.value = this.value.toUpperCase();"
                                    required>
                                @error('nomor_pic')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama Sales -->
                            <div>
                                <label for="nama_sales" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Sales <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="nama_sales" 
                                    id="nama_sales" 
                                    value="{{ old('nama_sales', $daftartoko->nama_sales) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nama_sales') border-red-500 @enderror"
                                    style="text-transform: uppercase;"
                                    oninput="this.value = this.value.toUpperCase();"
                                    required>
                                @error('nama_sales')
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
                                        <option value="{{ $lokasi->nama_lokasi }}" 
                                            {{ old('lokasi_event', $daftartoko->lokasi_event) == $lokasi->nama_lokasi ? 'selected' : '' }}>
                                            {{ $lokasi->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('lokasi_event')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status Toko -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status Toko <span class="text-red-500">*</span>
                                </label>
                                <select name="status" 
                                    id="status" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-red-500 @enderror"
                                    required>
                                    <option value="">Pilih Status</option>
                                    <option value="1" {{ old('status', $daftartoko->status) == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('status', $daftartoko->status) == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200">
                            <button type="submit" 
                                class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                                Update
                            </button>
                            <a href="{{ route('daftartoko.index') }}" 
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
            const kodeAgenSelect = document.getElementById('kode_agen');
            const namaAgenInput = document.getElementById('nama_agen');
            
            // Jika kode_agen adalah dropdown (untuk department SLS)
            if (kodeAgenSelect && kodeAgenSelect.tagName === 'SELECT') {
                // Data agen untuk auto-fill
                const agenData = {
                    @foreach($agenList as $agen)
                        "{{ $agen->kode_agen }}": "{{ $agen->nama_agen }}",
                    @endforeach
                };
                
                // Event listener untuk perubahan dropdown agen
                kodeAgenSelect.addEventListener('change', function() {
                    const selectedKodeAgen = this.value;
                    if (selectedKodeAgen && agenData[selectedKodeAgen]) {
                        namaAgenInput.value = agenData[selectedKodeAgen];
                    } else {
                        namaAgenInput.value = '';
                    }
                });
                
                // Trigger change event jika sudah ada nilai yang dipilih
                if (kodeAgenSelect.value) {
                    kodeAgenSelect.dispatchEvent(new Event('change'));
                }
            }
            
            // Script untuk provinsi dan kota (yang sudah ada)
            const provinsiSelect = document.getElementById('provinsi');
            const kotaSelect = document.getElementById('kota');
            
            const selectedProvinsi = '{{ old('provinsi', $daftartoko->provinsi) }}';
            const selectedKota = '{{ old('kota', $daftartoko->kota) }}';
            
            if (selectedProvinsi) {
                loadKota(selectedProvinsi, selectedKota);
            }
            
            provinsiSelect.addEventListener('change', function() {
                const provinsiKode = this.value;
                loadKota(provinsiKode);
            });
            
            function loadKota(provinsiKode, selectedKotaValue = '') {
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
                                if (item.kode === selectedKotaValue) {
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