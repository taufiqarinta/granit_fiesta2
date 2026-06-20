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
                                    <option value="Semarang" {{ $lokasiEvent == 'Semarang' ? 'selected' : '' }}>Semarang</option>
                                    <option value="Surabaya" {{ $lokasiEvent == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                                    <option value="Jakarta" {{ $lokasiEvent == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                                </select>
                            </div>

                            <!-- Statistik -->
                            <div class="statistik flex gap-6">
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
                        </div>

                        <!-- Search Bar -->
                        <div class="flex gap-3 items-center">
                            <input type="text" id="searchInput" placeholder="Cari nama toko, PIC, alamat, kota, provinsi..." 
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button id="clearBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Container Tabel dengan Scroll Horizontal -->
                    <div class="table-container" style="width: 100%; overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 8px;">
                        <table id="tabelDaftarToko" style="min-width: 1300px; width: 100%; border-collapse: collapse; background: white;">
                            <thead style="background-color: #f9fafb;">
                                <tr>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; position: sticky; left: 0; background: #f9fafb; z-index: 10; min-width: 60px;">No</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Kode Toko</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 200px;">Nama Toko</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 150px;">PIC</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Nomor PIC</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 250px;">Alamat</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Provinsi</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Kota</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 120px;">Kode Agen</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 150px;">Nama Agen</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 150px;">Nama Sales</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; min-width: 100px;">Jumlah Hadir</th>
                                    <th style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; position: sticky; right: 0; background: #f9fafb; z-index: 10; min-width: 80px;">Hadir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($daftartokos as $index => $daftartoko)
                                    <tr id="row-{{ $daftartoko->id }}" style="{{ $daftartoko->hadir ? 'background-color: #f0fdf4;' : '' }} transition: background-color 0.15s ease-in-out;">
                                        <!-- Nomor Urut - Sticky -->
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; font-size: 0.875rem; color: #111827; font-weight: 500; position: sticky; left: 0; background: inherit; z-index: 5;">
                                            {{ $index + 1 }}
                                        </td>
                                        
                                        <!-- Data Kolom -->
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827; white-space: nowrap;">
                                            {{ $daftartoko->kode_toko }}
                                        </td>
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $daftartoko->nama_toko }}">
                                                {{ $daftartoko->nama_toko }}
                                            </div>
                                        </td>
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            <div style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $daftartoko->pic }}">
                                                {{ $daftartoko->pic }}
                                            </div>
                                        </td>
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827; white-space: nowrap;">
                                            {{ $daftartoko->nomor_pic }}
                                        </td>
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            <div style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $daftartoko->alamat }}">
                                                {{ $daftartoko->alamat }}
                                            </div>
                                        </td>
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            <div style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $wilayahData[$daftartoko->id]['provinsi'] }}">
                                                {{ $wilayahData[$daftartoko->id]['provinsi'] }}
                                            </div>
                                        </td>
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            <div style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $wilayahData[$daftartoko->id]['kota'] }}">
                                                {{ $wilayahData[$daftartoko->id]['kota'] }}
                                            </div>
                                        </td>
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827; white-space: nowrap;">
                                            {{ $daftartoko->kode_agen }}
                                        </td>
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            <div style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $daftartoko->nama_agen }}">
                                                {{ $daftartoko->nama_agen }}
                                            </div>
                                        </td>
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: left; font-size: 0.875rem; color: #111827;">
                                            <div style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $daftartoko->nama_sales }}">
                                                {{ $daftartoko->nama_sales }}
                                            </div>
                                        </td>
                                        
                                        <!-- Kolom Jumlah Kehadiran -->
                                        <td style="border: 1px solid #e5e7eb; padding: 8px; text-align: center; background: inherit;">
                                            <input type="number" 
                                                   id="jumlah-kehadiran-{{ $daftartoko->id }}"
                                                   value="{{ $daftartoko->jumlah_kehadiran }}"
                                                   min="0"
                                                   onchange="ubahJumlahKehadiran({{ $daftartoko->id }}, this.value)"
                                                   style="width: 80px; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; text-align: center; font-size: 0.875rem;"
                                                   class="focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        
                                        <!-- Kolom Hadir - Sticky -->
                                        <td style="border: 1px solid #e5e7eb; padding: 12px; text-align: center; position: sticky; right: 0; background: inherit; z-index: 5;">
                                            <label style="display: inline-flex; align-items: center; cursor: pointer;">
                                                <input type="checkbox" 
                                                       {{ $daftartoko->hadir ? 'checked' : '' }}
                                                       onchange="ubahHadir({{ $daftartoko->id }}, this.checked)"
                                                       style="border-radius: 0.25rem; border: 1px solid #d1d5db; color: #4f46e5; height: 1.25rem; width: 1.25rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                                
                                @if($daftartokos->count() == 0)
                                    <tr>
                                        <td colspan="13" style="border: 1px solid #e5e7eb; padding: 32px; text-align: center; font-size: 0.875rem; color: #6b7280;">
                                            Tidak ada data untuk lokasi event "{{ $lokasiEvent }}"
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Info Tabel Responsif -->
                    <div style="margin-top: 16px; font-size: 0.75rem; color: #6b7280; text-align: center;">
                        <span>📱 Geser tabel ke samping untuk melihat kolom lainnya</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://web.kobin.co.id/kehadiran/socket.io.min.js"></script>
    <script>
        const socket = io("https://web.kobin.co.id:3001");
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Socket listener untuk update real-time
        socket.on("updateHadir", (data) => {
            const row = document.getElementById("row-" + data.id);
            if (!row) return;

            // Update checkbox hadir jika ada data
            if (data.hadir !== undefined) {
                const checkbox = row.querySelector("input[type=checkbox]");
                if (checkbox) {
                    checkbox.checked = data.hadir == 1;
                }
                
                // Update background color berdasarkan status hadir
                if (data.hadir == 1) {
                    row.style.backgroundColor = '#f0fdf4';
                } else {
                    row.style.backgroundColor = '';
                }
            }
            
            // Update jumlah kehadiran jika ada data
            if (data.jumlah_kehadiran !== undefined) {
                const inputJumlah = document.getElementById("jumlah-kehadiran-" + data.id);
                if (inputJumlah) {
                    inputJumlah.value = data.jumlah_kehadiran;
                }
            }
            
            hitungStatistik();
        });

        // Fungsi untuk mengubah status kehadiran
        function ubahHadir(id, status) {
            fetch("{{ route('kehadiran.update') }}", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: `id=${id}&hadir=${status ? 1 : 0}`
            }).then(response => response.json())
            .then(data => {
                // Update UI langsung tanpa menunggu socket
                const row = document.getElementById("row-" + id);
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
            fetch("{{ route('kehadiran.update') }}", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: `id=${id}&jumlah_kehadiran=${jumlah}`
            }).then(response => response.json())
            .then(data => {
                hitungStatistik();
            });
        }

        // Fungsi menghitung statistik
        function hitungStatistik() {
            const rows = document.querySelectorAll("#tabelDaftarToko tbody tr");
            let total = 0, hadir = 0, tidakHadir = 0, totalJumlahKehadiran = 0;

            rows.forEach(row => {
                // Skip row jika colspan (tidak ada data)
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
                        totalJumlahKehadiran += parseInt(inputJumlah.value) || 0;
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
                // Skip row jika colspan (tidak ada data)
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
            window.location.href = `?lokasi_event=${lokasiEvent}`;
        });

        // Initial calculation
        document.addEventListener('DOMContentLoaded', hitungStatistik);
    </script>

    <style>
        /* Custom scrollbar styling */
        .table-container {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }
        
        .table-container::-webkit-scrollbar {
            height: 12px;
        }
        
        .table-container::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 6px;
            margin: 4px;
        }
        
        .table-container::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 6px;
            border: 2px solid #f7fafc;
        }
        
        .table-container::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        /* Sticky column shadow effects */
        table th:first-child,
        table td:first-child {
            box-shadow: 2px 0 4px -1px rgba(0,0,0,0.1);
        }

        table th:last-child,
        table td:last-child {
            box-shadow: -2px 0 4px -1px rgba(0,0,0,0.1);
        }

        /* Ensure table takes full width */
        .table-container table {
            table-layout: auto;
        }

        /* Style untuk input number */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
</x-app-layout>