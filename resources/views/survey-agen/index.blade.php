<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Form Survey') }}
        </h2>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>

    <div class="py-6 px-4 max-w-8xl mx-auto">

        {{-- Tombol Scan QR --}}
        <!-- <div class="mb-6 flex justify-end">
            <a href="{{ route('form-survey.scan-qr') }}" 
               class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition flex items-center gap-2">
                Scan QR
            </a>
        </div> -->

        {{-- Filter --}}
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5 mb-6">
            <form method="GET" action="{{ route('form-survey.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Cari Kode Survey, Kode Agen, atau Nama Agen</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Masukkan kata kunci pencarian..."
                        class="w-full border border-red-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        🔍 Cari
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('form-survey.index') }}"
                            class="bg-white border border-red-200 text-gray-500 hover:border-red-400 hover:text-red-500 text-sm font-semibold px-4 py-2 rounded-lg transition">
                            ✕ Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-red-50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-sm">
                    Daftar Survey Agen
                    <span class="ml-2 text-xs font-normal text-gray-400">({{ $surveys->total() }} data)</span>
                </h3>
                <a href="{{ route('form-survey.scan-qr') }}" 
                class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition flex items-center gap-2">
                    Scan QR
                </a>
                <!-- <a href="{{ route('form-survey.form') }}" 
                   class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                    + Buat Survey Baru
                </a> -->
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-red-50 text-left">
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide w-16">No</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Kode Survey</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Kode Agen</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Nama Agen</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Sales</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide text-center">Status Klaim</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-50">
                        @forelse($surveys as $index => $survey)
                        <tr class="hover:bg-red-50/40 transition">
                            <td class="px-4 py-3 text-gray-400 text-xs text-center">
                                {{ $surveys->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">
                                {{ $survey->kode_survey }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-xs">
                                {{ $survey->kode_agen }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $survey->nama_agen }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ optional($survey->details->first())->nama_sales ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($survey->status_klaim_hadiah == 1)
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-600 px-2 py-0.5 rounded-full">
                                        ✓ Sudah
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-600 px-2 py-0.5 rounded-full">
                                        ✗ Belum
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('form-survey.detail', $survey->kode_survey) }}"
                                    class="inline-flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">
                                @if(!empty($search))
                                    Tidak ada survey yang cocok dengan pencarian "{{ $search }}"
                                @else
                                    Belum ada data survey agen
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($surveys->hasPages())
            <div class="px-5 py-4 border-t border-red-50">
                {{ $surveys->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>