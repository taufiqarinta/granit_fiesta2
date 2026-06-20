<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Kehadiran Event') }}
            </h2>
        </div>
    </x-slot>

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
                                    @foreach($lokasiEvents as $lokasi)
                                        <option value="{{ $lokasi->nama_lokasi }}" {{ $lokasiEvent == $lokasi->nama_lokasi ? 'selected' : '' }}>
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
                                <strong class="text-sm">Total</strong> 
                                <span id="totalPeserta" class="badge bg-gray-600 text-white px-3 py-1 rounded-full text-xs font-bold">0</span>
                            </div>
                            <div class="stat-item flex items-center gap-2">
                                <strong class="text-sm">Hadir</strong> 
                                <span id="totalHadir" class="badge bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold">0</span>
                            </div>
                            <div class="stat-item flex items-center gap-2">
                                <strong class="text-sm">Tidak</strong> 
                                <span id="totalTidakHadir" class="badge bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold">0</span>
                            </div>
                            <div class="stat-item flex items-center gap-2">
                                <strong class="text-sm">Total Kehadiran</strong> 
                                <span id="totalJumlahKehadiran" class="badge bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold">0</span>
                            </div>
                        </div>

                        <!-- Search Bar -->
                        <div class="flex gap-3 items-center">
                            <input type="text" id="searchInput" placeholder="Cari nama toko/agen, PIC, alamat, kota..." 
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
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Kode Toko</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 200px;">Nama Toko</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 150px;">PIC</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Nomor PIC</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 250px;">Alamat</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Kota</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Kode Agen</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 150px;">Nama Agen</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 150px;">Nama Sales</th>
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
                                                   class="focus:border-indigo-500 focus:ring-indigo-500">
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
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item['nama_toko'] }}">
                                                {{ $item['nama_toko'] }}
                                            </div>
                                        </td>
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            <div style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item['pic'] }}">
                                                {{ $item['pic'] }}
                                            </div>
                                        </td>
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827; white-space: nowrap;">
                                            {{ $item['nomor_pic'] }}
                                        </td>
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            <div style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item['alamat'] }}">
                                                {{ $item['alamat'] }}
                                            </div>
                                        </td>
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            <div style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $wilayahData[$item['id']]['kota'] }}">
                                                {{ $wilayahData[$item['id']]['kota'] }}
                                            </div>
                                        </td>

                                        <!-- Kolom Kode Agen - Tampilkan semua agen -->
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

                                        <!-- Kolom Nama Agen - Tampilkan semua agen -->
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

                                        <!-- Kolom Nama Sales - Tampilkan semua sales -->
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

    <script src="https://web.kobin.co.id/kehadiran/socket.io.min.js"></script>
    <script>
        const socket = io("https://web.kobin.co.id:3001");
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Fungsi Export Excel
        function exportToExcel() {
            const lokasiEvent = document.getElementById('lokasi_event').value;
            window.location.href = `{{ route('kehadiran.export') }}?lokasi_event=${encodeURIComponent(lokasiEvent)}`;
        }

        // Socket listener untuk update real-time
        socket.on("updateHadir", (data) => {
            const row = document.getElementById("row-" + data.id);
            if (!row) return;

            if (data.hadir !== undefined) {
                const checkbox = row.querySelector("input[type=checkbox]");
                if (checkbox) {
                    checkbox.checked = data.hadir == 1;
                }
                
                if (data.hadir == 1) {
                    row.style.backgroundColor = '#f0fdf4';
                } else {
                    row.style.backgroundColor = '';
                }
            }
            
            if (data.jumlah_kehadiran !== undefined) {
                const inputJumlah = document.getElementById("jumlah-kehadiran-" + data.id);
                if (inputJumlah) {
                    inputJumlah.value = data.jumlah_kehadiran;
                }
            }
            
            hitungStatistik();
        });

        // Fungsi untuk mengubah status kehadiran - HANYA UPDATE 1 DATA
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
                    // Hapus duplicate_ids, tidak perlu kirim semua
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

        // Fungsi untuk mengubah jumlah kehadiran - HANYA UPDATE 1 DATA
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
                    // Hapus duplicate_ids, tidak perlu kirim semua
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

        // Fungsi menghitung statistik
        function hitungStatistik() {
            const rows = document.querySelectorAll("#tabelDaftarToko tbody tr");
            let total = 0, hadir = 0, tidakHadir = 0, totalJumlahKehadiran = 0, totalPoint = 0;

            rows.forEach(row => {
                if (row.cells.length > 1 && row.style.display !== 'none') {
                    total++;
                    const checkbox = row.querySelector("input[type=checkbox]");
                    const inputJumlah = row.querySelector("input[type=number]");
                    const point = parseInt(row.getAttribute('data-total-point')) || 0;
                    
                    if (checkbox && checkbox.checked) {
                        hadir++;
                    } else {
                        tidakHadir++;
                    }
                    
                    if (inputJumlah) {
                        const nilai = inputJumlah.value === '' ? 0 : parseInt(inputJumlah.value);
                        totalJumlahKehadiran += nilai || 0;
                    }
                    
                    totalPoint += point;
                }
            });

            document.getElementById("totalPeserta").innerText = total;
            document.getElementById("totalHadir").innerText = hadir;
            document.getElementById("totalTidakHadir").innerText = tidakHadir;
            document.getElementById("totalJumlahKehadiran").innerText = totalJumlahKehadiran;
            document.getElementById("totalPoint").innerText = totalPoint.toLocaleString('id-ID');
        }

        // Search functionality
        const input = document.getElementById('searchInput');
        const clearBtn = document.getElementById('clearBtn');

        input.addEventListener('keyup', function() {
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll("#tabelDaftarToko tbody tr");

            rows.forEach(row => {
                if (row.cells.length > 1) {
                    let text = row.textContent.toLowerCase();
                    row.style.display = text.indexOf(filter) > -1 ? "" : "none";
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
    </style>
</x-app-layout>