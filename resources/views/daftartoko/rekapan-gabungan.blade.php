<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Rekapan Kehadiran & Order') }}
            </h2>
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

        .overflow-x-scroll {
            max-height: calc(100vh - 300px);
            overflow-y: auto;
        }

        thead {
            position: sticky;
            top: 0;
            z-index: 20;
        }

        thead th {
            background-color: #f3f4f6;
            position: sticky;
            top: 0;
            z-index: 20;
        }
    </style>

    <script src="https://cdn.tailwindcss.com"></script>

    <div class="py-12">
        <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="p-4 sm:p-6 text-gray-900">
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <form id="rekapan-filter-form" action="{{ route('daftartoko.rekapan-gabungan') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                                <input type="text"
                                    name="search"
                                    id="search"
                                    value="{{ $search ?? '' }}"
                                    placeholder="Cari berdasarkan nama agen, nama toko, kota, atau lokasi event..."
                                    autocomplete="off"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="flex-1">
                                <label for="tipe_filter" class="block text-sm font-medium text-gray-700 mb-1">
                                    Filter Tipe:
                                </label>
                                <select id="tipe_filter" name="tipe"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="semua" {{ ($tipe ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua Tipe</option>
                                    <option value="TOKO" {{ ($tipe ?? 'semua') === 'TOKO' ? 'selected' : '' }}>TOKO</option>
                                    <option value="AGEN" {{ ($tipe ?? 'semua') === 'AGEN' ? 'selected' : '' }}>AGEN</option>
                                </select>
                            </div>

                            <div class="flex-1">
                                <label for="sumber_filter" class="block text-sm font-medium text-gray-700 mb-1">
                                    Filter Sumber Data:
                                </label>
                                <select id="sumber_filter" name="sumber_data"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="semua" {{ ($sumberData ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua Sumber</option>
                                    <option value="DAFTAR_TOKO" {{ ($sumberData ?? 'semua') === 'DAFTAR_TOKO' ? 'selected' : '' }}>DAFTAR_TOKO</option>
                                    <option value="DAFTAR_AGEN" {{ ($sumberData ?? 'semua') === 'DAFTAR_AGEN' ? 'selected' : '' }}>DAFTAR_AGEN</option>
                                    <option value="FORM_ORDER" {{ ($sumberData ?? 'semua') === 'FORM_ORDER' ? 'selected' : '' }}>FORM_ORDER</option>
                                </select>
                            </div>

                            <div class="flex-1">
                                <label for="lokasi_filter" class="block text-sm font-medium text-gray-700 mb-1">
                                    Lokasi Event:
                                </label>
                                <select id="lokasi_filter" name="lokasi_event"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Pilih Lokasi --</option>

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
                                @if($search || (($lokasiEvent ?? '') != '' && ($lokasiEvent ?? '') != 'semua') || (($tipe ?? 'semua') != 'semua') || (($sumberData ?? 'semua') != 'semua'))
                                    <a href="{{ route('daftartoko.rekapan-gabungan') }}"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="table-container">
                        <div class="overflow-x-scroll">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">No</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Lokasi Event</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Tipe</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Sumber Data</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Nama Agen</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Nama Toko</th>
                                        <th class="px-3 py-2 text-center font-semibold text-gray-700">Hadir</th>
                                        <th class="px-3 py-2 text-center font-semibold text-gray-700">Jumlah Kehadiran</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Fasilitas Hotel</th>
                                        <th class="px-3 py-2 text-center font-semibold text-gray-700">Ditempati</th>
                                        <th class="px-3 py-2 text-center font-semibold text-gray-700">Form Order</th>
                                        <th class="px-3 py-2 text-right font-semibold text-gray-700">Order (Point)</th>
                                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Doorprize</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @forelse ($rekapan as $index => $item)
                                        <tr class="hover:bg-gray-50"
                                            data-row="1"
                                            data-type="{{ strtolower($item['type'] ?? '-') }}"
                                            data-source="{{ strtolower($item['source'] ?? '-') }}"
                                            data-search="{{ strtolower(implode(' ', [
                                                $item['type'] ?? '',
                                                $item['source'] ?? '',
                                                $item['nama_agen'] ?? '',
                                                $item['nama_toko'] ?? '',
                                                $item['lokasi_event'] ?? '',
                                                $item['kota'] ?? '',
                                                $item['hotel'] ?? '',
                                                $item['checkin'] ?? '',
                                                $item['doorprize'] ?? '',
                                                $item['order_point'] ?? '',
                                            ])) }}">
                                            <td class="px-3 py-2">{{ ($rekapan->firstItem() ?? 1) + $index }}</td>
                                            <td class="px-3 py-2">{{ ($item['type'] ?? '') === 'AGEN' ? 'Seluruh Lokasi' : ($item['lokasi_event'] ?? '-') }}</td>
                                            <td class="px-3 py-2">{{ $item['type'] ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $item['source'] ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $item['nama_agen'] ?: '-' }}</td>
                                            <td class="px-3 py-2">{{ $item['nama_toko'] ?: '-' }}</td>
                                            <td class="px-3 py-2 text-center">
                                                @if(($item['hadir'] ?? 0) === 1)
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700">&#10003;</span>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-700">&#10007;</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-center">{{ $item['jumlah_kehadiran'] ?? 0 }}</td>
                                            <td class="px-3 py-2">
                                                @if($item['source'] === 'DAFTAR_TOKO' || $item['source'] === 'DAFTAR_AGEN')
                                                    <form class="flex gap-1 form-hotel-ajax"
                                                        data-url="{{ route('daftartoko.update-hotel') }}">
                                                        @csrf
                                                        <input type="hidden" name="type" value="{{ $item['type'] }}">
                                                        <input type="hidden" name="source" value="{{ $item['source'] }}">
                                                        <input type="hidden" name="nama_toko" value="{{ $item['nama_toko'] }}">
                                                        <input type="hidden" name="nama_agen" value="{{ $item['nama_agen'] }}">
                                                        <input type="hidden" name="pic" value="{{ $item['pic'] ?? '' }}">
                                                        <input type="hidden" name="no_hp" value="{{ $item['no_hp'] ?? '' }}">
                                                        <input type="hidden" name="kota" value="{{ $item['kota'] ?? '' }}">
                                                        <input type="hidden" name="lokasi_event" value="{{ $item['lokasi_event'] }}">
                                                        <input type="hidden" name="kode_agen" value="{{ $item['kode_agen'] ?? '' }}">
                                                        <input type="text" name="hotel" value="{{ $item['hotel'] ?? '' }}"
                                                            class="px-2 py-1 border border-gray-300 rounded w-full text-xs uppercase"
                                                            placeholder="Nama hotel..." style="text-transform: uppercase;">
                                                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">Simpan</button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400 text-xs">{{ $item['hotel'] ?? '-' }}</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                @if($item['source'] === 'DAFTAR_TOKO' || $item['source'] === 'DAFTAR_AGEN')
                                                    <form class="flex justify-center form-checkin-ajax"
                                                        data-url="{{ route('daftartoko.update-checkin') }}">
                                                        @csrf
                                                        <input type="hidden" name="type" value="{{ $item['type'] }}">
                                                        <input type="hidden" name="source" value="{{ $item['source'] }}">
                                                        <input type="hidden" name="nama_toko" value="{{ $item['nama_toko'] }}">
                                                        <input type="hidden" name="nama_agen" value="{{ $item['nama_agen'] }}">
                                                        <input type="hidden" name="pic" value="{{ $item['pic'] ?? '' }}">
                                                        <input type="hidden" name="kota" value="{{ $item['kota'] ?? '' }}">
                                                        <input type="hidden" name="no_hp" value="{{ $item['no_hp'] ?? '' }}">
                                                        <input type="hidden" name="lokasi_event" value="{{ $item['lokasi_event'] }}">
                                                        <input type="hidden" name="kode_agen" value="{{ $item['kode_agen'] ?? '' }}">
                                                        <label class="flex items-center cursor-pointer">
                                                            <input type="checkbox" name="checkin" value="1"
                                                                {{ !empty($item['checkin']) ? 'checked' : '' }}
                                                                class="checkin-checkbox w-4 h-4 text-blue-600 rounded">
                                                        </label>
                                                    </form>
                                                @else
                                                    @if(!empty($item['checkin']))
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700">&#10003;</span>
                                                    @elseif(!empty($item['hotel']))
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-700">&#10007;</span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                @if(($item['order_point'] ?? 0) != 0)
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700">&#10003;</span>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-700">&#10007;</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-right font-semibold">{{ number_format($item['order_point'] ?? 0, 0, ',', '.') }}</td>
                                            <td class="px-3 py-2">{{ $item['doorprize'] ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr data-empty-server="1">
                                            <td colspan="13" class="px-3 py-8 text-center text-gray-500">Tidak ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-6 text-sm text-gray-600 mb-3">
                        Total data terlihat: <span id="visible-row-count" class="font-semibold">{{ $totalRows ?? 0 }}</span>
                        <span class="text-gray-400">/ {{ $totalRows ?? 0 }}</span>
                    </div>

                    <div>
                        {{ $rekapan->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── FILTER REALTIME (CLIENT-SIDE UNTUK TIPE & SUMBER) ─────────────────────
            const filterForm = document.getElementById('rekapan-filter-form');
            const searchInput = document.getElementById('search');
            const lokasiFilter = document.getElementById('lokasi_filter');
            const tipeFilter = document.getElementById('tipe_filter');
            const sumberFilter = document.getElementById('sumber_filter');
            const visibleCountEl = document.getElementById('visible-row-count');
            const tbody = document.querySelector('tbody');

            function applyClientFilter() {
                if (!tbody) {
                    return;
                }

                const rows = Array.from(tbody.querySelectorAll('tr[data-row="1"]'));
                const existingNoResult = tbody.querySelector('tr[data-no-result="1"]');
                const query = (searchInput?.value || '').toLowerCase().trim();
                const tipe = (tipeFilter?.value || 'semua').toLowerCase();
                const sumber = (sumberFilter?.value || 'semua').toLowerCase();

                let visibleCount = 0;

                rows.forEach(function (row) {
                    const rowType = row.dataset.type || '';
                    const rowSource = row.dataset.source || '';
                    const rowSearch = row.dataset.search || '';

                    const matchSearch = !query || rowSearch.includes(query);
                    const matchTipe = tipe === 'semua' || rowType === tipe;
                    const matchSumber = sumber === 'semua' || rowSource === sumber;

                    const isMatch = matchSearch && matchTipe && matchSumber;
                    row.style.display = isMatch ? '' : 'none';

                    if (isMatch) {
                        visibleCount++;
                    }
                });

                if (existingNoResult) {
                    existingNoResult.remove();
                }

                if (visibleCount === 0) {
                    const noResultRow = document.createElement('tr');
                    noResultRow.setAttribute('data-no-result', '1');
                    noResultRow.innerHTML = '<td colspan="13" class="px-3 py-8 text-center text-gray-500">Tidak ada data yang sesuai filter.</td>';
                    tbody.appendChild(noResultRow);
                }

                if (visibleCountEl) {
                    visibleCountEl.textContent = String(visibleCount);
                }
            }

            if (filterForm) {
                filterForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    applyClientFilter();
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', applyClientFilter);
            }

            if (tipeFilter) {
                tipeFilter.addEventListener('change', applyClientFilter);
            }

            if (sumberFilter) {
                sumberFilter.addEventListener('change', applyClientFilter);
            }

            // ── DROPDOWN LOKASI: SUBMIT FORM (HIT METHOD) ──────────────────────────────
            if (lokasiFilter) {
                lokasiFilter.addEventListener('change', function () {
                    if (filterForm) {
                        filterForm.submit();
                    }
                });
            }
 
            // ── HOTEL ──────────────────────────────────────────────
            document.querySelectorAll('.form-hotel-ajax').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const url = form.dataset.url;
                    const formData = new FormData(form);
                    const hotelValue = formData.get('hotel');

                    // Ambil identifiers baris ini untuk mencocokkan baris lain
                    const namaToko = formData.get('nama_toko');
                    const pic = formData.get('pic');
                    const noHp = formData.get('no_hp');
                    const kota = formData.get('kota');
                    const lokasiEvent = formData.get('lokasi_event');

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': formData.get('_token'),
                        },
                        body: formData,
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Update semua form hotel yang punya identitas sama
                            document.querySelectorAll('.form-hotel-ajax').forEach(function (otherForm) {
                                const od = new FormData(otherForm);
                                if (
                                    od.get('nama_toko') === namaToko &&
                                    od.get('pic') === pic &&
                                    od.get('no_hp') === noHp &&
                                    od.get('kota') === kota &&
                                    od.get('lokasi_event') === lokasiEvent
                                ) {
                                    otherForm.querySelector('input[name="hotel"]').value = hotelValue;
                                }
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: data.message ?? 'Terjadi kesalahan.' });
                        }
                    })
                    .catch(() => {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
                    });
                });
            });

            // ── CHECKIN ────────────────────────────────────────────
            document.querySelectorAll('.checkin-checkbox').forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const form = checkbox.closest('.form-checkin-ajax');
                    const url = form.dataset.url;
                    const formData = new FormData(form);
                    const isChecked = checkbox.checked;

                    const namaToko = formData.get('nama_toko');
                    const pic = formData.get('pic');
                    const noHp = formData.get('no_hp');
                    const kota = formData.get('kota');
                    const lokasiEvent = formData.get('lokasi_event');

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': formData.get('_token'),
                        },
                        body: formData,
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Sync semua checkbox yang punya identitas sama
                            document.querySelectorAll('.checkin-checkbox').forEach(function (otherCheckbox) {
                                const otherForm = otherCheckbox.closest('.form-checkin-ajax');
                                const od = new FormData(otherForm);
                                if (
                                    od.get('nama_toko') === namaToko &&
                                    od.get('pic') === pic &&
                                    od.get('no_hp') === noHp &&
                                    od.get('kota') === kota &&
                                    od.get('lokasi_event') === lokasiEvent
                                ) {
                                    otherCheckbox.checked = isChecked;
                                }
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            checkbox.checked = !isChecked;
                            Swal.fire({ icon: 'error', title: 'Gagal', text: data.message ?? 'Terjadi kesalahan.' });
                        }
                    })
                    .catch(() => {
                        checkbox.checked = !isChecked;
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
                    });
                });
            });

        });
        </script>
</x-app-layout>
