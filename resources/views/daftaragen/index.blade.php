<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Master Data Agen') }}
            </h2>
            <!-- <a href="{{ route('daftaragen.create') }}" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Tambah Agen
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
                <div class="p-4 sm:p-6 text-gray-900 p-2">
                    
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Header Section -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <form action="{{ route('daftaragen.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                                <input type="text" 
                                    name="search" 
                                    id="search"
                                    value="{{ $search ?? '' }}"
                                    placeholder="Cari berdasarkan nama agen, kode agen, PIC, atau kota..."
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
                                <a href="{{ route('daftaragen.index') }}" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Reset
                                </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div class="w-full sm:w-auto">
                            <h2 class="text-lg font-semibold text-gray-800">Daftar Agen</h2>
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <!-- <a href="{{ route('daftaragen.exportAgenExcel', [
                                'search' => request('search'),
                                'lokasi_event' => request('lokasi_event')
                            ]) }}" 
                                class="px-3 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 whitespace-nowrap">
                                Export Excel Tracking Agen
                            </a> -->
                            <a href="{{ route('daftaragen.import.form') }}" 
                                class="px-3 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 whitespace-nowrap">
                                Import Excel
                            </a>
                                <a href="{{ route('daftaragen.generate-qr') }}" 
                                    class="px-3 py-2 bg-purple-600 text-white rounded-md text-sm hover:bg-purple-700 whitespace-nowrap">
                                    Generate QR Code
                                </a>
                            <a href="{{ route('daftaragen.create') }}" 
                                 class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 whitespace-nowrap">
                                + Tambah Agen
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
                                        <th class="column-kode">Kode Agen</th>
                                        <th class="column-nama">Nama Agen</th>
                                        <th class="column-alamat">Alamat</th>
                                        <!-- <th class="column-provinsi">Provinsi</th> -->
                                        <th class="column-kota">Kota</th>
                                        <th class="column-pic">PIC</th>
                                        <th class="column-nomor-pic">Nomor PIC</th>
                                        <th class="column-lokasi">Lokasi Event</th>
                                        <th class="column-status">Status</th>
                                        <th class="column-aksi">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($agens as $index => $agen)
                                        <tr>
                                            <td class="column-no">{{ $agens->firstItem() + $index }}</td>
                                            <td class="column-kode">{{ $agen->kode_agen }}</td>
                                            <td class="column-nama">{{ $agen->nama_agen }}</td>
                                            <td class="column-alamat">
                                                <div class="truncate" title="{{ $agen->alamat }}">
                                                    {{ Str::limit($agen->alamat, 50) }}
                                                </div>
                                            </td>
                                            <!-- <td class="column-provinsi">{{ $agen->provinsi_name ?? $agen->provinsi }}</td> -->
                                            <!-- <td class="column-kota">{{ $agen->kota_name ?? $agen->kota }}</td> -->
                                            <td class="column-kota">{{ $agen->kota }}</td>
                                            <td class="column-pic">{{ $agen->pic }}</td>
                                            <td class="column-nomor-pic">{{ $agen->nomor_pic }}</td>
                                            <td class="column-lokasi">
                                                <span class="badge badge-blue">{{ $agen->lokasi_event }}</span>
                                            </td>
                                            <td class="column-status">
                                                <span class="badge {{ $agen->status ? 'badge-green' : 'badge-red' }}">
                                                    {{ $agen->status ? 'Aktif' : 'Non-Aktif' }}
                                                </span>
                                            </td>
                                            <td class="column-aksi">
                                                <div class="action-buttons">
                                                    <a href="{{ route('daftaragen.show', $agen) }}" class="btn btn-blue">
                                                        Lihat
                                                    </a>
                                                    <a href="{{ route('daftaragen.edit', $agen) }}" class="btn btn-yellow">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('daftaragen.destroy', $agen) }}" method="POST" onsubmit="return confirm('Yakin ingin menonaktifkan data ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-red">
                                                            Nonaktifkan
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">Tidak ada data agen</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $agens->links() }}
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
            border-collapse: collapse;
            min-width: 1200px; /* Minimum width untuk memastikan konten tidak terpotong */
        }

        .responsive-table th,
        .responsive-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .responsive-table thead {
            background-color: #f9fafb;
        }

        .responsive-table th {
            font-weight: 600;
            font-size: 0.75rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .responsive-table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Column Widths */
        .column-no {
            width: 60px;
        }

        .column-kode {
            width: 100px;
        }

        .column-nama {
            width: 200px;
            min-width: 150px;
        }

        .column-alamat {
            width: 250px;
            min-width: 200px;
            white-space: normal !important;
        }

        .column-provinsi {
            width: 120px;
        }

        .column-kota {
            width: 120px;
        }

        .column-pic {
            width: 120px;
        }

        .column-nomor-pic {
            width: 120px;
        }

        .column-lokasi {
            width: 120px;
        }

        .column-status {
            width: 100px;
        }

        .column-aksi {
            width: 180px;
        }

        /* Badge Styles */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
            text-align: center;
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

        /* Button Styles */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .btn {
            display: block;
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-blue {
            background-color: #3b82f6;
            color: white;
        }

        .btn-blue:hover {
            background-color: #2563eb;
        }

        .btn-yellow {
            background-color: #f59e0b;
            color: white;
        }

        .btn-yellow:hover {
            background-color: #d97706;
        }

        .btn-red {
            background-color: #ef4444;
            color: white;
        }

        .btn-red:hover {
            background-color: #dc2626;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .table-container {
                border-radius: 0.375rem;
                margin: 0 -1rem;
            }
            
            .overflow-x-scroll {
                border-radius: 0;
            }
            
            .responsive-table {
                min-width: 1000px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                font-size: 0.7rem;
                padding: 4px 8px;
            }
        }

        /* Pagination Styling */
        .mt-6 .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1rem;
        }

        .mt-6 .pagination .page-link {
            padding: 0.5rem 0.75rem;
            margin: 0 0.25rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            color: #374151;
            text-decoration: none;
            transition: all 0.2s;
        }

        .mt-6 .pagination .page-link:hover {
            background-color: #f3f4f6;
            border-color: #9ca3af;
        }

        .mt-6 .pagination .page-item.active .page-link {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }

        .mt-6 .pagination .page-item.disabled .page-link {
            color: #9ca3af;
            cursor: not-allowed;
        }
    </style>
</x-app-layout>