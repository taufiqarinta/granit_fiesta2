<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Daftar Kehadiran Event') }}
            </h2>
        </div>
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
        
        .max-w-9xl {
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
        <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    
                    <!-- Filter dan Stats -->
                    <div class="mb-6">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                            <!-- Filter Lokasi Event -->
                            <div class="w-full md:w-auto">
                                <label for="lokasi_event" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Lokasi Event
                                </label>
                                <select name="lokasi_event" id="lokasi_event" 
                                    class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <!-- Opsi Semua Lokasi -->
                                    <option value="semua">Semua Lokasi</option>
                                    @foreach($lokasiEvents as $lokasi)
                                        <option value="{{ $lokasi->nama_lokasi }}" 
                                            {{ $lokasiEvent == $lokasi->nama_lokasi || 
                                            (!request('lokasi_event') && $defaultLokasi && $lokasi->nama_lokasi == $defaultLokasi->nama_lokasi) ? 'selected' : '' }}>
                                            {{ $lokasi->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tombol Export Excel -->
                            <div>
                                <button onclick="exportToExcel()" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Export Excel
                                </button>
                            </div>
                        </div>

                        <!-- Statistik -->
                        <div class="statistik flex flex-wrap gap-4 mb-4">
                            <div class="stat-item flex items-center gap-2">
                                <strong class="text-sm">Total Undangan</strong> 
                                <span id="totalPeserta" class="badge bg-gray-600 text-white px-3 py-1 rounded-full text-xs font-bold">0</span>
                            </div>
                            <div class="stat-item flex items-center gap-2">
                                <strong class="text-sm">Jumlah Hadir</strong> 
                                <span id="totalHadir" class="badge bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold">0</span>
                            </div>
                            <div class="stat-item flex items-center gap-2">
                                <strong class="text-sm">Jumlah Belum Hadir</strong> 
                                <span id="totalTidakHadir" class="badge bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold">0</span>
                            </div>
                            <div class="stat-item flex items-center gap-2">
                                <strong class="text-sm">Total Kehadiran Peserta</strong> 
                                <span id="totalJumlahKehadiran" class="badge bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold">0</span>
                            </div>
                        </div>

                        <!-- Search Bar -->
                        <div class="flex gap-3 items-center">
                            <input type="text" id="searchInput" placeholder="Cari nama pelanggan/agen, PIC, alamat, kota..." 
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button id="clearBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Container Tabel dengan Scroll Horizontal dan Vertical -->
                    <div class="table-wrapper" style="max-height: 600px; overflow: auto; border: 1px solid #e5e7eb; border-radius: 8px; position: relative;">
                        <table id="tabelDaftarToko" style="min-width: 1400px; width: 100%; border-collapse: collapse; background: white;">
                            <thead style="position: sticky; top: 0; z-index: 20; background-color: #f9fafb;">
                                <tr>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; background: #f9fafb; z-index: 30; min-width: 60px;">No</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 100px;">Jumlah Hadir</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; position: sticky; right: 0; background: #f9fafb; z-index: 30; min-width: 80px;">Hadir</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 100px;">Tipe</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Kode Pelanggan</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 200px;">Nama Pelanggan</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 150px;">PIC</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Nomor PIC</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Kota</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Kode Agen</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 150px;">Nama Agen</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 150px;">Nama Sales</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 250px;">Alamat</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 100px;">Waktu Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gabunganData as $index => $item)
                                    <tr id="row-{{ $item['id'] }}" 
                                        style="{{ $item['hadir'] ? 'background-color: #f0fdf4;' : '' }} transition: background-color 0.15s ease-in-out;">
                                        <!-- Nomor Urut -->
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.875rem; color: #111827; font-weight: 500; background: inherit;">
                                            {{ $index + 1 }}
                                        </td>

                                        <!-- Kolom Jumlah Kehadiran -->
                                        <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: center; background: inherit;">
                                            <input type="number" 
                                                   id="jumlah-kehadiran-{{ $item['id'] }}"
                                                   value="{{ $item['jumlah_kehadiran'] }}"
                                                   min="0"
                                                   oninput="ubahJumlahKehadiranDebounced('{{ $item['id'] }}', this.value)"
                                                   style="width: 80px; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; text-align: center; font-size: 0.875rem;"
                                                   class="focus:border-indigo-500 focus:ring-indigo-500"
                                                    onfocus="if(this.value === '0') this.value = '';"
                                                    onblur="if(this.value === '') this.value = '0';">    
                                        </td>
                                        
                                        <!-- Kolom Hadir - Sticky -->
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; position: sticky; right: 0; background: inherit; z-index: 15;">
                                            <label style="display: inline-flex; align-items: center; cursor: pointer;">
                                                <input type="checkbox" 
                                                       {{ $item['hadir'] ? 'checked' : '' }}
                                                       onchange="ubahHadir('{{ $item['id'] }}', this.checked)"
                                                       style="border-radius: 0.25rem; border: 1px solid #d1d5db; color: #4f46e5; height: 1.25rem; width: 1.25rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                                            </label>
                                        </td>
                                        
                                        <!-- Kolom Tipe -->
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $item['type'] == 'toko' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ strtoupper($item['type']) }}
                                            </span>
                                        </td>
                                        
                                        <!-- Data Kolom -->
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827; white-space: nowrap;">
                                            {{ $item['kode_toko'] }}
                                        </td>
                                        
                                        <!-- Nama Toko (Editable) -->
                                        <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 0.875rem; color: #111827; background: inherit;">
                                            <input type="text" 
                                                   id="nama-toko-{{ $item['id'] }}"
                                                   value="{{ $item['nama_toko'] }}"
                                                   onchange="ubahDataDebounced('{{ $item['id'] }}', 'nama_toko', this.value)"
                                                   style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 0.875rem; background: transparent; text-transform: uppercase;"
                                                   class="editable-field focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        
                                        <!-- PIC (Editable) -->
                                        <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 0.875rem; color: #111827; background: inherit;">
                                            <input type="text" 
                                                   id="pic-{{ $item['id'] }}"
                                                   value="{{ $item['pic'] }}"
                                                   onchange="ubahDataDebounced('{{ $item['id'] }}', 'pic', this.value)"
                                                   style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 0.875rem; background: transparent; text-transform: uppercase;"
                                                   class="editable-field focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        
                                        <!-- Nomor PIC (Editable) -->
                                        <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 0.875rem; color: #111827; background: inherit;">
                                            <input type="text" 
                                                   id="nomor-pic-{{ $item['id'] }}"
                                                   value="{{ $item['nomor_pic'] }}"
                                                   onchange="ubahDataDebounced('{{ $item['id'] }}', 'nomor_pic', this.value)"
                                                   style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 0.875rem; background: transparent; text-transform: uppercase;"
                                                   class="editable-field focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        
                                        <!-- Kota (Editable) -->
                                        <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 0.875rem; color: #111827; background: inherit;">
                                            <input type="text" 
                                                   id="kota-{{ $item['id'] }}"
                                                   value="{{ $item['kota'] }}"
                                                   onchange="ubahDataDebounced('{{ $item['id'] }}', 'kota', this.value)"
                                                   style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 0.875rem; background: transparent; text-transform: uppercase;"
                                                   class="editable-field focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>

                                        <!-- Kolom Kode Agen -->
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827; white-space: nowrap;">
                                            @if(isset($item['agen_info']) && count($item['agen_info']) > 0)
                                                @foreach($item['agen_info'] as $index => $agen)
                                                    @if(!$loop->first)<br>@endif
                                                    <div class="agen-item {{ $index > 0 ? 'mt-1' : '' }}">
                                                        {{ $agen['kode_agen'] }}
                                                    </div>
                                                @endforeach
                                            @else
                                                {{ $item['kode_agen'] }}
                                            @endif
                                        </td>

                                        <!-- Kolom Nama Agen -->
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            @if(isset($item['agen_info']) && count($item['agen_info']) > 0)
                                                @foreach($item['agen_info'] as $index => $agen)
                                                    @if(!$loop->first)<br>@endif
                                                    <div class="agen-item {{ $index > 0 ? 'mt-1' : '' }}" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $agen['nama_agen'] }}">
                                                        {{ $agen['nama_agen'] }}
                                                    </div>
                                                @endforeach
                                            @else
                                                <div style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item['nama_agen'] }}">
                                                    {{ $item['nama_agen'] }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Kolom Nama Sales -->
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            @if(isset($item['agen_info']) && count($item['agen_info']) > 0)
                                                @foreach($item['agen_info'] as $index => $agen)
                                                    @if(!$loop->first)<br>@endif
                                                    <div class="agen-item {{ $index > 0 ? 'mt-1' : '' }}" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $agen['nama_sales'] }}">
                                                        {{ $agen['nama_sales'] }}
                                                    </div>
                                                @endforeach
                                            @else
                                                <div style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item['nama_sales'] }}">
                                                    {{ $item['nama_sales'] }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Alamat (Editable) -->
                                        <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 0.875rem; color: #111827; background: inherit;">
                                            <textarea 
                                                id="alamat-{{ $item['id'] }}"
                                                onchange="ubahDataDebounced('{{ $item['id'] }}', 'alamat', this.value)"
                                                style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 0.875rem; background: transparent; resize: vertical; min-height: 60px; text-transform: uppercase;"
                                                class="editable-field focus:border-indigo-500 focus:ring-indigo-500">{{ $item['alamat'] }}</textarea>
                                        </td>

                                        <!-- Kolom Waktu Kehadiran -->
                                        <td id="waktu-{{ $item['id'] }}" style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.875rem; color: #111827; white-space: nowrap;">
                                            @if($item['waktu_kehadiran'])
                                                {{ \Carbon\Carbon::parse($item['waktu_kehadiran'])->format('H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                
                                @if(count($gabunganData) == 0)
                                    <tr>
                                        <td colspan="15" style="border: 1px solid #e5e7eb; padding: 32px; text-align: center; font-size: 0.875rem; color: #6b7280;">
                                            Tidak ada data untuk lokasi event "{{ $lokasiEvent }}"
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Info Tabel Responsif -->
                    <div style="margin-top: 16px; font-size: 0.75rem; color: #6b7280; text-align: center;">
                        <span>📱 Geser tabel ke samping untuk melihat kolom lainnya • Scroll ke bawah untuk melihat lebih banyak data</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://granit-fiesta.kobin.co.id/socket.io.min.js"></script>
    <script>
        const socket = io("https://nodejs.kobin.co.id:443");
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Fungsi Export Excel
        function exportToExcel() {
            const lokasiEvent = document.getElementById('lokasi_event').value;
            window.location.href = `{{ route('kehadiran.export') }}?lokasi_event=${encodeURIComponent(lokasiEvent)}`;
        }

        socket.on('connect', () => {
            console.log('Socket connected:', socket.id);
        });

        socket.on('disconnect', () => {
            console.log('Socket disconnected');
        });

        // Socket listener untuk update real-time
        socket.on("updateHadir", (data) => {
            // Parse all_ids — bisa string "toko_1,toko_2,toko_3" atau array
            const ids = data.all_ids
                ? String(data.all_ids).split(',').map(s => s.trim()).filter(Boolean)
                : [data.id];

            ids.forEach(rowId => {
                const row = document.getElementById("row-" + rowId);
                if (!row) return;

                if (data.hadir !== undefined) {
                    const checkbox = row.querySelector("input[type=checkbox]");
                    if (checkbox) checkbox.checked = data.hadir == 1;
                    row.style.backgroundColor = data.hadir == 1 ? '#f0fdf4' : '';
                }

                if (data.jumlah_kehadiran !== undefined) {
                    const inputJumlah = document.getElementById("jumlah-kehadiran-" + rowId);
                    if (inputJumlah) inputJumlah.value = data.jumlah_kehadiran;
                }

                if (data.nama_toko !== undefined) {
                    const el = document.getElementById("nama-toko-" + rowId);
                    if (el) el.value = data.nama_toko;
                }

                if (data.pic !== undefined) {
                    const el = document.getElementById("pic-" + rowId);
                    if (el) el.value = data.pic;
                }

                if (data.nomor_pic !== undefined) {
                    const el = document.getElementById("nomor-pic-" + rowId);
                    if (el) el.value = data.nomor_pic;
                }

                if (data.alamat !== undefined) {
                    const el = document.getElementById("alamat-" + rowId);
                    if (el) el.value = data.alamat;
                }

                if (data.kota !== undefined) {
                    const el = document.getElementById("kota-" + rowId);
                    if (el) el.value = data.kota;
                }

                const waktuElement = document.getElementById("waktu-" + rowId);
                if (waktuElement && data.waktu_kehadiran !== undefined) {
                    if (!data.waktu_kehadiran || data.waktu_kehadiran === 'null') {
                        waktuElement.textContent = '-';
                    } else {
                        const parts = data.waktu_kehadiran.split(':');
                        if (parts.length >= 2) waktuElement.textContent = parts[0] + ':' + parts[1];
                    }
                }
            });

            hitungStatistik();
        });

        // Fungsi untuk mengubah status kehadiran
        function ubahHadir(id, status) {
            const row = document.getElementById("row-" + id);
            
            fetch("{{ route('kehadiran.update') }}", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    id: id,
                    hadir: status ? 1 : 0
                })
            }).then(response => response.json())
            .then(data => {
                if (row) {
                    if (status) {
                        row.style.backgroundColor = '#f0fdf4';
                    } else {
                        row.style.backgroundColor = '';
                    }
                }
                hitungStatistik();
            });
        }

        // Fungsi untuk mengubah jumlah kehadiran
        function ubahJumlahKehadiran(id, jumlah) {
            const input = document.getElementById("jumlah-kehadiran-" + id);
            
            if (input) {
                input.style.borderColor = '#3b82f6';
                input.style.boxShadow = '0 0 0 1px #3b82f6';
            }

            fetch("{{ route('kehadiran.update') }}", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    id: id,
                    jumlah_kehadiran: jumlah
                })
            }).then(response => response.json())
            .then(data => {
                if (input) {
                    input.style.borderColor = '#10b981';
                    input.style.boxShadow = '0 0 0 1px #10b981';
                    
                    setTimeout(() => {
                        input.style.borderColor = '#d1d5db';
                        input.style.boxShadow = 'none';
                    }, 1000);
                }
                hitungStatistik();
            })
            .catch(error => {
                console.error('Error updating jumlah kehadiran:', error);
                if (input) {
                    input.style.borderColor = '#ef4444';
                    input.style.boxShadow = '0 0 0 1px #ef4444';
                    
                    setTimeout(() => {
                        input.style.borderColor = '#d1d5db';
                        input.style.boxShadow = 'none';
                    }, 2000);
                }
            });
        }

        // Fungsi untuk mengubah data lainnya (nama toko, PIC, dll)
        function ubahData(id, field, value) {
            const input = document.getElementById(field + "-" + id);
            
            if (input) {
                input.style.borderColor = '#3b82f6';
                input.style.boxShadow = '0 0 0 1px #3b82f6';
            }

            fetch("{{ route('kehadiran.update') }}", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    id: id,
                    [field]: value
                })
            }).then(response => response.json())
            .then(data => {
                if (input) {
                    input.style.borderColor = '#10b981';
                    input.style.boxShadow = '0 0 0 1px #10b981';
                    
                    setTimeout(() => {
                        input.style.borderColor = '#d1d5db';
                        input.style.boxShadow = 'none';
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Error updating ' + field + ':', error);
                if (input) {
                    input.style.borderColor = '#ef4444';
                    input.style.boxShadow = '0 0 0 1px #ef4444';
                    
                    setTimeout(() => {
                        input.style.borderColor = '#d1d5db';
                        input.style.boxShadow = 'none';
                    }, 2000);
                }
            });
        }

        const debounceTimers = {};

        // Fungsi untuk mengubah jumlah kehadiran dengan debounce
        function ubahJumlahKehadiranDebounced(id, jumlah) {
            if (debounceTimers[id]) {
                clearTimeout(debounceTimers[id]);
            }

            if (jumlah === '' || jumlah === null || jumlah === undefined || parseInt(jumlah) < 0) {
                jumlah = 0;
            } else {
                jumlah = parseInt(jumlah);
            }

            const input = document.getElementById("jumlah-kehadiran-" + id);
            if (input) {
                input.style.borderColor = '#fbbf24';
                input.style.boxShadow = '0 0 0 1px #fbbf24';
            }

            debounceTimers[id] = setTimeout(() => {
                if (input) {
                    input.value = jumlah;
                }
                ubahJumlahKehadiran(id, jumlah);
            }, 800);

            hitungStatistik();
        }

        // Fungsi untuk mengubah data lainnya dengan debounce
        function ubahDataDebounced(id, field, value) {
            const timerKey = id + '_' + field;
            
            if (debounceTimers[timerKey]) {
                clearTimeout(debounceTimers[timerKey]);
            }

            const input = document.getElementById(field + "-" + id);
            if (input) {
                input.style.borderColor = '#fbbf24';
                input.style.boxShadow = '0 0 0 1px #fbbf24';
            }

            debounceTimers[timerKey] = setTimeout(() => {
                ubahData(id, field, value);
            }, 800);
        }

        // Fungsi menghitung statistik
        function hitungStatistik() {
            const rows = document.querySelectorAll("#tabelDaftarToko tbody tr");
            let total = 0, hadir = 0, tidakHadir = 0, totalJumlahKehadiran = 0;

            rows.forEach(row => {
                if (row.cells.length > 1 && row.style.display !== 'none') {
                    total++;
                    const checkbox = row.querySelector("input[type=checkbox]");
                    const inputJumlah = row.querySelector("input[type=number]");
                    
                    if (checkbox && checkbox.checked) {
                        hadir++;
                    } else {
                        tidakHadir++;
                    }
                    
                    if (inputJumlah) {
                        const nilai = inputJumlah.value === '' ? 0 : parseInt(inputJumlah.value);
                        totalJumlahKehadiran += nilai || 0;
                    }
                }
            });

            document.getElementById("totalPeserta").innerText = total;
            document.getElementById("totalHadir").innerText = hadir;
            document.getElementById("totalTidakHadir").innerText = tidakHadir;
            document.getElementById("totalJumlahKehadiran").innerText = totalJumlahKehadiran;
        }

        // Search functionality
        const input = document.getElementById('searchInput');
        const clearBtn = document.getElementById('clearBtn');

        input.addEventListener('keyup', function() {
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll("#tabelDaftarToko tbody tr");

            rows.forEach(row => {
                if (row.cells.length > 1) {
                    let searchText = '';
                    
                    // Ambil teks dari semua cell termasuk nilai input/textarea
                    const cells = row.querySelectorAll('td');
                    cells.forEach((cell, index) => {
                        // Skip kolom pertama (No) dan kolom checkbox
                        if (index === 0 || index === 2) return;
                        
                        // Cek jika cell berisi input/textarea
                        const inputElement = cell.querySelector('input[type="text"]');
                        const textareaElement = cell.querySelector('textarea');
                        const numberInput = cell.querySelector('input[type="number"]');
                        
                        if (inputElement) {
                            searchText += ' ' + inputElement.value.toLowerCase();
                        } else if (textareaElement) {
                            searchText += ' ' + textareaElement.value.toLowerCase();
                        } else if (numberInput) {
                            searchText += ' ' + numberInput.value.toLowerCase();
                        } else {
                            // Untuk cell biasa, ambil textContent
                            searchText += ' ' + cell.textContent.toLowerCase();
                        }
                    });

                    // Tambahkan juga teks dari span (tipe)
                    const typeSpan = row.querySelector('span');
                    if (typeSpan) {
                        searchText += ' ' + typeSpan.textContent.toLowerCase();
                    }

                    row.style.display = searchText.indexOf(filter) > -1 ? "" : "none";
                }
            });
            
            hitungStatistik();
        });

        clearBtn.addEventListener('click', () => {
            input.value = '';
            const rows = document.querySelectorAll("#tabelDaftarToko tbody tr");
            rows.forEach(row => {
                if (row.cells.length > 1) {
                    row.style.display = "";
                }
            });
            hitungStatistik();
            input.focus();
        });

        // Filter lokasi event
        document.getElementById('lokasi_event').addEventListener('change', function() {
            const lokasiEvent = this.value;
            window.location.href = `?lokasi_event=${encodeURIComponent(lokasiEvent)}`;
        });

        // Initial calculation
        document.addEventListener('DOMContentLoaded', hitungStatistik);
    </script>

    <style>
        .agen-item {
            line-height: 1.3;
        }

        .agen-item:not(:first-child) {
            border-top: 1px dashed #e5e7eb;
            padding-top: 4px;
        }
        
        .table-wrapper::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }
        
        .table-wrapper::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 6px;
        }
        
        .table-wrapper::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 6px;
            border: 2px solid #f7fafc;
        }
        
        .table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .table-wrapper {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        table th:first-child,
        table td:first-child {
            box-shadow: 2px 0 4px -1px rgba(0,0,0,0.1);
        }

        table th:last-child,
        table td:last-child {
            box-shadow: -2px 0 4px -1px rgba(0,0,0,0.1);
        }

        .table-wrapper thead th {
            box-shadow: 0 2px 4px -1px rgba(0,0,0,0.1);
        }

        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        input[type="number"] {
            -moz-appearance: textfield;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .editable-field {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background: transparent !important;
        }

        .editable-field:focus {
            background: white !important;
            z-index: 10;
            position: relative;
        }
    </style>
</x-app-layout>