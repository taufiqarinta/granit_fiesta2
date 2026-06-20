<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Report') }}
        </h2>
    </x-slot>

    <div class="container p-4 mx-auto">
        <form method="GET" action="{{ route('daily.report') }}" class="flex items-center gap-2 mb-4">
            <div class="flex flex-wrap items-end gap-4 mb-4">
                <div class="flex flex-col">
                    <label for="month" class="mb-1 font-semibold">Bulan:</label>
                    <select name="month" id="month" class="px-3 py-1 border border-gray-300 rounded">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}"
                                {{ request('month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="year" class="mb-1 font-semibold">Tahun:</label>
                    <select name="year" id="year" class="py-1 border border-gray-300 rounded px-7">
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <br>
                <div>
                    <button type="submit"
                        class="px-4 py-2 mt-1 font-semibold text-black transition duration-200 bg-blue-600 rounded shadow hover:bg-blue-700">
                        Filter Report
                    </button>

                </div>
            </div>
        </form>
        @php
            $bulanNama = \Carbon\Carbon::createFromFormat('m', request('month', now()->format('m')))->translatedFormat(
                'F',
            );
            $tahun = request('year', now()->format('Y'));
        @endphp

        @if (request()->has('month') && request()->has('year'))
            <h1 class="mb-2 text-3xl font-bold text-center">TIME REPORT</h1>
            <h2 class="mb-2 text-2xl font-semibold text-center">PERIODE {{ strtoupper($bulanNama) }} {{ $tahun }}
            </h2>
            {{-- <h3 class="mb-4 text-xl font-medium text-center">FINANCE ACCOUNTING TAX TANRISE</h3> --}}

            <div class="p-2 overflow-auto bg-gray-100 border rounded-lg">

                <table class="w-full text-sm border border-collapse border-gray-400 table-auto">
                    <thead>
                        <tr class="text-white">
                            <td></td>
                            <th class="p-2 bg-green-700 border border-gray-300" rowspan="4">Nama
                            </th>
                            @foreach ($projects as $project)
                                @php
                                    $relatedSubprojects = $subprojects->where('project_id', $project->id);
                                    $colspan = $relatedSubprojects->count() * 5; // anggap setiap subproject punya 5 kolom (w1-w5)
                                @endphp
                                <th class="p-2 bg-green-700 border border-gray-300" colspan="{{ $colspan }}">
                                    {{ $project->name }}
                                </th>
                            @endforeach
                            <th class="p-2 text-green-700 bg-white border border-gray-300" rowspan="3"
                                colspan="5">
                                CONTROL</th>
                            <th class="p-2 bg-gray-500 border border-gray-300" rowspan="4">TOTAL JAM</th>
                            <th class="p-2 bg-gray-500 border border-gray-300" rowspan="4">KETERANGAN</th>
                        </tr>
                        <tr class="text-white">
                            <td></td>
                            @foreach ($projects as $project)
                                @php
                                    $relatedSubprojects = $subprojects->where('project_id', $project->id);
                                    $groupedByPT = $relatedSubprojects->groupBy('nama_pt');
                                @endphp
                                @foreach ($groupedByPT as $ptName => $groupedSubprojects)
                                    @php
                                        $colspan = $groupedSubprojects->count() * 5;
                                    @endphp
                                    <th class="px-2 bg-green-700 border border-gray-300" colspan="{{ $colspan }}">
                                        {{ $ptName }}
                                    </th>
                                @endforeach
                            @endforeach
                        </tr>
                        <tr class="text-xs text-white uppercase bg-green-700">
                            <td class="bg-gray-100"></td>
                            @foreach ($projects as $project)
                                @php
                                    $relatedSubprojects = $subprojects->where('project_id', $project->id);
                                @endphp
                                @foreach ($relatedSubprojects as $subproject)
                                    <th class="p-2 bg-green-700 border border-gray-300" colspan="5">
                                        {{ $subproject->nama_sub_project }}
                                    </th>
                                @endforeach
                            @endforeach
                        </tr>
                        <tr class="text-xs text-white uppercase bg-green-700">
                            <td class="bg-gray-100"></td>
                            @foreach ($projects as $project)
                                @php
                                    $relatedSubprojects = $subprojects->where('project_id', $project->id);
                                @endphp
                                @foreach ($relatedSubprojects as $subproject)
                                    @for ($i = 1; $i <= 5; $i++)
                                        <th class="px-2 border border-gray-300">w{{ $i }}</th>
                                    @endfor
                                @endforeach
                            @endforeach
                            <th class="px-2 text-green-700 bg-white border border-gray-300">w1</th>
                            <th class="px-2 text-green-700 bg-white border border-gray-300">w2</th>
                            <th class="px-2 text-green-700 bg-white border border-gray-300">w3</th>
                            <th class="px-2 text-green-700 bg-white border border-gray-300">w4</th>
                            <th class="px-2 text-green-700 bg-white border border-gray-300">w5</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grouped = $users->groupBy(fn($user) => $user->departement->name ?? 'LAINNYA');
                            $totalDynamicColumns = $subprojects->count() * 5;
                        @endphp

                        @foreach ($grouped as $departement => $employees)
                            @php $rowspan = $employees->count(); @endphp
                            @foreach ($employees as $index => $employee)
                                <tr>
                                    @if ($index === 0)
                                        <td class="p-2 font-semibold uppercase border border-gray-300"
                                            rowspan="{{ $rowspan }}">
                                            {{ $departement }}
                                        </td>
                                    @endif
                                    @php
                                        $totalJam = 0;
                                        foreach ($groupedActivities as $userData) {
                                            foreach ($userData as $projData) {
                                                foreach ($projData as $subData) {
                                                    foreach ($subData as $waktu) {
                                                        $totalJam += $waktu;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    {{-- <td class="text-center bg-red-100 border border-gray-300">{{ $totalJam }}</td> --}}

                                    @php
                                        $targetJam = $employee->total_jam ?? 160; // Default kalau gak ada
                                        $totalJamColor =
                                            $totalJam >= $targetJam
                                                ? 'bg-green-600 text-white'
                                                : 'bg-red-600 text-white';
                                    @endphp
                                    <td class="p-2 font-semibold border border-gray-300 {{ $totalJamColor }}">
                                        {{ $employee->name }}</td>
                                    {{-- @for ($j = 0; $j < $totalDynamicColumns; $j++)
                                        <td class="text-center border border-gray-300">
                                            <div class="w-10" contenteditable="false"></div>
                                        </td>
                                    @endfor --}}

                                    @foreach ($projects as $project)
                                        @php
                                            $relatedSubprojects = $subprojects->where('project_id', $project->id);
                                        @endphp
                                        @foreach ($relatedSubprojects as $subproject)
                                            @for ($w = 1; $w <= 5; $w++)
                                                @php
                                                    $weekKey = 'w' . $w;
                                                    $value =
                                                        $groupedActivities[$employee->id][$project->id][
                                                            $subproject->id
                                                        ][$weekKey] ?? 0;
                                                @endphp
                                                <td class="text-center border border-gray-300 font-semibold whitespace-nowrap cursor-pointer text-blue-600 hover:underline"
                                                    onclick="showDetailModal({{ $employee->id }}, {{ $project->id }}, {{ $subproject->id }}, '{{ $subproject->nama_pt }}', '{{ $subproject->nama_sub_project }}', '{{ $weekKey }}')">
                                                    {{ $value }}
                                                </td>
                                            @endfor
                                        @endforeach
                                    @endforeach

                                    {{-- Kolom control tetap --}}
                                    {{-- <td class="text-center border border-gray-300"> </td>
                                    <td class="text-center border border-gray-300"> </td>
                                    <td class="text-center border border-gray-300"> </td>
                                    <td class="text-center border border-gray-300"> </td>
                                    <td class="text-center border border-gray-300"> </td> --}}
                                    @for ($i = 1; $i <= 5; $i++)
                                        @php
                                            $weekKey = 'w' . $i;
                                            $totalControl = 0;
                                            foreach ($groupedActivities as $userData) {
                                                foreach ($userData as $projData) {
                                                    foreach ($projData as $subData) {
                                                        $totalControl += $subData[$weekKey] ?? 0;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <td class="text-center bg-blue-100 border border-gray-300">{{ $totalControl }}
                                        </td>
                                    @endfor

                                    {{-- <td class="font-bold text-center border border-gray-300">0</td> --}}
                                    {{-- Total Jam --}}

                                    <td class="text-center {{ $totalJamColor }} font-semibold border border-gray-300">
                                        {{ $totalJam }}
                                    </td>

                                    @php
                                        $bulan = request('month', now()->format('m'));
                                        $tahun = request('year', now()->format('Y'));

                                        $startOfMonth = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
                                        $endOfMonth = $startOfMonth->copy()->endOfMonth();

                                        // Ambil data aktivitas user yang sesuai bulan & tahun
                                        $records = $employee
                                            ->dailyActivities()
                                            ->whereBetween('tanggal', [
                                                $startOfMonth->toDateString(),
                                                $endOfMonth->toDateString(),
                                            ])
                                            ->get();

                                        // Group by tanggal, lalu ambil satu record untuk tiap tanggal
                                        $uniquePerDay = $records
                                            ->groupBy(fn($r) => \Carbon\Carbon::parse($r->tanggal)->format('Y-m-d'))
                                            ->map(fn($group) => $group->first());

                                        $countCuti = $uniquePerDay->where('cuti', 1)->count();
                                        $countIjin = $uniquePerDay->where('ijin', 1)->count();
                                        $countSakit = $uniquePerDay->where('sakit', 1)->count();

                                        $countKerja = $uniquePerDay
                                            ->filter(fn($r) => !$r->cuti && !$r->ijin && !$r->sakit)
                                            ->count();

                                        // Buat semua tanggal dalam bulan
                                        $tanggalDiisi = $uniquePerDay
                                            ->pluck('tanggal')
                                            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
                                            ->unique();

                                        $tanggalLengkap = collect(
                                            \Carbon\CarbonPeriod::create($startOfMonth, $endOfMonth),
                                        )->map(fn($d) => $d->format('Y-m-d'));

                                        $tanggalLibur = $tanggalLengkap->diff($tanggalDiisi);
                                        $countLibur = $tanggalLibur->count();
                                    @endphp

                                    <td class="text-xs text-center border border-gray-300 leading-5">
                                        <span class="block text-green-700">Kerja: {{ $countKerja }}</span>
                                        <span class="block text-yellow-600">Cuti: {{ $countCuti }}</span>
                                        <span class="block text-orange-500">Ijin Setengah Hari: {{ $countIjin }}</span>
                                        <span class="block text-red-600">Sakit: {{ $countSakit }}</span>
                                        <span class="block text-gray-600">Libur: {{ $countLibur }}</span>
                                    </td>

                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                @if ($dailyActivities->isEmpty())
                    <p class="mt-4 font-semibold text-center text-red-600">Tidak ada data aktivitas untuk periode ini.
                    </p>
                @endif
            </div>
        @endif
    </div>

    <!-- resources/views/components/detail-modal.blade.php -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
        <div class="relative max-w-md p-6 mx-auto mt-20 bg-white rounded-lg shadow-lg">
            <div class="flex justify-between mb-4">
                <h3 class="text-lg font-bold">Detail Jam per Minggu</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-black">X</button>
            </div>
            <div class="mb-4">
                <input type="number" id="searchTanggal" oninput="filterTanggal()"
                    placeholder="Cari Tanggal (misal: 29)" class="w-full px-4 py-2 border border-gray-300 rounded"
                    min="1" max="31">
            </div>
            <table class="w-full text-sm text-left border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">Tanggal</th>
                        <th class="p-2 border">Keterangan</th>
                        <th class="p-2 border">Waktu</th>
                    </tr>
                </thead>
                <tbody id="modalTableBody"></tbody>
            </table>
            <div id="noDataMessage" class="mt-4 text-center text-red-500 font-semibold hidden">Tidak ada data</div>
            <div id="paginationControls" class="flex justify-center mt-4 gap-2">
                <button onclick="changePage(-1)" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Prev</button>
                <span id="pageInfo" class="px-2 py-1"></span>
                <button onclick="changePage(1)" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Next</button>
            </div>

            <div id="totalJamContainer" class="mt-4 text-center font-semibold hidden"></div>
        </div>
    </div>

    <script>
        const detailActivities = @json($detailActivities ?? []);
        let lastModalData = [];
        let currentPage = 1;
        const perPage = 5;
        let filteredData = null;

        function normalize(str) {
            return (str ?? '').toString().trim().toLowerCase();
        }

        function showDetailModal(userId, projectId, subprojectId, namaPt, namaSubProject, weekKey) {
            const tbody = document.getElementById('modalTableBody');
            tbody.innerHTML = '';
            lastModalData = [];
            currentPage = 1;

            const detail = detailActivities?.[userId]?.[projectId]?.[subprojectId] || {};

            // const weekData = detail[weekKey] ?? [];
            const weekData = Object.values(detail).flat(); // Ambil semua data aktivitas

            const filteredDetail = weekData.filter(entry =>
                normalize(entry.nama_pt) === normalize(namaPt) &&
                normalize(entry.nama_sub_project) === normalize(namaSubProject) &&
                entry.week === weekKey // Tambahkan filter week di sini
            );


            filteredDetail.forEach(entry => {
                const tanggal = new Date(entry.tanggal.split('/').reverse().join('-'));
                const dayOfMonth = tanggal.getDate();
                lastModalData.push({
                    tanggal: entry.tanggal,
                    day: dayOfMonth,
                    week: entry.week,
                    keterangan: entry.keterangan ?? '-',
                    jam: parseFloat(entry.jam) || 0
                });
            });

            renderTable(lastModalData);
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function renderTable(dataInput, searchKeyword = null) {
            const tbody = document.getElementById('modalTableBody');
            tbody.innerHTML = '';
            const data = dataInput ?? (filteredData ?? lastModalData);
            const totalPages = Math.ceil(data.length / perPage);
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            const paginatedData = data.slice(start, end);

            document.getElementById('paginationControls').style.display = data.length > perPage ? 'flex' : 'none';
            document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;

            if (paginatedData.length === 0 && searchKeyword !== null) {
                const bulan = {{ request('month') }};
                const tahun = {{ request('year') }};
                const bulanNama = new Date(tahun, bulan - 1).toLocaleString('default', {
                    month: 'long'
                });

                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 3;
                cell.className = 'p-4 text-center text-red-600 font-semibold';
                cell.textContent = `Tidak ada data pada tanggal ${searchKeyword} ${bulanNama} ${tahun}`;
                row.appendChild(cell);
                tbody.appendChild(row);
                return;
            }

            paginatedData.forEach(entry => {
                const row = document.createElement('tr');

                const tanggalCell = document.createElement('td');
                tanggalCell.className = 'p-2 text-center border';
                tanggalCell.textContent = `${entry.tanggal} (week ${entry.week})`;
                row.appendChild(tanggalCell);

                const ketCell = document.createElement('td');
                ketCell.className = 'p-2 text-center border';
                ketCell.textContent = entry.keterangan;
                row.appendChild(ketCell);

                const jamCell = document.createElement('td');
                jamCell.className = 'p-2 text-center border';
                jamCell.textContent = `${(entry.jam % 1 === 0 ? entry.jam : entry.jam.toFixed(1))} jam`;
                row.appendChild(jamCell);

                tbody.appendChild(row);
            });

            // Tambahkan ini di akhir renderTable()
            const totalJamContainer = document.getElementById('totalJamContainer');
            if (searchKeyword !== null) {
                const totalJam = data.reduce((sum, entry) => sum + (entry.jam || 0), 0);
                totalJamContainer.textContent =
                    `Total Jam pada tanggal ${searchKeyword}: ${totalJam % 1 === 0 ? totalJam : totalJam.toFixed(1)} jam`;
                totalJamContainer.classList.remove('hidden');
            } else {
                totalJamContainer.classList.add('hidden');
                totalJamContainer.textContent = '';
            }
        }

        function changePage(direction) {
            const dataToUse = filteredData ?? lastModalData;
            const totalPages = Math.ceil(dataToUse.length / perPage);
            currentPage += direction;
            if (currentPage < 1) currentPage = 1;
            if (currentPage > totalPages) currentPage = totalPages;
            renderTable(dataToUse);
        }

        function filterTanggal() {
            const keyword = document.getElementById('searchTanggal').value.trim();
            currentPage = 1;

            if (!keyword) {
                filteredData = null;
                renderTable(lastModalData);
                return;
            }

            const searchNum = parseInt(keyword, 10);
            filteredData = lastModalData.filter(row => row.day === searchNum);
            renderTable(filteredData, keyword);
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>

</x-app-layout>
