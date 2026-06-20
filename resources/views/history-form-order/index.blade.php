<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('History Form Order') }}
        </h2>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>

    <div class="py-6 px-4 max-w-8xl mx-auto">

        {{-- Filter --}}
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5 mb-6">
            <form method="GET" action="{{ route('history-form-order.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama toko, agen, lokasi..."
                        class="w-full border border-red-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                </div>
                <div class="min-w-[130px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Aksi</label>
                    <select name="aksi" class="w-full border border-red-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                        <option value="">Semua</option>
                        <option value="create" {{ request('aksi') == 'create' ? 'selected' : '' }}>Create</option>
                        <option value="update" {{ request('aksi') == 'update' ? 'selected' : '' }}>Update</option>
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Dari Tanggal</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                        class="w-full border border-red-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Sampai Tanggal</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                        class="w-full border border-red-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        🔍 Filter
                    </button>
                    <a href="{{ route('history-form-order.index') }}"
                        class="bg-white border border-red-200 text-gray-500 hover:border-red-400 hover:text-red-500 text-sm font-semibold px-4 py-2 rounded-lg transition">
                        ✕ Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-red-50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-sm">
                    Daftar History
                    <span class="ml-2 text-xs font-normal text-gray-400">({{ $histories->total() }} data)</span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-red-50 text-left">
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">No</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Waktu</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Aksi</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Nama Toko</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Agen</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Lokasi Event</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Total Point</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Kupon</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Oleh</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-50">
                        @forelse($histories as $i => $history)
                        <tr class="hover:bg-red-50/40 transition">
                            <td class="px-4 py-3 text-gray-400 text-xs">
                                {{ $histories->firstItem() + $i }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                {{ $history->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($history->aksi === 'create')
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-600 px-2 py-0.5 rounded-full">
                                        ✚ Create
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-600 px-2 py-0.5 rounded-full">
                                        ✎ Update
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $history->nama_toko ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <div class="text-xs text-gray-400">{{ $history->kode_agen }}</div>
                                {{ $history->nama_agen ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $history->lokasi_event ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold text-red-600">
                                {{ number_format($history->total_point, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $history->total_kupon }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $history->username ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('history-form-order.show', $history->id) }}"
                                    class="inline-flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-gray-400 text-sm">
                                Belum ada data history
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($histories->hasPages())
            <div class="px-5 py-4 border-t border-red-50">
                {{ $histories->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>