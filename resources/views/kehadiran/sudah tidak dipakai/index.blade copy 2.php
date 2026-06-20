<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Kehadiran Event') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

                    <!-- Tabel -->
                    <div class="overflow-x-auto">
                        <table id="tabelDaftarToko" class="min-w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2 text-center">ID</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Kode Toko</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Nama Toko</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">PIC</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Nomor PIC</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Alamat</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Provinsi</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Kota</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Kode Agen</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Nama Agen</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Nama Sales</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Hadir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($daftartokos as $daftartoko)
                                    <tr id="row-{{ $daftartoko->id }}" class="{{ $daftartoko->hadir ? 'bg-green-100' : '' }}">
                                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $daftartoko->id }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $daftartoko->kode_toko }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $daftartoko->nama_toko }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $daftartoko->pic }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $daftartoko->nomor_pic }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $daftartoko->alamat }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $wilayahData[$daftartoko->id]['provinsi'] }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $wilayahData[$daftartoko->id]['kota'] }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $daftartoko->kode_agen }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $daftartoko->nama_agen }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $daftartoko->nama_sales }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-center">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" 
                                                       {{ $daftartoko->hadir ? 'checked' : '' }}
                                                       onchange="ubahHadir({{ $daftartoko->id }}, this.checked)"
                                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-5 w-5">
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://web.kobin.co.id/kehadiran/socket.io.min.js"></script>
    <script>
        const socket = io("https://web.kobin.co.id:3001");
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Fungsi untuk mengubah status kehadiran
        function ubahHadir(id, status) {
            fetch("{{ route('kehadiran.update') }}", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: `id=${id}&hadir=${status ? 1 : 0}`
            });
        }

        // Socket listener untuk update real-time
        socket.on("updateHadir", ({id, hadir}) => {
            const row = document.getElementById("row-" + id);
            const checkbox = row.querySelector("input[type=checkbox]");
            checkbox.checked = hadir == 1;
            
            // Update background color
            if (hadir == 1) {
                row.classList.add('bg-green-100');
                row.classList.remove('bg-white');
            } else {
                row.classList.remove('bg-green-100');
                row.classList.add('bg-white');
            }
            
            hitungStatistik();
        });

        // Fungsi menghitung statistik
        function hitungStatistik() {
            const rows = document.querySelectorAll("#tabelDaftarToko tbody tr");
            let total = 0, hadir = 0, tidakHadir = 0;

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    total++;
                    const checkbox = row.querySelector("input[type=checkbox]");
                    if (checkbox.checked) hadir++; else tidakHadir++;
                }
            });

            document.getElementById("totalPeserta").innerText = total;
            document.getElementById("totalHadir").innerText = hadir;
            document.getElementById("totalTidakHadir").innerText = tidakHadir;
        }

        // Search functionality
        const input = document.getElementById('searchInput');
        const clearBtn = document.getElementById('clearBtn');

        input.addEventListener('keyup', function() {
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll("#tabelDaftarToko tbody tr");

            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(filter) > -1 ? "" : "none";
            });
            
            hitungStatistik();
        });

        clearBtn.addEventListener('click', () => {
            input.value = '';
            const rows = document.querySelectorAll("#tabelDaftarToko tbody tr");
            rows.forEach(row => row.style.display = "");
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
</x-app-layout>