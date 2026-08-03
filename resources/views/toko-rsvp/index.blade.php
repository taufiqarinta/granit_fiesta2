<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data RSVP Toko Granite Fiesta V2') }}
        </h2>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>

    <div class="py-6 px-4 max-w-8xl mx-auto">

        {{-- Filter --}}
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5 mb-6">
            <form method="GET" action="{{ route('toko-rsvp.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[220px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Cari Toko, Agen, PIC, atau Kota</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Masukkan kata kunci pencarian..."
                        class="w-full border border-red-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                </div>

                <div class="min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Lokasi Event</label>
                    <select name="lokasi_event"
                        class="w-full border border-red-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                        <option value="">-- Semua Lokasi --</option>
                        @foreach($lokasiEvents as $lokasi)
                            <option value="{{ $lokasi->nama_lokasi }}" @selected($lokasiEvent == $lokasi->nama_lokasi)>
                                {{ $lokasi->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Status RSVP</label>
                    <select name="status"
                        class="w-full border border-red-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                        <option value="all" @selected($status == 'all')>Semua</option>
                        <option value="sudah" @selected($status == 'sudah')>Sudah RSVP</option>
                        <option value="belum" @selected($status == 'belum')>Belum RSVP</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        🔍 Cari
                    </button>
                    @if(!empty($search) || !empty($lokasiEvent) || ($status ?? 'all') !== 'all')
                        <a href="{{ route('toko-rsvp.index') }}"
                            class="bg-white border border-red-200 text-gray-500 hover:border-red-400 hover:text-red-500 text-sm font-semibold px-4 py-2 rounded-lg transition">
                            ✕ Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Toko</p>
                    <p class="text-2xl font-800 text-gray-800 mt-1">{{ $totalToko }}</p>
                </div>
                <span class="text-3xl">🏪</span>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Sudah RSVP</p>
                    <p class="text-2xl font-800 text-green-600 mt-1">{{ $totalSudah }}</p>
                </div>
                <span class="text-3xl">✅</span>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Belum RSVP</p>
                    <p class="text-2xl font-800 text-red-600 mt-1">{{ $totalBelum }}</p>
                </div>
                <span class="text-3xl">⏳</span>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-red-50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-sm">
                    Daftar Toko RSVP
                    <span class="ml-2 text-xs font-normal text-gray-400">({{ $paginator->total() }} toko)</span>
                </h3>
                <div class="flex gap-2">
                    <a href="{{ route('toko-rsvp.export-merge', request()->query()) }}"
                        class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                        📊 Export (Merge)
                    </a>
                    <a href="{{ route('toko-rsvp.export', request()->query()) }}"
                        class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                        📄 Export (No Merge)
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-red-50 text-left">
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Lokasi Event</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Kode Toko</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Nama Toko</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">PIC</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Nomor PIC</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Kota</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Kode Agen</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Nama Agen</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide text-center">Status</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide text-center">Updated at</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-50">
                        @forelse($groups as $kodeToko => $agenRows)
                            @php $rowCount = $agenRows->count(); @endphp
                            @foreach($agenRows as $i => $row)
                                <tr class="hover:bg-red-50/40 transition">
                                    @if($i === 0)
                                        <td class="px-4 py-3 text-gray-600 align-top" rowspan="{{ $rowCount }}">{{ $row->lokasi_event }}</td>
                                        <td class="px-4 py-3 font-mono text-xs text-gray-600 align-top" rowspan="{{ $rowCount }}">{{ $row->kode_toko }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-800 align-top" rowspan="{{ $rowCount }}">{{ $row->nama_toko }}</td>
                                        <td class="px-4 py-3 text-gray-600 align-top" rowspan="{{ $rowCount }}">{{ $row->pic ?: '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600 align-top" rowspan="{{ $rowCount }}">{{ $row->nomor_pic ?: '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600 align-top" rowspan="{{ $rowCount }}">{{ $row->kota ?: '-' }}</td>
                                    @endif
                                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $row->kode_agen }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $row->nama_agen }}</td>
                                    @if($i === 0)
                                        <td class="px-4 py-3 text-center align-top" rowspan="{{ $rowCount }}">
                                            @if((int) $row->konfirmasi_kehadiran === 1)
                                                <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-600 px-2 py-0.5 rounded-full">
                                                    ✓ Sudah
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-600 px-2 py-0.5 rounded-full">
                                                    ✗ Belum
                                                </span>
                                            @endif
                                        </td>
                                        @if (((int) $row->konfirmasi_kehadiran === 1) && $row->updated_at)
                                            <td class="px-4 py-3 text-center align-top" rowspan="{{ $rowCount }}">
                                                {{ $row->updated_at->format('d M Y H:i') }}
                                            </td>
                                        @else
                                            <td class="px-4 py-3 text-center align-top" rowspan="{{ $rowCount }}">
                                                -
                                            </td>
                                        @endif
                                    @endif
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center text-gray-400 text-sm">
                                    @if(!empty($search))
                                        Tidak ada data yang cocok dengan pencarian "{{ $search }}"
                                    @else
                                        Belum ada data toko RSVP
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($paginator->hasPages())
            <div class="px-5 py-4 border-t border-red-50">
                {{ $paginator->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>