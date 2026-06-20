<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Master Lokasi Event') }}
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
        
        .max-w-6xl {
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
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Data Lokasi Event</h3>
                        <a href="{{ route('master-lokasi-event.create') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah Lokasi Event
                        </a>
                    </div>

                    <!-- Wrapper dengan overflow yang sama seperti form order -->
                    <div class="relative">
                        <div class="overflow-x-auto" style="max-width: 100vw; margin-left: -1rem; margin-right: -1rem;">
                            <div class="inline-block min-w-full px-4">
                                <table class="min-w-full border border-gray-300" style="min-width: 1050px;">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">No</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama Lokasi</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                                            <th class="px-4 py-3 border text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($lokasiEvents as $index => $lokasiEvent)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap text-center">
                                                {{ ($lokasiEvents->currentPage() - 1) * $lokasiEvents->perPage() + $index + 1 }}
                                            </td>
                                            <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap">
                                                {{ $lokasiEvent->tanggal->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap">
                                                {{ $lokasiEvent->nama_lokasi }}
                                            </td>
                                            <td class="px-4 py-3 border text-sm text-gray-900 whitespace-nowrap">
                                                {{ $lokasiEvent->status }}
                                            </td>
                                            <td class="px-4 py-3 border text-sm font-medium whitespace-nowrap">
                                                <div class="flex space-x-2 justify-start">
                                                    <a href="{{ route('master-lokasi-event.show', $lokasiEvent->id) }}" 
                                                       class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded text-xs font-medium transition-colors">
                                                        Lihat
                                                    </a>
                                                    <a href="{{ route('master-lokasi-event.edit', $lokasiEvent->id) }}" 
                                                       class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 px-3 py-1 rounded text-xs font-medium transition-colors">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('master-lokasi-event.destroy', $lokasiEvent->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                onclick="return confirm('Apakah Anda yakin ingin menonaktifkan lokasi event ini?')"
                                                                class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded text-xs font-medium transition-colors">
                                                            Nonaktifkan
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-4 border text-center text-sm text-gray-500">
                                                Tidak ada data lokasi event.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 px-4">
                        {{ $lokasiEvents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Force horizontal scrolling */
        .overflow-x-auto {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Custom scrollbar styling */
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