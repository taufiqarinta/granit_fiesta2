<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Master Data Toko') }}
            </h2>
            <!-- <a href="{{ route('daftartoko.create') }}" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Tambah Toko
            </a> -->
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

    <script src="https://cdn.tailwindcss.com"></script>

    <div class="py-12">
        <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="p-4 sm:p-6 text-gray-900">
                    
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Header Section -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <form action="{{ route('daftartoko.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                                <input type="text" 
                                    name="search" 
                                    id="search"
                                    value="{{ $search ?? '' }}"
                                    placeholder="Cari berdasarkan nama toko, kode toko, PIC, kota, atau lokasi event..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div class="flex-1">
                                <label for="lokasi_filter" class="block text-sm font-medium text-gray-700 mb-1">
                                    Filter Berdasarkan Lokasi Event:
                                </label>
                                <select id="lokasi_filter" name="lokasi_event" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <!-- Opsi Semua Lokasi -->
                                    <option value="semua">Semua Lokasi</option>
                                    
                                    @foreach($lokasiEvents as $lokasi)
                                        <option value="{{ $lokasi->nama_lokasi }}" 
                                            {{ (($lokasiEvent ?? '') == $lokasi->nama_lokasi || 
                                                (!request('lokasi_event') && $defaultLokasi && $lokasi->nama_lokasi == $defaultLokasi->nama_lokasi)) ? 'selected' : '' }}>
                                            {{ $lokasi->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="flex items-end space-x-2">
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Filter
                                </button>
                                @if($search || (($lokasiEvent ?? '') != '' && ($lokasiEvent ?? '') != 'semua'))
                                <a href="{{ route('daftartoko.index') }}" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Reset
                                </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Header dengan tombol Tambah Toko -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div class="w-full sm:w-auto">
                            <h2 class="text-lg font-semibold text-gray-800"></h2>
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <!-- <a href="{{ route('daftartoko.export', [
                                'search' => request('search'),
                                'lokasi_event' => request('lokasi_event')
                            ]) }}" 
                                class="px-3 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 whitespace-nowrap">
                                Export Data Toko
                            </a> -->
                            <!-- <a href="{{ route('daftartoko.exportTrackingExcel', [
                                'search' => request('search'),
                                'lokasi_event' => request('lokasi_event')
                            ]) }}" 
                                class="px-3 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 whitespace-nowrap">
                                Export Excel Tracking Toko
                            </a> -->
                            <a href="{{ route('daftartoko.generate-qr') }}" 
                                class="px-3 py-2 bg-purple-600 text-white rounded-md text-sm hover:bg-purple-700 whitespace-nowrap">
                                Generate QR Code
                            </a>
                            <a href="{{ route('daftartoko.exportRekapanGabungan', [
                                'search' => request('search'),
                                'lokasi_event' => request('lokasi_event')
                            ]) }}" 
                                class="px-3 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 whitespace-nowrap">
                                Export Excel Rekapan Gabungan
                            </a>
                            <a href="{{ route('daftartoko.create') }}" 
                                class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 whitespace-nowrap">
                                + Tambah Toko
                            </a>
                        </div>
                        </div>
                    </div>

                    <!-- Responsive Table Container -->
                    <div class="table-container">
                        <div class="overflow-x-scroll">
                            <table class="responsive-table">
                                <thead>
                                    <tr>
                                        <th class="column-no">No</th>
                                        <th class="column-kode">Kode Toko</th>
                                        <th class="column-nama">Nama Toko</th>
                                        <th class="column-agen">Agen</th>
                                        <th class="column-alamat">Alamat</th>
                                        <!-- <th class="column-provinsi">Provinsi</th> -->
                                        <th class="column-kota">Kota</th>
                                        <th class="column-pic">PIC</th>
                                        <th class="column-nomor-pic">Nomor PIC</th>
                                        <th class="column-sales">Sales</th>
                                        <th class="column-lokasi">Lokasi Event</th>
                                        <th class="column-status">Status</th>
                                        <th class="column-aksi">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tokos as $index => $toko)
                                        <tr>
                                            <td class="column-no">{{ $tokos->firstItem() + $index }}</td>
                                            <td class="column-kode">{{ $toko->kode_toko }}</td>
                                            <td class="column-nama">{{ $toko->nama_toko }}</td>
                                            <td class="column-agen">
                                                <div class="text-xs">
                                                    <div><strong>{{ $toko->kode_agen }}</strong></div>
                                                    <div class="text-gray-600">{{ $toko->nama_agen }}</div>
                                                </div>
                                            </td>
                                            <td class="column-alamat">
                                                <div class="truncate" title="{{ $toko->alamat }}">
                                                    {{ Str::limit($toko->alamat, 50) }}
                                                </div>
                                            </td>
                                            <!-- <td class="column-provinsi">{{ $toko->provinsi_name ?? $toko->provinsi }}</td> -->
                                            <td class="column-kota">{{ $toko->kota }}</td>
                                            <td class="column-pic">{{ $toko->pic }}</td>
                                            <td class="column-nomor-pic">{{ $toko->nomor_pic }}</td>
                                            <td class="column-sales">{{ $toko->nama_sales }}</td>
                                            <td class="column-lokasi">
                                                <span class="badge badge-blue">{{ $toko->lokasi_event }}</span>
                                            </td>
                                            <td class="column-status">
                                                <span class="badge {{ $toko->status ? 'badge-green' : 'badge-red' }}">
                                                    {{ $toko->status ? 'Aktif' : 'Non-Aktif' }}
                                                </span>
                                            </td>
                                            <td class="column-aksi">
                                                <div class="action-buttons">
                                                    <a href="{{ route('daftartoko.show', $toko) }}" class="btn btn-blue">
                                                        Lihat
                                                    </a>
                                                    <a href="{{ route('daftartoko.edit', $toko) }}" class="btn btn-yellow">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('daftartoko.destroy', $toko) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-red">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="13" class="text-center">Tidak ada data toko</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $tokos->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom CSS untuk tabel responsif */
        .table-container {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background: white;
            overflow: hidden;
        }

        .overflow-x-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        .overflow-x-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .btn {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn-blue { background-color: #3490dc; }
        .btn-yellow { background-color: #ffcc00; color: #333; }
        .btn-red { background-color: #e3342f; }


        .overflow-x-scroll::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 4px;
        }

        .overflow-x-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        .overflow-x-scroll::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .responsive-table {
            width: 100%;
            min-width: 1200px; /* Minimum width untuk memaksa scroll */
            border-collapse: collapse;
        }

        .responsive-table th,
        .responsive-table td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .responsive-table th {
            background-color: #f9fafb;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .responsive-table td {
            font-size: 0.875rem;
            color: #374151;
        }

        .responsive-table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Lebar kolom spesifik */
        .column-no { width: 60px; min-width: 60px; }
        .column-kode { width: 100px; min-width: 100px; }
        .column-nama { width: 150px; min-width: 150px; }
        .column-agen { width: 120px; min-width: 120px; }
        .column-alamat { width: 200px; min-width: 200px; }
        .column-provinsi { width: 120px; min-width: 120px; }
        .column-kota { width: 120px; min-width: 120px; }
        .column-pic { width: 100px; min-width: 100px; }
        .column-nomor-pic { width: 120px; min-width: 120px; }
        .column-sales { width: 120px; min-width: 120px; }
        .column-lokasi { width: 120px; min-width: 120px; }
        .column-status { width: 100px; min-width: 100px; }
        .column-aksi { width: 180px; }

        /* Styling untuk badges */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 9999px;
        }

        .badge-blue {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-green {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-red {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Styling untuk tombol aksi */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .btn {
            display: block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            text-align: center;
            border-radius: 0.25rem;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-blue {
            background-color: #2563eb;
            color: white;
        }

        .btn-blue:hover {
            background-color: #1d4ed8;
        }

        .btn-yellow {
            background-color: #f59e0b;
            color: white;
        }

        .btn-yellow:hover {
            background-color: #d97706;
        }

        .btn-red {
            background-color: #dc2626;
            color: white;
        }

        .btn-red:hover {
            background-color: #b91c1c;
        }

        /* Responsive design untuk mobile */
        @media (max-width: 768px) {
            .table-container {
                margin: 0 -1rem;
                border-radius: 0;
                border-left: none;
                border-right: none;
            }
            
            .responsive-table {
                min-width: 1400px; /* Lebih lebar untuk mobile */
            }
        }
    </style>
</x-app-layout>