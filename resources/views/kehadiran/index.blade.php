<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Daftar Kehadiran Event') }}
            </h2>
        </div>
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
                                    class="p-2 block mt-1 w-full h-[42px] rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                            <input type="text" id="searchInput" placeholder="Cari nama pelanggan/agen, PIC, alamat, kota, email..." 
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button id="clearBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Container Tabel dengan Scroll Horizontal dan Vertical -->
                    <div class="table-wrapper" style="max-height: 600px; overflow: auto; border: 1px solid #e5e7eb; border-radius: 8px; position: relative;">
                        <table id="tabelDaftarToko" style="min-width: 1600px; width: 100%; border-collapse: collapse; background: white;">
                            <thead style="position: sticky; top: 0; z-index: 20; background-color: #f9fafb;">
                                <tr>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; background: #f9fafb; z-index: 30; min-width: 60px;">No</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 100px;">Jumlah Hadir</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; position: sticky; right: 90px; background: #f9fafb; z-index: 30; min-width: 80px;">Hadir</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; position: sticky; right: 45px; background: #f9fafb; z-index: 30; min-width: 110px;">Send Link</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; position: sticky; right: 0; background: #f9fafb; z-index: 30; min-width: 90px; display: none;">
                                        Print QR
                                    </th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 100px;">Tipe</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Kode Pelanggan</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 200px;">Nama Pelanggan</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 150px;">PIC</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Nomor PIC</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 180px;">Email</th>
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
                                        data-kode-toko="{{ $item['kode_toko'] }}"
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
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; position: sticky; right: 90px; background: inherit; z-index: 15;">
                                            <label style="display: inline-flex; align-items: center; cursor: pointer;">
                                                <input type="checkbox" 
                                                    {{ $item['hadir'] ? 'checked' : '' }}
                                                    onchange="ubahHadir('{{ $item['id'] }}', this.checked)"
                                                    style="border-radius: 0.25rem; border: 1px solid #d1d5db; color: #4f46e5; height: 1.25rem; width: 1.25rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                                            </label>
                                        </td>

                                        <!-- Kolom Send Link - Sticky -->
                                        <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: center; position: sticky; right: 45px; background: inherit; z-index: 15;">
                                            <div style="display:flex; gap:6px; justify-content:center;">
                                                <button type="button" 
                                                        id="btn-wa-{{ $item['id'] }}"
                                                        class="btn-send-wa {{ $item['wa_terkirim'] ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 hover:bg-gray-500' }} text-white px-3 py-1 rounded-md text-xs font-medium" 
                                                        onclick="handleSendLink('{{ $item['id'] }}')" title="Kirim via WA">By WA</button>
                                                <button type="button" 
                                                        id="btn-email-{{ $item['id'] }}"
                                                        class="btn-send-email {{ $item['email_terkirim'] ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 hover:bg-gray-500' }} text-white px-3 py-1 rounded-md text-xs font-medium" 
                                                        onclick="handleSendEmail('{{ $item['id'] }}')" title="Kirim via Email">By Email</button>
                                            </div>
                                        </td>

                                        <!-- Kolom Print QR - Sticky (button hidden but kept in DOM) -->
                                        <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: center; position: sticky; right: 0; background: inherit; z-index: 15; display: none;">
                                            <button type="button" class="hidden"
                                                    onclick="printQRCode('{{ $item['id'] }}')"
                                                    style="background: #4f46e5; color: white; border: none; padding: 6px 10px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; white-space: nowrap;"
                                                    title="Print QR Code">
                                                Print
                                            </button>
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

                                        <!-- Email (Editable) -->
                                        <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 0.875rem; color: #111827; background: inherit;">
                                            <input type="email" 
                                                   id="email-{{ $item['id'] }}"
                                                   value="{{ $item['email'] ?? '' }}"
                                                   onchange="ubahDataDebounced('{{ $item['id'] }}', 'email', this.value)"
                                                   style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 0.875rem; background: transparent;"
                                                   class="editable-field focus:border-indigo-500 focus:ring-indigo-500"
                                                   placeholder="Email">
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
                                        <td colspan="17" style="border: 1px solid #e5e7eb; padding: 32px; text-align: center; font-size: 0.875rem; color: #6b7280;">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

                if (data.wa_terkirim !== undefined) {
                    markSentButton('wa', rowId, data.wa_terkirim == 1);
                }
                if (data.email_terkirim !== undefined) {
                    markSentButton('email', rowId, data.email_terkirim == 1);
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

                if (data.email !== undefined) {
                    const el = document.getElementById("email-" + rowId);
                    if (el) el.value = data.email;
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
                        const emailElement = cell.querySelector('input[type="email"]');
                        const textareaElement = cell.querySelector('textarea');
                        const numberInput = cell.querySelector('input[type="number"]');
                        
                        if (inputElement) {
                            searchText += ' ' + inputElement.value.toLowerCase();
                        } else if (emailElement) {
                            searchText += ' ' + emailElement.value.toLowerCase();
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

        // ============ PRINT QR CODE ============
        let isPrintingQR = false; // guard biar gak kepencet 2x / kebuka 2 window

        async function printQRCode(id) {
            if (isPrintingQR) return;
            isPrintingQR = true;

            const row = document.getElementById("row-" + id);
            if (!row) { isPrintingQR = false; return; }

            const kodeToko = row.dataset.kodeToko || '-';
            const namaTokoEl = document.getElementById('nama-toko-' + id);
            const picEl = document.getElementById('pic-' + id);
            const alamatEl = document.getElementById('alamat-' + id);

            const namaToko = namaTokoEl ? namaTokoEl.value : '-';
            const pic = picEl ? picEl.value : '-';
            const alamat = alamatEl ? alamatEl.value : '-';

            if (!kodeToko || kodeToko === '-') {
                alert('Kode toko/agen tidak ditemukan');
                isPrintingQR = false;
                return;
            }

            try {
                const response = await fetch(`/kehadiran/qr-code/${encodeURIComponent(kodeToko)}`);
                const result = await response.json();

                if (!result.success) {
                    alert('Gagal membuat QR Code');
                    return;
                }

                // Kirim data termasuk type dari response
                openPrintWindowQR({
                    kodeToko,
                    namaToko: result.nama || namaToko,
                    pic: result.pic || pic,
                    alamat: result.alamat || alamat,
                    qrDataUrl: result.qr_base64,
                    type: result.type || 'pelanggan' // 'pelanggan' atau 'agen'
                });
            } catch (err) {
                console.error('Gagal generate QR:', err);
                alert('Gagal membuat QR Code');
            } finally {
                setTimeout(() => { isPrintingQR = false; }, 1000);
            }
        }

        function openPrintWindowQR(data) {
            const printWindow = window.open('', '_blank', 'width=800,height=600');
            if (!printWindow) {
                alert('Popup diblokir browser. Izinkan popup untuk halaman ini.');
                return;
            }

            // Tentukan label berdasarkan type
            const labelQR = data.type === 'agen' ? 'QR AGEN' : 'QR PELANGGAN';
            const labelColor = data.type === 'agen' ? '#7c3aed' : '#2563eb'; // Ungu untuk agen, Biru untuk pelanggan

            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>Print QR - ${data.kodeToko}</title>
                    <style>
                        @page { 
                            size: 80mm 110mm; 
                            margin: 3mm; 
                        }
                        * { 
                            box-sizing: border-box; 
                            margin: 0; 
                            padding: 0; 
                        }
                        body {
                            font-family: 'Segoe UI', Arial, sans-serif;
                            width: 94mm;
                            padding: 8px 4px;
                            text-align: center;
                            background: white;
                        }
                        .qr-label {
                            display: inline-block;
                            font-size: 15px;
                            font-weight: 700;
                            letter-spacing: 2px;
                            text-transform: uppercase;
                            padding: 3px 12px;
                            border-radius: 12px;
                            background: ${labelColor};
                            color: white;
                            margin-bottom: 6px;
                            border: 1px solid ${labelColor};
                        }
                        .pic {
                            font-size: 20px;
                            font-weight: bold;
                            text-transform: uppercase;
                            margin-bottom: 6px;
                            color: #1e293b;
                        }
                        .qr-wrap { 
                            margin: 4px 0; 
                        }
                        .qr-wrap img { 
                            width: 42mm; 
                            height: 42mm; 
                            border: 1px solid #e5e7eb;
                            border-radius: 4px;
                            padding: 2px;
                        }
                        .kode-toko {
                            font-size: 16px;
                            font-weight: bold;
                            margin-top: 4px;
                            color: #0f172a;
                            font-family: 'Courier New', monospace;
                            letter-spacing: 1px;
                        }
                        .nama-toko {
                            font-size: 15px;
                            font-weight: bold;
                            text-transform: uppercase;
                            margin-top: 2px;
                            color: #1e293b;
                        }
                        .alamat {
                            font-size: 12px;
                            margin-top: 4px;
                            word-wrap: break-word;
                            color: #475569;
                            line-height: 1.4;
                            max-width: 90%;
                            margin-left: auto;
                            margin-right: auto;
                        }
                        .divider {
                            border: none;
                            border-top: 1px dashed #e5e7eb;
                            margin: 6px auto;
                            width: 80%;
                        }
                        .footer {
                            font-size: 8px;
                            color: #94a3b8;
                            margin-top: 6px;
                        }
                    </style>
                </head>
                <body>
                    <!-- LABEL QR -->
                    <div class="qr-label">${labelQR}</div>
                    
                    <!-- NAMA PIC -->
                    <div class="pic">${data.pic}</div>
                    
                    <!-- QR CODE -->
                    <div class="qr-wrap"><img src="${data.qrDataUrl}" /></div>
                    
                    <!-- KODE -->
                    <div class="kode-toko">${data.kodeToko}</div>
                    
                    <!-- NAMA -->
                    <div class="nama-toko">${data.namaToko}</div>
                    
                    <!-- ALAMAT -->
                    <div class="alamat">${data.alamat}</div>
                    
                    <hr class="divider">
                    
                    <!-- FOOTER -->
                    <div class="footer">Dicetak: ${new Date().toLocaleString('id-ID')}</div>
                </body>
                </html>
            `);

            printWindow.document.close();

            printWindow.onload = function () {
                printWindow.focus();
                printWindow.print();

                printWindow.onafterprint = function () {
                    printWindow.close();
                };

                setTimeout(function () {
                    if (!printWindow.closed) {
                        printWindow.close();
                    }
                }, 5000);
            };
        }

        // ===== SEND LINK (WA / Email) =====
        function gatherAgenListsFromRow(row) {
            const kodeAgenCell = row.cells[12];
            const namaAgenCell = row.cells[13];

            let kodeAgenList = [];
            if (kodeAgenCell) {
                const items = kodeAgenCell.querySelectorAll('.agen-item');
                if (items.length) kodeAgenList = Array.from(items).map(el => el.textContent.trim());
                else kodeAgenList = kodeAgenCell.textContent.split(',').map(s => s.trim()).filter(Boolean);
            }

            let namaAgenList = [];
            if (namaAgenCell) {
                const items = namaAgenCell.querySelectorAll('.agen-item');
                if (items.length) namaAgenList = Array.from(items).map(el => el.textContent.trim());
                else namaAgenList = namaAgenCell.textContent.split(',').map(s => s.trim()).filter(Boolean);
            }

            return { kodeAgenList, namaAgenList };
        }

        function getLokasiEvent() {
            const selectElement = document.getElementById('lokasi_event');
            return selectElement ? selectElement.value : 'Event';
        }

        function buildSendMessageForRow(id) {
            const row = document.getElementById('row-' + id);
            if (!row) return '';

            const lokasiEvent = getLokasiEvent();
            const kodeToko = row.dataset.kodeToko || '-';
            const namaToko = (document.getElementById('nama-toko-' + id)?.value || '-');
            const pic = (document.getElementById('pic-' + id)?.value || '-');
            const alamat = (document.getElementById('alamat-' + id)?.value || '-');

            const { kodeAgenList, namaAgenList } = gatherAgenListsFromRow(row);

            let msg = '';

            // Header dengan bold menggunakan karakter asterisk untuk WhatsApp
            msg += '*Granite Fiesta 2.0 - ' + lokasiEvent + '*\n\n';

            // Informasi Toko
            msg += '*Kode Toko* : ' + kodeToko + '\n';
            msg += '*Nama Toko* : ' + namaToko + '\n\n';

            // Informasi Agen
            if (kodeAgenList.length > 0) {
                if (kodeAgenList.length === 1) {
                    // Hanya 1 agen, tanpa angka
                    const payload = { kode_toko: kodeToko, kode_agen: kodeAgenList[0] };
                    const b64 = btoa(JSON.stringify(payload));
                    const link = `${location.origin}/inputformorder?d=${encodeURIComponent(b64)}`;

                    msg += '*Agen* : ' + '\n';
                    msg += '* Nama Agen : ' + (namaAgenList[0] || '-') + '\n';
                    msg += '* Order Paket : ' + link + '\n\n';
                } else {
                    // Multiple agen, dengan angka
                    kodeAgenList.forEach((kode, i) => {
                        const namaAgen = namaAgenList[i] || '-';
                        const payload = { kode_toko: kodeToko, kode_agen: kode };
                        const b64 = btoa(JSON.stringify(payload));
                        const link = `${location.origin}/inputformorder?d=${encodeURIComponent(b64)}`;

                        msg += '*Agen ' + '\n';
                        msg += '* Nama Agen : ' + namaAgen + '\n';
                        msg += '* Order Paket : ' + link + '\n\n';
                    });
                }
            }

            // Doorprize dan Cek Voucher
            msg += '*Doorprize* : https://granit-fiesta2.kobin.co.id/cek-voucher\n';
            msg += '*Cek Voucher* : https://granit-fiesta2.kobin.co.id/cek-voucher\n';

            return msg;
        }


        async function sendViaWA(number, message) {
            try {
                const form = new FormData();
                form.append('action', 'send');
                form.append('number', number);
                form.append('message', message);

                const res = await fetch('/wa_api.php', { method: 'POST', body: form });
                const text = await res.text();
                try {
                    return JSON.parse(text);
                } catch (e) {
                    return { status: false, error: 'Invalid response from WA service', raw: text };
                }
            } catch (e) {
                return { status: false, error: e.message };
            }
        }

        function buildEmailBodyForRow(id) {
            const row = document.getElementById('row-' + id);
            if (!row) return '';

            const lokasiEvent = getLokasiEvent();
            const kodeToko = row.dataset.kodeToko || '-';
            const namaToko = (document.getElementById('nama-toko-' + id)?.value || '-');
            const pic = (document.getElementById('pic-' + id)?.value || '-');
            const alamat = (document.getElementById('alamat-' + id)?.value || '-');

            const { kodeAgenList, namaAgenList } = gatherAgenListsFromRow(row);

            let body = '';

            // Informasi Toko (tanpa bold di email karena HTML)
            body += '<strong>Kode Toko</strong> : ' + kodeToko + '<br>';
            body += '<strong>Nama Toko</strong> : ' + namaToko + '<br><br>';

            // Informasi Agen
            if (kodeAgenList.length > 0) {
                if (kodeAgenList.length === 1) {
                    // Hanya 1 agen, tanpa angka
                    const payload = { kode_toko: kodeToko, kode_agen: kodeAgenList[0] };
                    const b64 = btoa(JSON.stringify(payload));
                    const link = `${location.origin}/inputformorder?d=${encodeURIComponent(b64)}`;

                    body += '<strong>Agen</strong> : ' + '<br>';
                    body += '&nbsp;&nbsp;* Nama Agen : ' + (namaAgenList[0] || '-') + '<br>';
                    body += '&nbsp;&nbsp;* Order Paket : <a href="' + link + '">' + link + '</a><br><br>';
                } else {
                    // Multiple agen, dengan angka
                    kodeAgenList.forEach((kode, i) => {
                        const namaAgen = namaAgenList[i] || '-';
                        const payload = { kode_toko: kodeToko, kode_agen: kode };
                        const b64 = btoa(JSON.stringify(payload));
                        const link = `${location.origin}/inputformorder?d=${encodeURIComponent(b64)}`;

                        body += '<strong>Agen ' + '<br>';
                        body += '&nbsp;&nbsp;* Nama Agen : ' + namaAgen + '<br>';
                        body += '&nbsp;&nbsp;* Order Paket : <a href="' + link + '">' + link + '</a><br><br>';
                    });
                }
            }

            // Doorprize dan Cek Voucher
            body += '<strong>Doorprize</strong> : <a href="https://granit-fiesta2.kobin.co.id/cek-voucher">https://granit-fiesta2.kobin.co.id/cek-voucher</a><br>';
            body += '<strong>Cek Voucher</strong> : <a href="https://granit-fiesta2.kobin.co.id/cek-voucher">https://granit-fiesta2.kobin.co.id/cek-voucher</a><br>';

            return body;
        }

        async function handleSendLink(id) {
            const msg = buildSendMessageForRow(id);
            const nomorEl = document.getElementById('nomor-pic-' + id);
            const raw = nomorEl ? (nomorEl.value || '') : '';
            let number = String(raw).replace(/[^0-9]/g, '');
            if (!number) { Swal.fire({icon:'error', title: 'Nomor tidak tersedia', toast:true, position:'top-end', timer:3000, showConfirmButton:false}); return; }

            if (number.startsWith('0')) number = '62' + number.slice(1);
            else if (number.startsWith('8')) number = '62' + number;
            else if (!number.startsWith('62')) {
                Swal.fire({icon:'error', title: 'Format nomor tidak valid', text: 'Nomor harus diawali 62/08', toast:true, position:'top-end', timer:3000, showConfirmButton:false});
                return;
            }

            Swal.fire({ title: 'Mengirim via WA...', html: 'Mohon tunggu', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

            const res = await sendViaWA(number, msg);
            Swal.close();

            if (res && res.status === true) {
                Swal.fire({ icon: 'success', title: 'Pesan terkirim via WA', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                markSentButton('wa', id);
                persistStatusKirim(id, 'wa_terkirim');
            } else {
                const errText = res && (res.error || res.message) ? (res.error || res.message) : JSON.stringify(res);
                Swal.fire({ icon: 'error', title: 'Gagal kirim WA', text: errText, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            }
        }

        async function handleSendEmail(id) {
            const body = buildEmailBodyForRow(id);
            const lokasiEvent = getLokasiEvent();
            const subject = 'Granite Fiesta 2.0 - ' + lokasiEvent;
            
            const emailEl = document.getElementById('email-' + id);
            const to = emailEl ? (emailEl.value || '') : '';
            if (!to) { Swal.fire({icon:'error', title: 'Email tidak tersedia', toast:true, position:'top-end', timer:3000, showConfirmButton:false}); return; }

            // Loading
            Swal.fire({ title: 'Mengirim email...', html: 'Mohon tunggu', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

            try {
                const res = await fetch('/send_email.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        to: to, 
                        subject: subject, 
                        body: body 
                    })
                });
                const data = await res.json();
                Swal.close();
                if (data && data.status === true) {
                    Swal.fire({ icon: 'success', title: 'Email terkirim', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                    markSentButton('email', id);
                    persistStatusKirim(id, 'email_terkirim');
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal kirim email', text: (data.error || JSON.stringify(data)), toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                }
            } catch (e) {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Gagal kirim email', text: e.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            }
        }

        function markSentButton(type, id, status = true) {
            const btn = document.getElementById('btn-' + type + '-' + id);
            if (!btn) return;

            if (status) {
                btn.classList.remove('bg-gray-400', 'hover:bg-gray-500');
                btn.classList.add('bg-green-600', 'hover:bg-green-700');
            } else {
                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                btn.classList.add('bg-gray-400', 'hover:bg-gray-500');
            }
        }

        function persistStatusKirim(id, field) {
            fetch("{{ route('kehadiran.update') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken },
                body: JSON.stringify({ id: id, [field]: 1 })
            }).catch(err => console.error('Gagal menyimpan status kirim:', err));
        }
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