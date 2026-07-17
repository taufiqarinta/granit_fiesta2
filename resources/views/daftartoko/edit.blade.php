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

        .agen-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 8px;
            border-left: 3px solid #4f46e5;
            transition: all 0.2s;
        }

        .agen-item:hover {
            background: #f1f3f5;
        }

        .agen-item .badge {
            background: #4f46e5;
            color: white;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .agen-item .badge-current {
            background: #10b981;
            color: white;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .tab-button {
            padding: 8px 16px;
            border: none;
            background: transparent;
            color: #6b7280;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
            cursor: pointer;
        }

        .tab-button.active {
            color: #4f46e5;
            border-bottom-color: #4f46e5;
        }

        .tab-button:hover {
            color: #4f46e5;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    
                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 mb-6">
                        <button class="tab-button active" data-tab="data-toko">
                            Data Toko
                        </button>
                        <button class="tab-button" data-tab="manajemen-agen" id="tabAgenBtn">
                            Manajemen Agen
                            @php
                                $totalAgen = \App\Models\DaftarToko::where('kode_toko', $daftartoko->kode_toko)
                                    ->where('status', 1)
                                    ->count();
                            @endphp
                            <span class="ml-1 bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full text-xs">
                                {{ $totalAgen }}
                            </span>
                        </button>
                    </div>

                    <!-- Tab 1: Data Toko -->
                    <div id="tab-data-toko" class="tab-content active">
                        <form method="POST" action="{{ route('daftartoko.update', $daftartoko) }}" id="tokoForm">
                            @csrf
                            @method('PUT')

                            <!-- TAMBAHKAN HIDDEN INPUT UNTUK KODE AGEN DAN NAMA AGEN -->
                            <input type="hidden" name="kode_agen" value="{{ $daftartoko->kode_agen }}">
                            <input type="hidden" name="nama_agen" value="{{ $daftartoko->nama_agen }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

                                <!-- Kode Toko -->
                                <div>
                                    <label for="kode_toko" class="block text-sm font-medium text-gray-700 mb-2">
                                        Kode Toko <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                        name="kode_toko" 
                                        id="kode_toko" 
                                        value="{{ old('kode_toko', $daftartoko->kode_toko) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kode_toko') border-red-500 @enderror bg-gray-100"
                                        style="text-transform: uppercase;"
                                        readonly>
                                    @error('kode_toko')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">* Kode toko tidak dapat diubah</p>
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

                                <!-- Alamat Lengkap -->
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

                                <!-- Kota -->
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
                                    Update Data Toko
                                </button>
                                <a href="{{ route('daftartoko.index') }}" 
                                    class="w-full sm:w-auto px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-center">
                                    Batal
                                </a>
                            </div>

                        </form>
                    </div>

                    <!-- Tab 2: Manajemen Agen -->
                    <div id="tab-manajemen-agen" class="tab-content">
                        <!-- List Agen Existing -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                                Daftar Agen untuk Toko Ini
                                <span class="text-sm font-normal text-gray-500">
                                    ({{ $totalAgen }} agen)
                                </span>
                            </h3>
                            
                            @php
                                $allAgen = \App\Models\DaftarToko::where('kode_toko', $daftartoko->kode_toko)
                                    ->where('status', 1)
                                    ->get();
                                
                                $uniqueAgen = [];
                                foreach ($allAgen as $toko) {
                                    if ($toko->kode_agen && $toko->nama_agen) {
                                        $key = $toko->kode_agen;
                                        if (!isset($uniqueAgen[$key])) {
                                            $uniqueAgen[$key] = [
                                                'kode_agen' => $toko->kode_agen,
                                                'nama_agen' => $toko->nama_agen,
                                                'id' => $toko->id,
                                                'is_current' => ($toko->id == $daftartoko->id)
                                            ];
                                        }
                                    }
                                }
                            @endphp

                            @if(count($uniqueAgen) > 0)
                                <div class="space-y-2">
                                    @foreach($uniqueAgen as $agen)
                                        <div class="agen-item {{ $agen['is_current'] ? 'border-l-4 border-l-green-500 bg-green-50' : '' }}" 
                                            data-id="{{ $agen['id'] }}"
                                            data-kode-agen="{{ $agen['kode_agen'] }}">
                                            <div class="flex-1 flex items-center gap-2">
                                                <span class="font-medium">{{ $agen['nama_agen'] }}</span>
                                                <span class="text-sm text-gray-500">({{ $agen['kode_agen'] }})</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if(count($uniqueAgen) > 1)
                                                    <button type="button" 
                                                            class="btn-hapus-agen text-sm text-red-600 hover:text-red-800"
                                                            data-id="{{ $agen['id'] }}"
                                                            data-kode-agen="{{ $agen['kode_agen'] }}"
                                                            data-nama-agen="{{ $agen['nama_agen'] }}">
                                                        <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-yellow-600">Belum ada agen yang terdaftar untuk toko ini.</p>
                            @endif
                            
                            @if(count($uniqueAgen) <= 1)
                                <div class="mt-2 text-xs text-gray-500">
                                    <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 rounded">
                                        ⚠️ Minimal harus ada 1 agen untuk setiap toko
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Form Tambah Agen Baru -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                                Tambah Agen Baru
                            </h3>
                            
                            <form method="POST" action="{{ route('daftartoko.store-agen-from-edit') }}" id="formTambahAgen">
                                @csrf
                                <input type="hidden" name="kode_toko" value="{{ $daftartoko->kode_toko }}">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Pilih Agen -->
                                    <div>
                                        <label for="kode_agen_baru" class="block text-sm font-medium text-gray-700 mb-2">
                                            Pilih Agen <span class="text-red-500">*</span>
                                        </label>
                                        
                                        @php
                                            // Ambil semua agen yang sudah terdaftar
                                            $existingAgenCodes = \App\Models\DaftarToko::where('kode_toko', $daftartoko->kode_toko)
                                                ->where('status', 1)
                                                ->pluck('kode_agen')
                                                ->toArray();
                                            
                                            // Ambil agen yang belum terdaftar
                                            $availableAgen = \App\Models\DaftarAgen::whereNotIn('kode_agen', $existingAgenCodes)
                                                ->orderBy('nama_agen', 'asc')
                                                ->get();
                                        @endphp
                                        
                                        @if($availableAgen->count() > 0)
                                            <select name="kode_agen" 
                                                    id="kode_agen_baru" 
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kode_agen') border-red-500 @enderror"
                                                    required>
                                                <option value="">- Pilih Agen -</option>
                                                @foreach($availableAgen as $agen)
                                                    <option value="{{ $agen->kode_agen }}" 
                                                        {{ old('kode_agen') == $agen->kode_agen ? 'selected' : '' }}>
                                                        {{ $agen->kode_agen }} - {{ $agen->nama_agen }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('kode_agen')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                            <p class="mt-1 text-xs text-gray-500">
                                                * Hanya menampilkan agen yang belum terdaftar untuk toko ini
                                            </p>
                                        @else
                                            <div class="bg-green-50 border-l-4 border-green-400 p-3">
                                                <p class="text-sm text-green-700">
                                                    <strong>Info:</strong> Semua agen sudah terdaftar untuk toko ini.
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Nama Agen (auto-fill) -->
                                    <div>
                                        <label for="nama_agen_baru" class="block text-sm font-medium text-gray-700 mb-2">
                                            Nama Agen <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                            name="nama_agen" 
                                            id="nama_agen_baru" 
                                            value="{{ old('nama_agen') }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nama_agen') border-red-500 @enderror"
                                            style="text-transform: uppercase;"
                                            oninput="this.value = this.value.toUpperCase();"
                                            required
                                            {{ $availableAgen->count() == 0 ? 'disabled' : '' }}>
                                        @error('nama_agen')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Tombol Tambah -->
                                <div class="mt-4">
                                    <button type="submit" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 transition-colors inline-flex items-center"
                                        {{ $availableAgen->count() == 0 ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Tambah Agen
                                    </button>
                                    <span class="ml-2 text-xs text-gray-500">
                                        Data toko akan disalin dengan agen baru
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = {
                'data-toko': document.getElementById('tab-data-toko'),
                'manajemen-agen': document.getElementById('tab-manajemen-agen')
            };
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    Object.values(tabContents).forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked button and corresponding content
                    this.classList.add('active');
                    const tabId = this.dataset.tab;
                    if (tabContents[tabId]) {
                        tabContents[tabId].classList.add('active');
                    }
                });
            });
            
            // Auto-fill nama agen ketika kode agen dipilih
            const kodeAgenSelect = document.getElementById('kode_agen_baru');
            const namaAgenInput = document.getElementById('nama_agen_baru');
            
            if (kodeAgenSelect) {
                // Data agen untuk auto-fill
                const agenData = {
                    @foreach($availableAgen ?? [] as $agen)
                        "{{ $agen->kode_agen }}": "{{ $agen->nama_agen }}",
                    @endforeach
                };
                
                kodeAgenSelect.addEventListener('change', function() {
                    const selectedKode = this.value;
                    if (selectedKode && agenData[selectedKode]) {
                        namaAgenInput.value = agenData[selectedKode];
                    } else {
                        namaAgenInput.value = '';
                    }
                });
                
                // Trigger change jika ada nilai default
                if (kodeAgenSelect.value) {
                    kodeAgenSelect.dispatchEvent(new Event('change'));
                }
            }
            
            // Check if there's an error or success message for agen tab
            @if(session('tab') == 'agen' || $errors->has('kode_agen') || $errors->has('nama_agen'))
                // Switch to agen tab
                document.querySelector('[data-tab="manajemen-agen"]')?.click();
            @endif
            
            // Handle hapus agen
            const btnHapus = document.querySelectorAll('.btn-hapus-agen');
            
            btnHapus.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const kodeAgen = this.dataset.kodeAgen;
                    const namaAgen = this.dataset.namaAgen;
                    const id = this.dataset.id;
                    const kodeToko = document.querySelector('input[name="kode_toko"]').value;
                    const currentId = {{ $daftartoko->id }};
                    
                    // Konfirmasi
                    if (!confirm(`Apakah Anda yakin ingin menghapus agen "${namaAgen}" (${kodeAgen}) dari toko ini?`)) {
                        return;
                    }
                    
                    // Disable button
                    this.disabled = true;
                    this.innerHTML = 'Menghapus...';
                    
                    // Kirim request
                    fetch('{{ route("daftartoko.remove-agen") }}', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            kode_toko: kodeToko,
                            kode_agen: kodeAgen,
                            current_id: currentId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Jika agen yang dihapus adalah agen current, redirect ke agen baru
                            if (data.redirect) {
                                window.location.href = data.redirect;
                                return;
                            }
                            
                            // Hapus element dari DOM
                            const item = this.closest('.agen-item');
                            item.style.transition = 'all 0.3s';
                            item.style.opacity = '0';
                            item.style.transform = 'translateX(-20px)';
                            
                            setTimeout(() => {
                                item.remove();
                                
                                // Update counter
                                const counter = document.querySelector('.tab-button[data-tab="manajemen-agen"] span');
                                if (counter) {
                                    const currentCount = parseInt(counter.textContent);
                                    counter.textContent = currentCount - 1;
                                }
                                
                                // Refresh halaman untuk update data
                                location.reload();
                            }, 300);
                        } else {
                            alert(data.message || 'Gagal menghapus agen');
                            this.disabled = false;
                            this.innerHTML = 'Hapus';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus agen');
                        this.disabled = false;
                        this.innerHTML = 'Hapus';
                    });
                });
            });
        });
    </script>
    
</x-app-layout>