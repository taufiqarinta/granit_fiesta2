<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Daftar Form Order') }}
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
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                            @if (session('kode_unik'))
                                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded">
                                    <strong>Detail Voucher:</strong><br>
                                    • Kode Unik: <span class="font-mono font-bold">{{ session('kode_unik') }}</span><br>
                                    • Jumlah Voucher: {{ session('jumlah_voucher') }} voucher<br>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Data Form Order</h3>
                        <div class="flex space-x-2">
                            <!-- Export Button (Lama - dengan penggabungan) -->
                            <form action="{{ route('form-order.export') }}" method="GET" class="inline">
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if(request('lokasi_event'))
                                    <input type="hidden" name="lokasi_event" value="{{ request('lokasi_event') }}">
                                @endif
                                <button type="submit" 
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export Excel (Grouped)
                                </button>
                            </form>
                            
                            <!-- Export Button Baru (Detail - tanpa penggabungan) -->
                            <form action="{{ route('form-order.export-detail') }}" method="GET" class="inline">
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if(request('lokasi_event'))
                                    <input type="hidden" name="lokasi_event" value="{{ request('lokasi_event') }}">
                                @endif
                                <button type="submit" 
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export Excel (Detail)
                                </button>
                            </form>
                            
                            <a href="{{ route('form-order.create') }}" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                + Tambah Form Order
                            </a>
                        </div>
                    </div>

                    <!-- Search and Filter Form -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <form action="{{ route('form-order.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                                <input type="text" 
                                       name="search" 
                                       id="search"
                                       value="{{ request('search') }}"
                                       placeholder="Cari berdasarkan nama agen, sales, toko, PIC, kota, atau brand..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- <div class="flex-1">
                                <label for="lokasi_event" class="block text-sm font-medium text-gray-700 mb-1">Filter Lokasi Event</label>
                                <select name="lokasi_event" 
                                        id="lokasi_event"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Semua Lokasi</option>
                                    @foreach($lokasiEvents as $lokasi)
                                        <option value="{{ $lokasi }}" {{ request('lokasi_event') == $lokasi ? 'selected' : '' }}>
                                            {{ $lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> -->

                            <div class="flex-1">
                                <label for="lokasi_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                    Filter Berdasarkan Lokasi Event:
                                </label>
                                <select id="lokasi_filter" name="lokasi_event" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <!-- Opsi Semua Lokasi -->
                                    <option value="semua">Semua Lokasi</option>
                                    
                                    @foreach($lokasiEvents as $lokasi)
                                        <option value="{{ $lokasi->nama_lokasi }}" 
                                            {{ (request('lokasi_event') == $lokasi->nama_lokasi || 
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
                                <a href="{{ route('form-order.index') }}" 
                                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Results Info -->
                    @if(request('search') || request('lokasi_event'))
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                        <p class="text-sm text-blue-700">
                            Menampilkan hasil 
                            @if(request('search')) untuk pencarian "<strong>{{ request('search') }}</strong>" @endif
                            @if(request('search') && request('lokasi_event')) dan @endif
                            @if(request('lokasi_event')) filter lokasi "<strong>{{ request('lokasi_event') }}</strong>" @endif
                            - Total: <strong>{{ $formOrders->total() }}</strong> data
                        </p>
                    </div>
                    @endif

                    <!-- Wrapper dengan overflow yang lebih agresif -->
                    <div class="relative">
                        <div class="overflow-x-auto" style="max-width: 100vw; margin-left: -1rem; margin-right: -1rem;">
                            <div class="inline-block min-w-full px-4">
                                <table class="min-w-full border border-gray-300" style="min-width: 1300px;"> <!-- Tambah width karena ada kolom baru -->
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">No</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Tanggal Pembuatan</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama Agen</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama Sales</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama Toko</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama PIC</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nomor PIC</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Kota Toko</th>
                                            <!-- KOLOM BARU: VOUCHER -->
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Kode Unik</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Jumlah Voucher</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Total Point</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Lokasi Event</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($formOrders as $index => $order)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap text-center">{{ $formOrders->firstItem() + $index }}</td>
                                                <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($order->tanggal_order)->format('Y-m-d') }}
                                                </td>
                                                <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap">{{ $order->nama_agen }}</td>
                                                <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap">{{ $order->nama_sales }}</td>
                                                <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap">{{ $order->nama_toko }}</td>
                                                <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap">{{ $order->pic }}</td>
                                                <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap">{{ $order->no_hp }}</td>
                                                <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap">{{ $order->kota }}</td>
                                                
                                                <!-- KOLOM BARU: VOUCHER -->
                                                <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap">
                                                    @if($order->kode_unik_voucher)
                                                        <span class="font-mono text-blue-600 font-semibold">{{ $order->kode_unik_voucher }}</span>
                                                    @else
                                                        <span class="text-gray-400 text-xs">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap text-center">
                                                    @if($order->jumlah_voucher > 0)
                                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">
                                                            {{ $order->jumlah_voucher }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400 text-xs">-</span>
                                                    @endif
                                                </td>
                                                
                                                <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap text-right">{{ number_format($order->total_point, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap">{{ $order->lokasi_event }}</td>
                                                <td class="px-4 py-3 border text-sm font-medium whitespace-nowrap">
                                                    <div class="flex space-x-2 justify-start">
                                                        <a href="{{ route('form-order.show', $order->id) }}" 
                                                        class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded text-xs font-medium transition-colors">
                                                            Lihat
                                                        </a>
                                                        @if ($order->ttd_pic || $order->ttd_nama_terang)
                                                        <a href="{{ route('form-order.pdf', $order->id) }}" 
                                                           target="_blank"
                                                           class="text-red-700 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded text-xs font-medium transition-colors">
                                                            PDF
                                                        </a>
                                                        @endif
                                                        <a href="{{ route('form-order.edit', $order->id) }}" 
                                                        class="text-center px-2 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600"
                                                            style="background-color: #f1c40f; color: white; padding: 5px 10px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer;">
                                                            Edit
                                                        </a>
                                                        <form action="{{ route('form-order.destroy', $order->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus form order ini?')"
                                                                    class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded text-xs font-medium transition-colors">
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="13" class="px-4 py-4 border text-center text-sm text-gray-500"> <!-- Ubah colspan jadi 13 -->
                                                    @if(request('search') || request('lokasi_event'))
                                                        Tidak ada data yang sesuai dengan kriteria pencarian.
                                                    @else
                                                        Belum ada data form order
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 px-4">
                        {{ $formOrders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .overflow-x-auto {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        
        .overflow-x-auto::-webkit-scrollbar {
            height: 12px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 6px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 6px;
            border: 2px solid #f1f1f1;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</x-app-layout>