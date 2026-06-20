<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <!-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-8">
                <!-- Total Toko -->
                <div class="bg-blue-500 rounded-lg shadow overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-xs font-semibold uppercase tracking-wide mb-2"> 🏪 Total Toko</p>
                                <h2 class="text-3xl font-bold mb-3">{{ number_format($totalToko) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Agen -->
                <div class="bg-blue-500 rounded-lg shadow overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-xs font-semibold uppercase tracking-wide mb-2"> 👤 Total Agen</p>
                                <h2 class="text-3xl font-bold mb-3">{{ number_format($totalAgen) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Agen + Toko -->
                <div class="bg-blue-500 rounded-lg shadow overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-xs font-semibold uppercase tracking-wide mb-2"> 👥 Agen + Toko</p>
                                <h2 class="text-3xl font-bold mb-3">{{ number_format($totalAgenToko) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Kehadiran -->
                <div class="bg-blue-500 rounded-lg shadow overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-xs font-semibold uppercase tracking-wide mb-2"> ✓ Jumlah Kehadiran</p>
                                <h2 class="text-3xl font-bold mb-3">{{ number_format($totalKehadiran) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Order -->
                <div class="bg-blue-500 rounded-lg shadow overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-xs font-semibold uppercase tracking-wide mb-2"> 📋 Jumlah Order</p>
                                <h2 class="text-3xl font-bold mb-3">{{ number_format($totalOrder) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Belum Order -->
                <div class="bg-blue-500 rounded-lg shadow overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-xs font-semibold uppercase tracking-wide mb-2">⏳ Jumlah Belum Order</p>
                                <h2 class="text-3xl font-bold mb-3">{{ number_format($totalBelumOrder) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            <br>

            <!-- Error Message -->
            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Detailed Statistics -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Jumlah Toko per Lokasi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jumlah Toko per Lokasi Event</h3>
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
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item->lokasi_event }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item->total) }}</td>
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

                <!-- Jumlah Agen per Lokasi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jumlah Agen per Lokasi Event</h3>
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
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item->lokasi_event }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item->total) }}</td>
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

                <!-- Jumlah Agen + Toko per Lokasi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jumlah Agen + Toko per Lokasi Event</h3>
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
                                        <tr class="hover:bg-gray-50">
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

                <!-- Jumlah Kehadiran per Lokasi -->
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
                                        <tr class="hover:bg-gray-50">
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

                <!-- Jumlah Order per Lokasi -->
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
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $item->lokasi_event }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item->total) }}</td>
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

                <!-- Jumlah Belum Order per Lokasi -->
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
                                        <tr class="hover:bg-gray-50">
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