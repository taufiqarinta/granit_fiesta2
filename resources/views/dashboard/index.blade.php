<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Dashboard') }}
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Error Message -->
            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Baris Pertama: 2 Card -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Card 1: Jumlah Toko per Lokasi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jumlah Toko Aktif per Lokasi Event</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi Event</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($jumlahToko as $item)
                                        <tr class="hover:bg-gray-50 {{ $item['is_today'] ? 'bg-green-100 hover:bg-green-200' : '' }}">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item['lokasi_event'] }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item['total']) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Total</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-right">{{ number_format($totalToko) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Jumlah Agen per Lokasi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jumlah Agen Aktif per Lokasi Event</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi Event</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($jumlahAgen as $item)
                                        <tr class="hover:bg-gray-50 {{ $item['is_today'] ? 'bg-green-100 hover:bg-green-200' : '' }}">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item['lokasi_event'] }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item['total']) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Total</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-right">{{ number_format($totalAgen) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Baris Kedua: 2 Card -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Card 3: Jumlah Agen + Toko per Lokasi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jumlah Agen + Toko Aktif per Lokasi Event</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi Event</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($jumlahAgenToko as $item)
                                        <tr class="hover:bg-gray-50 {{ $item['is_today'] ? 'bg-green-100 hover:bg-green-200' : '' }}">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item['lokasi_event'] }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item['total']) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Total</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-right">{{ number_format($totalAgenToko) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Jumlah Kehadiran per Lokasi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jumlah Kehadiran per Lokasi Event</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi Event</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($jumlahKehadiran as $item)
                                        <tr class="hover:bg-gray-50 {{ $item['is_today'] ? 'bg-green-100 hover:bg-green-200' : '' }}">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item['lokasi_event'] }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item['total']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Total</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-right">{{ number_format($totalKehadiran) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Baris Ketiga: 2 Card -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card 5: Jumlah Order per Lokasi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jumlah Order per Lokasi Event</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi Event</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($jumlahOrder as $item)
                                        <tr class="hover:bg-gray-50 {{ $item['is_today'] ? 'bg-green-100 hover:bg-green-200' : '' }}">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item['lokasi_event'] }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item['total']) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Total</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-right">{{ number_format($totalOrder) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Card 6: Jumlah Belum Order per Lokasi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jumlah Belum Order per Lokasi Event</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi Event</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($jumlahBelumOrder as $item)
                                        <tr class="hover:bg-gray-50 {{ $item['is_today'] ? 'bg-green-100 hover:bg-green-200' : '' }}">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item['lokasi_event'] }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item['total']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Total</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-right">{{ number_format($totalBelumOrder) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>