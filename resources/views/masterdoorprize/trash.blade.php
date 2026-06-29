<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Data Master Doorprize Nonaktif') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('masterdoorprize.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Data Aktif
                </a>
            </div>
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
                        <h3 class="text-lg font-semibold">Daftar Master Doorprize Nonaktif</h3>
                        <p class="text-gray-600 text-sm">Total: {{ $masterDoorprizes->total() }} data</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="py-3 px-4 border-b text-center w-16">No</th>
                                    <th class="py-3 px-4 border-b">Nama Doorprize</th>
                                    <th class="py-3 px-4 border-b text-center w-24">Jumlah Doorprize</th>
                                    <th class="py-3 px-4 border-b text-center w-32">Status</th>
                                    <th class="py-3 px-4 border-b text-center w-48">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($masterDoorprizes as $index => $doorprize)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 border-b text-center">
                                            {{ $masterDoorprizes->firstItem() + $index }}
                                        </td>
                                        <td class="py-3 px-4 border-b">{{ $doorprize->nama_doorprize }}</td>
                                        <td class="py-3 px-4 border-b text-center">
                                            <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-sm font-medium">
                                                {{ $doorprize->jumlah_doorprize }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border-b text-center">
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                Nonaktif
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border-b text-center">
                                            <div class="flex justify-center space-x-2">
                                                <form action="{{ route('masterdoorprize.restore', $doorprize->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" 
                                                            class="text-green-600 hover:text-green-900 transition duration-150 ease-in-out"
                                                            onclick="return confirm('Apakah Anda yakin ingin mengaktifkan kembali doorprize ini?')"
                                                            title="Aktifkan Kembali">
                                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-4 px-4 border-b text-center text-gray-500">
                                            Tidak ada data doorprize nonaktif.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($masterDoorprizes->hasPages())
                    <div class="mt-6">
                        {{ $masterDoorprizes->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>