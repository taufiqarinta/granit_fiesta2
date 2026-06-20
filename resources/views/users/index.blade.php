<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Daily Activity') }}
        </h2>
    </x-slot>

    <div class="px-4 py-8 overflow-hidden">
        <div class="mx-auto">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-2 mb-4 text-sm text-white bg-green-500 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-auto mb-4">
                <div class="flex items-center min-w-[1000px] justify-between gap-4">
                    {{-- Tombol Tambah Aktivitas di kiri --}}
                    <button data-modal-target="add-activity-modal" data-modal-toggle="add-activity-modal"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        type="button">
                        Tambah Aktivitas
                    </button>

                    {{-- Form Upload Excel di tengah --}}
                    <form action="{{ route('daily-activities.import') }}" method="POST" enctype="multipart/form-data"
                        class="flex items-center gap-4">
                        @csrf
                        <input type="file" name="file" accept=".xlsx,.xls"
                            class="block w-60 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer focus:outline-none">
                        <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                            Tambah via Excel
                        </button>
                    </form>

                    {{-- Form Filter Tanggal --}}
                    <form action="{{ route('daily') }}" method="GET" class="flex items-end gap-3">
                        <div>
                            <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') }}"
                                class="w-full p-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring focus:border-blue-300"
                                required>
                        </div>
                        <button type="submit"
                            class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                            Cari Data berdasar Tanggal
                        </button>
                        <a href="{{ route('daily') }}"
                            class="px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                            Lihat Semua Data
                        </a>
                    </form>
                </div>
            </div>

            <div class="overflow-auto">
                <table class="w-full text-sm text-gray-700 border border-gray-200 rounded-lg table-auto">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr class="text-center">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Nama Departement</th>
                            <th class="px-4 py-3">Nama PT</th>
                            <th class="px-4 py-3">Nama Project</th>
                            <th class="px-4 py-3">Waktu (Jam)</th>
                            <th class="px-4 py-3">Keterangan Kegiatan</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center bg-white divide-y divide-gray-200">
                        @foreach ($dailyActivities as $index => $activity)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $dailyActivities->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    {{ \Carbon\Carbon::parse($activity->tanggal)->translatedFormat('d-F-Y') }}</td>
                                <td class="px-4 py-3">
                                    @if ($activity->cuti == 1)
                                        Cuti
                                    @elseif ($activity->ijin == 1)
                                        Ijin Setengah Hari
                                    @elseif ($activity->sakit == 1)
                                        Sakit
                                    @else
                                        Kerja
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $activity->user->departement->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $activity->subProject->nama_pt ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $activity->subProject->nama_sub_project ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    {{ rtrim(rtrim(number_format($activity->waktu, 2, '.', ''), '0'), '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if (empty($activity->keterangan))
                                        <span class="text-red-600">Kamu belum menceritakan kegiatanmu</span>
                                    @else
                                        {{ $activity->keterangan }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 space-x-2">
                                    <button
                                        onclick="openEditModal(
                                        {{ $activity->id }},
                                        '{{ $activity->subProject->nama_pt }}',
                                        '{{ $activity->subProject->nama_sub_project }}',
                                        '{{ $activity->tanggal }}',
                                        '{{ $activity->waktu }}',
                                        '{{ $activity->keterangan }}',
                                        {{ $activity->cuti }},
                                        {{ $activity->ijin }},
                                        {{ $activity->sakit }}
                                    )"
                                        class="text-blue-600 hover:text-blue-800">Edit</button>
                                    <button onclick="openDeleteModal({{ $activity->id }})"
                                        class="text-red-600 hover:text-red-800">Hapus</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if (request()->filled('tanggal'))
                    <div class="mt-4 text-right text-base font-semibold text-gray-700">
                        Total Jam pada Tanggal
                        {{ \Carbon\Carbon::parse(request('tanggal'))->translatedFormat('d F Y') }}:
                        {{ rtrim(rtrim(number_format($totalWaktu, 2, '.', ''), '0'), '.') }} Jam
                    </div>
                @endif

            </div>


            <div class="mt-4">
                {{ $dailyActivities->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="add-activity-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center transition duration-300 ease-out">
        <div class="relative w-full max-w-md max-h-[90vh] overflow-y-auto p-6 bg-white rounded-lg shadow-lg">


            <!-- Modal header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Aktivitas</h3>
                <button type="button"
                    class="w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900"
                    data-modal-toggle="add-activity-modal">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <form id="activity-form" method="POST" action="{{ route('daily.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col">
                        <label for="pt-input" class="block text-sm font-medium mb-1">Nama PT</label>
                        <select id="pt-input" name="nama_pt"
                            class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            required>
                            <!-- PT akan dimuat dinamis -->
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label for="subproject-input" class="block text-sm font-medium mb-1">Project</label>
                        <select id="subproject-input" name="nama_sub_project"
                            class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            required disabled>
                            <!-- Subproject akan dimuat dinamis -->
                        </select>
                    </div>
                </div>
                <!-- Akhir perubahan -->

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Tanggal</label>
                        <input type="date" name="tanggal" class="w-full p-2 border-gray-300 rounded-lg" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Tipe</label>
                        <select id="kehadiran-select" class="w-full p-2 border-gray-300 rounded-lg">
                            <option value="">Kerja</option>
                            <option value="cuti">Cuti</option>
                            <option value="ijin">Ijin Setengah Hari</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>

                    <!-- Hidden inputs untuk dikirim -->
                    <input type="hidden" name="cuti" value="0" id="input-cuti">
                    <input type="hidden" name="ijin" value="0" id="input-ijin">
                    <input type="hidden" name="sakit" value="0" id="input-sakit">

                    <div>
                        <label class="block text-sm font-medium">Waktu (Jam)</label>
                        <input type="number" step="0.1" name="waktu"
                            class="w-full p-2 border-gray-300 rounded-lg" required>

                        @error('waktu')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Keterangan</label>
                        <textarea name="keterangan" rows="3" class="w-full p-2 border-gray-300 rounded-lg"
                            placeholder="Ceritakan kegiatanmu hari ini..." required></textarea>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-medium rounded-lg text-sm px-5 py-2.5">
                    Simpan
                </button>
            </form>

        </div>
    </div>

    @if ($errors->any())
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                document.getElementById('add-activity-modal').classList.remove('hidden');
            });
        </script>
    @endif

    <!-- Modal Edit -->
    <div id="edit-activity-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="relative w-full max-w-md p-6 bg-white rounded-lg shadow-lg">

            <!-- Modal header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Edit Aktivitas</h3>
                <button type="button"
                    class="w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900"
                    onclick="closeModal('edit-activity-modal')">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7L1 13" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <form id="edit-activity-form" method="POST" action="" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit-id">

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Nama PT</label>
                        <select id="edit-pt-input" name="nama_pt" class="w-full p-2 border-gray-300 rounded-lg"
                            required>
                            <option value="">Pilih Nama PT</option>
                        </select>
                        <input type="text" id="edit-pt-input-text" name="nama_pt_text"
                            class="w-full p-2 border-gray-300 rounded-lg mt-2" placeholder="Atau ketik..."
                            style="display: none;">
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Project</label>
                        <select id="edit-subproject-input" name="nama_sub_project"
                            class="w-full p-2 border-gray-300 rounded-lg" required>
                            <option value="">Pilih Project</option>
                        </select>
                        <input type="text" id="edit-subproject-input-text" name="nama_sub_project_text"
                            class="w-full p-2 border-gray-300 rounded-lg mt-2" placeholder="Atau ketik..."
                            style="display: none;">
                    </div>
                </div>


                <div class="grid grid-cols-1 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium">Tanggal</label>
                        <input type="date" name="tanggal" id="edit-tanggal"
                            class="w-full p-2 border-gray-300 rounded-lg" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Tipe</label>
                        <select id="edit-jenis-izin" name="jenis_izin" class="w-full p-2 border-gray-300 rounded-lg">
                            <option value="">Kerja</option>
                            <option value="cuti">Cuti</option>
                            <option value="ijin">Ijin Setengah Hari</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Waktu (Jam)</label>
                        <input type="number" step="0.1" name="waktu" id="edit-waktu"
                            class="w-full p-2 border-gray-300 rounded-lg" required>

                        @error('waktu')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Keterangan</label>
                        <textarea name="keterangan" id="edit-keterangan" rows="3" class="w-full p-2 border-gray-300 rounded-lg"
                            placeholder="Ceritakan kegiatanmu hari ini..." required></textarea>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-medium rounded-lg text-sm px-5 py-2.5 mt-4">
                    Simpan Perubahan
                </button>
            </form>

        </div>
    </div>

    @if ($errors->any())
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                document.getElementById('edit-activity-modal').classList.remove('hidden');

                const oldId = @json(old('id'));
                const oldPt = @json(old('nama_pt'));
                let oldSubproject = @json(old('nama_sub_project'));
                const oldTanggal = @json(old('tanggal'));
                const oldWaktu = @json(old('waktu'));
                const oldKeterangan = @json(old('keterangan'));

                const ptInput = document.getElementById('edit-pt-input').tomselect;
                const subprojectInput = document.getElementById('edit-subproject-input').tomselect;

                // Handle null subproject
                if (!oldSubproject || oldSubproject === "null") {
                    oldSubproject = "-";
                }

                document.getElementById('edit-id').value = oldId;
                document.getElementById('edit-tanggal').value = oldTanggal;
                document.getElementById('edit-waktu').value = oldWaktu;
                document.getElementById('edit-keterangan').value = oldKeterangan;
                document.getElementById('edit-activity-form').action = `/daily/${oldId}`;

                // PT
                ptInput.clearOptions();
                ptInput.addOption({
                    value: "",
                    text: "Pilih Nama PT"
                });
                Object.keys(subprojectsGroupedByPTEdit).forEach(function(ptName) {
                    ptInput.addOption({
                        value: ptName,
                        text: ptName
                    });
                });
                ptInput.refreshOptions(false);
                ptInput.setValue(oldPt);

                // Subproject
                updateSubprojects(oldPt);
                if (subprojectsGroupedByPTEdit[oldPt] && subprojectsGroupedByPTEdit[oldPt].includes(oldSubproject)) {
                    subprojectInput.setValue(oldSubproject);
                } else {
                    subprojectInput.addOption({
                        value: oldSubproject,
                        text: oldSubproject
                    });
                    subprojectInput.refreshOptions(false);
                    subprojectInput.setValue(oldSubproject);
                }
            });
        </script>
    @endif


    <!-- Modal Konfirmasi Hapus -->
    <div id="delete-activity-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="relative w-full max-w-sm p-6 bg-white rounded-lg shadow-lg">
            <div class="relative">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                    <button type="button"
                        class="w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900"
                        onclick="closeModal('delete-activity-modal')">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7L1 13" />
                        </svg>
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="py-4 text-gray-700 modal-body">
                    Apakah Anda yakin ingin menghapus data ini?
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end pt-4 space-x-2 border-t border-gray-200">
                    <button onclick="closeModal('delete-activity-modal')"
                        class="px-4 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">
                        Batal
                    </button>
                    <form id="delete-activity-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="id" id="delete-id">
                        <button type="submit"
                            class="px-4 py-2 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        let subprojectsGroupedByPT = {};

        $(document).ready(function() {
            $.ajax({
                url: '/get-subprojects',
                type: 'GET',
                success: function(data) {
                    subprojectsGroupedByPT = {}; // reset
                    $('#pt-input').empty().append('<option value="">Pilih Nama PT</option>');
                    $('#subproject-input').empty().append(
                        '<option value="">Pilih Project</option>');

                    let ptSet = new Set(); // buat ngecek PT unique
                    data.forEach(function(item) {
                        if (!subprojectsGroupedByPT[item.nama_pt]) {
                            subprojectsGroupedByPT[item.nama_pt] = [];
                        }
                        subprojectsGroupedByPT[item.nama_pt].push(item.nama_sub_project);

                        if (!ptSet.has(item.nama_pt)) {
                            $('#pt-input').append(
                                `<option value="${item.nama_pt}">${item.nama_pt}</option>`);
                            ptSet.add(item.nama_pt);
                        }
                    });

                    // Aktifkan TomSelect
                    new TomSelect('#pt-input', {
                        create: false,
                        sortField: {
                            field: "text",
                            direction: "asc"
                        }
                    });

                    new TomSelect('#subproject-input', {
                        create: false,
                        sortField: {
                            field: "text",
                            direction: "asc"
                        }
                    });
                }
            });

            // Saat user pilih Nama BU
            $(document).on('change', '#pt-input', function() {
                let selectedPT = $(this).val();
                let subprojectSelect = $('#subproject-input')[0].tomselect;

                subprojectSelect.clearOptions();
                subprojectSelect.addOption({
                    value: "",
                    text: "Pilih Project"
                });

                if (subprojectsGroupedByPT[selectedPT] && subprojectsGroupedByPT[selectedPT].some(sp =>
                        sp && sp.trim() !== '')) {
                    subprojectsGroupedByPT[selectedPT].forEach(function(subproject) {
                        if (subproject && subproject.trim() !== '') {
                            subprojectSelect.addOption({
                                value: subproject,
                                text: subproject
                            });
                        }
                    });
                    subprojectSelect.enable();
                } else {
                    subprojectSelect.addOption({
                        value: "-",
                        text: "-"
                    });
                    subprojectSelect.setValue("-"); // default value
                    subprojectSelect.enable();
                }

                subprojectSelect.refreshOptions(false);
            });
        });
    </script>

    <script>
        $('#kehadiran-select').on('change', function() {
            const selected = $(this).val();

            // Reset semua dulu
            $('#input-cuti').val(0);
            $('#input-ijin').val(0);
            $('#input-sakit').val(0);

            if (selected) {
                // Auto-set waktu jadi 0
                $('input[name="waktu"]').val(0);

                if (selected === 'cuti') {
                    $('#input-cuti').val(1);
                } else if (selected === 'ijin') {
                    $('#input-ijin').val(1);
                } else if (selected === 'sakit') {
                    $('#input-sakit').val(1);
                }
            } else {
                // Biarkan user isi waktu secara manual
                $('input[name="waktu"]').val('');
            }
        });
    </script>

    <script>
        let subprojectsGroupedByPTEdit = {}; // Data subproject dari backend

        document.addEventListener('DOMContentLoaded', function() {
            fetch('/get-subprojects')
                .then(response => response.json())
                .then(data => {
                    data.forEach(item => {
                        if (!subprojectsGroupedByPTEdit[item.nama_pt]) {
                            subprojectsGroupedByPTEdit[item.nama_pt] = [];
                        }
                        subprojectsGroupedByPTEdit[item.nama_pt].push(item.nama_sub_project);
                    });

                    // Inject script dari Blade setelah data siap
                    @if ($errors->any())
                        const oldId = @json(old('id'));
                        const oldPt = @json(old('nama_pt'));
                        const oldSubproject = @json(old('nama_sub_project'));
                        const oldTanggal = @json(old('tanggal'));
                        const oldWaktu = @json(old('waktu'));
                        const oldKeterangan = @json(old('keterangan'));

                        const ptInput = document.getElementById('edit-pt-input').tomselect;
                        const subprojectInput = document.getElementById('edit-subproject-input').tomselect;

                        document.getElementById('edit-id').value = oldId;
                        document.getElementById('edit-tanggal').value = oldTanggal;
                        document.getElementById('edit-waktu').value = oldWaktu;
                        document.getElementById('edit-keterangan').value = oldKeterangan;
                        document.getElementById('edit-activity-form').action = `/daily/${oldId}`;

                        ptInput.clearOptions();
                        ptInput.addOption({
                            value: "",
                            text: "Pilih Nama PT"
                        });
                        Object.keys(subprojectsGroupedByPTEdit).forEach(function(ptName) {
                            ptInput.addOption({
                                value: ptName,
                                text: ptName
                            });
                        });
                        ptInput.refreshOptions(false);
                        ptInput.setValue(oldPt);

                        updateSubprojects(oldPt);
                        if (subprojectsGroupedByPTEdit[oldPt] && subprojectsGroupedByPTEdit[oldPt].includes(
                                oldSubproject)) {
                            subprojectInput.setValue(oldSubproject);
                        } else {
                            subprojectInput.clear(true);
                        }

                        document.getElementById('edit-activity-modal').classList.remove('hidden');
                    @endif
                })
                .catch(error => {
                    console.error('Failed to fetch subprojects:', error);
                });

            const ptInput = new TomSelect("#edit-pt-input", {
                create: true,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                onChange: function(selectedPT) {
                    updateSubprojects(selectedPT);

                    // ⛏️ Cek & set otomatis "-" jika subproject-nya kosong/null
                    const subprojectInput = document.getElementById('edit-subproject-input').tomselect;
                    const subprojects = subprojectsGroupedByPTEdit[selectedPT];

                    const isEmpty = !subprojects || subprojects.length === 0 || subprojects.every(sp =>
                        !sp || sp === "null");

                    if (isEmpty) {
                        subprojectInput.setValue("-"); // langsung isi otomatis
                    } else {
                        subprojectInput.setValue(""); // biar user pilih manual
                    }
                }
            });

            new TomSelect("#edit-subproject-input", {
                create: true,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        });

        // Update subproject saat PT dipilih
        function updateSubprojects(selectedPT) {
            const subprojectInput = document.getElementById('edit-subproject-input').tomselect;
            subprojectInput.clearOptions();

            // Jika PT tidak punya subproject, masukkan opsi "-"
            if (!subprojectsGroupedByPTEdit[selectedPT] || subprojectsGroupedByPTEdit[selectedPT].length === 0) {
                subprojectInput.addOption({
                    value: "-",
                    text: "-"
                });
                subprojectInput.refreshOptions(false);
                subprojectInput.setValue("-");
                return;
            }

            subprojectInput.addOption({
                value: "",
                text: "Pilih Project"
            });

            subprojectsGroupedByPTEdit[selectedPT].forEach(function(sub) {
                // Handle null/undefined subproject
                const subValue = sub ?? "-";
                subprojectInput.addOption({
                    value: subValue,
                    text: subValue
                });
            });

            subprojectInput.refreshOptions(false);
            subprojectInput.setValue("");
        }

        function openEditModal(id, pt, subproject, tanggal, waktu, keterangan, cuti, ijin, sakit) {
            const ptInput = document.getElementById('edit-pt-input').tomselect;
            const subprojectInput = document.getElementById('edit-subproject-input').tomselect;

            document.getElementById('edit-activity-form').action = `/daily/${id}`;
            document.getElementById('edit-activity-modal').classList.remove('hidden');
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-tanggal').value = tanggal;
            document.getElementById('edit-waktu').value = parseFloat(waktu);

            // 🧼 Clean keterangan
            let cleanedKeterangan = keterangan
                .split(',')
                .map(item => item.trim())
                .filter(item => item !== "1" && item !== "0" && item !== "")
                .join(', ')
                .trim();
            document.getElementById('edit-keterangan').value = cleanedKeterangan;

            // Handle null subproject
            if (!subproject || subproject === "null") {
                subproject = "-";
            }

            // PT input
            ptInput.clearOptions();
            ptInput.addOption({
                value: "",
                text: "Pilih Nama PT"
            });
            Object.keys(subprojectsGroupedByPTEdit).forEach(function(ptName) {
                ptInput.addOption({
                    value: ptName,
                    text: ptName
                });
            });
            ptInput.refreshOptions(false);
            ptInput.setValue(pt);

            // Subproject input
            updateSubprojects(pt);
            if (subprojectsGroupedByPTEdit[pt] && subprojectsGroupedByPTEdit[pt].includes(subproject)) {
                subprojectInput.setValue(subproject);
            } else {
                subprojectInput.addOption({
                    value: subproject,
                    text: subproject
                });
                subprojectInput.refreshOptions(false);
                subprojectInput.setValue(subproject);
            }

            // Jenis Izin
            let izinFlags = {
                cuti,
                ijin,
                sakit
            };
            let activeIzin = Object.keys(izinFlags).filter(k => izinFlags[k] == 1);
            let jenisIzin = activeIzin.length === 1 ? activeIzin[0] : "";
            if (activeIzin.length > 1) {
                console.warn("⚠️ Data izin ganda ditemukan:", izinFlags);
            }
            document.getElementById('edit-jenis-izin').value = jenisIzin;
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
    </script>

    <script>
        function openDeleteModal(id) {
            const form = document.getElementById('delete-activity-form');
            form.action = `/daily/${id}`; // Set action form sesuai id activity
            document.getElementById('delete-activity-modal').classList.remove('hidden');
        }
    </script>

</x-app-layout>
