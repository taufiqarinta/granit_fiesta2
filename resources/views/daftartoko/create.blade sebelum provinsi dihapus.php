<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Toko Baru') }}
        </h2>
    </x-slot>

    <!-- Tambahkan CSS Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Custom CSS untuk menyesuaikan Select2 dengan Tailwind -->
    <style>
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem 0.75rem !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            padding-left: 0 !important;
            color: #374151 !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 1px #6366f1 !important;
        }
        
        .select2-container {
            width: 100% !important;
        }
        
        .select2-dropdown {
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
        }
        
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem !important;
        }
        
        .select2-results__option {
            padding: 0.5rem 0.75rem !important;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    
                    <form method="POST" action="{{ route('daftartoko.store') }}" id="tokoForm">
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
                                    value="{{ $isSalesDepartment ? old('kode_agen') : auth()->user()->id_customer }}"
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
                                
                                @if($isSalesDepartment)
                                    <!-- Dropdown untuk department SLS dengan Select2 -->
                                    <select name="nama_agen" 
                                        id="nama_agen" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 select2-agen @error('nama_agen') border-red-500 @enderror"
                                        required>
                                        <option value="">- Pilih Agen -</option>
                                        @foreach($agenList as $agen)
                                            <option value="{{ $agen->nama_agen }}" 
                                                data-kode-agen="{{ $agen->kode_agen }}"
                                                {{ old('nama_agen') == $agen->nama_agen ? 'selected' : '' }}>
                                                {{ $agen->nama_agen }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <!-- Input readonly untuk department lain -->
                                    <input type="text" 
                                        name="nama_agen" 
                                        id="nama_agen" 
                                        value="{{ auth()->user()->name }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nama_agen') border-red-500 @enderror"
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
                                    value="{{ $kodeToko }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kode_toko') border-red-500 @enderror"
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
                                    value="{{ old('nama_toko') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nama_toko') border-red-500 @enderror"
                                    style="text-transform: uppercase;"
                                    oninput="this.value = this.value.toUpperCase();"
                                    required>
                                @error('nama_toko')
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
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 select2-provinsi @error('provinsi') border-red-500 @enderror"
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
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 select2-kota @error('kota') border-red-500 @enderror"
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

                            <!-- Nama Sales -->
                            <div>
                                <label for="nama_sales" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Sales <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="nama_sales" 
                                    id="nama_sales" 
                                    value="{{ old('nama_sales') }}"
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
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 select2-lokasi @error('lokasi_event') border-red-500 @enderror"
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

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200">
                            <button type="submit" 
                                class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                                Simpan
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

    <!-- Tambahkan jQuery dan Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Select2 untuk semua dropdown
            $('.select2-agen').select2({
                placeholder: '- Pilih Agen -',
                allowClear: true,
                width: '100%'
            });

            $('.select2-provinsi').select2({
                placeholder: '- Pilih Provinsi -',
                allowClear: true,
                width: '100%'
            });

            $('.select2-kota').select2({
                placeholder: '- Pilih Kota -',
                allowClear: true,
                width: '100%'
            });

            $('.select2-lokasi').select2({
                placeholder: 'Pilih Lokasi Event',
                allowClear: true,
                width: '100%'
            });

            const provinsiSelect = document.getElementById('provinsi');
            const kotaSelect = document.getElementById('kota');
            
            // Jika ada provinsi yang sudah dipilih sebelumnya (misalnya karena validasi gagal)
            // maka muat daftar kota untuk provinsi tersebut
            if (provinsiSelect.value) {
                loadKota(provinsiSelect.value);
            }
            
            // Event listener untuk perubahan provinsi
            $('#provinsi').on('change', function() {
                const provinsiKode = this.value;
                loadKota(provinsiKode);
            });
            
            function loadKota(provinsiKode) {
                // Destroy Select2 sebelum update
                $('.select2-kota').select2('destroy');
                
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
                            
                            // Re-inisialisasi Select2 untuk kota
                            $('.select2-kota').select2({
                                placeholder: '- Pilih Kota -',
                                allowClear: true,
                                width: '100%'
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching kota:', error);
                            kotaSelect.innerHTML = '<option value="">- Error memuat data -</option>';
                            kotaSelect.disabled = false;
                            
                            // Re-inisialisasi Select2 untuk kota
                            $('.select2-kota').select2({
                                placeholder: '- Pilih Kota -',
                                allowClear: true,
                                width: '100%'
                            });
                        });
                } else {
                    kotaSelect.innerHTML = '<option value="">- Pilih Kota -</option>';
                    kotaSelect.disabled = false;
                    
                    // Re-inisialisasi Select2 untuk kota
                    $('.select2-kota').select2({
                        placeholder: '- Pilih Kota -',
                        allowClear: true,
                        width: '100%'
                    });
                }
            }

            // JavaScript untuk mengisi kode_agen otomatis ketika nama_agen dipilih (hanya untuk SLS)
            @if($isSalesDepartment)
            const namaAgenSelect = document.getElementById('nama_agen');
            const kodeAgenInput = document.getElementById('kode_agen');
                
            $('#nama_agen').on('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.dataset.kodeAgen) {
                    kodeAgenInput.value = selectedOption.dataset.kodeAgen;
                } else {
                    kodeAgenInput.value = '';
                }
            });

            // Trigger change event jika sudah ada nilai yang dipilih (misalnya karena validasi gagal)
            if (namaAgenSelect.value) {
                $('#nama_agen').trigger('change');
            }
            @endif
        });
    </script>
    
</x-app-layout>