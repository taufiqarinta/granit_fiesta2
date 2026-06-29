<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Master Doorprize') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('masterdoorprize.trash') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Data Nonaktif
                </a>
                <a href="{{ route('masterdoorprize.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Tambah Doorprize
                </a>
            </div>
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold">Daftar Master Doorprize Aktif</h3>
                        <p class="text-gray-600 text-sm">Total: {{ $masterDoorprizes->total() }} data</p>
                    </div>

                    <!-- Container untuk tabel dengan responsive behavior -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="w-full overflow-x-auto">
                            <table class="w-full min-w-[800px] md:min-w-0">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap w-16">No</th>
                                        <th class="py-3 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama Doorprize</th>
                                        <th class="py-3 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap w-24">Jumlah Doorprize</th>
                                        <th class="py-3 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap w-24"">Gambar</th>
                                        <th class="py-3 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap w-32">Status</th>
                                        <th class="py-3 px-4 border-b text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap w-48">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($masterDoorprizes as $index => $doorprize)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="py-3 px-4 border-b text-sm text-gray-900 text-center whitespace-nowrap">
                                                {{ $masterDoorprizes->firstItem() + $index }}
                                            </td>
                                            <td class="py-3 px-4 border-b text-sm text-gray-900 text-center whitespace-nowrap">
                                                {{ $doorprize->nama_doorprize }}
                                            <td class="py-3 px-4 border-b text-sm text-gray-900 text-center whitespace-nowrap">
                                                {{ $doorprize->jumlah_doorprize }}
                                            </td>
                                            <td class="py-3 px-4 border-b text-sm text-gray-900 text-center whitespace-nowrap">
                                                @if($doorprize->nama_file)
                                                    <img src="{{ asset('images/doorprizes/' . $doorprize->nama_file) }}" 
                                                        alt="{{ $doorprize->nama_doorprize }}" 
                                                        class="w-10 h-10 object-cover rounded">
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 border-b text-sm text-center whitespace-nowrap">
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                    Aktif
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 border-b text-sm whitespace-nowrap">
                                                <div class="flex justify-center space-x-3">
                                                    <a href="{{ route('masterdoorprize.show', $doorprize->id) }}" 
                                                       class="text-green-600 hover:text-green-900 transition duration-150 ease-in-out p-1 rounded hover:bg-green-50"
                                                       title="Lihat Detail">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('masterdoorprize.edit', $doorprize->id) }}" 
                                                       class="text-blue-600 hover:text-blue-900 transition duration-150 ease-in-out p-1 rounded hover:bg-blue-50"
                                                       title="Edit">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('masterdoorprize.destroy', $doorprize->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out p-1 rounded hover:bg-red-50"
                                                                onclick="return confirm('Apakah Anda yakin ingin menonaktifkan doorprize ini?')"
                                                                title="Nonaktifkan">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-6 px-4 border-b text-center text-gray-500">
                                                <div class="flex flex-col items-center justify-center">
                                                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="text-base font-medium text-gray-600">Tidak ada data doorprize aktif.</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    @if($masterDoorprizes->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $masterDoorprizes->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom scrollbar styling untuk tabel */
        .overflow-x-auto {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Untuk layar kecil, berikan scroll horizontal */
        @media (max-width: 768px) {
            .overflow-x-auto {
                border-left: 1px solid #e5e7eb;
                border-right: 1px solid #e5e7eb;
            }
        }
        
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
            border: 2px solid #f1f5f9;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</x-app-layout>