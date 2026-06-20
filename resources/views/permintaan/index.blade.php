<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Forecast Order') }}
        </h2>
    </x-slot>

    <div class="px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-2 mb-4 text-sm text-white bg-green-500 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-2 mb-4 text-sm text-white bg-red-500 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col mb-4 space-y-2 md:flex-row md:items-center md:justify-between md:space-y-0">
                <!-- Kiri: Tombol Tambah -->
                <button onclick="document.getElementById('add-permintaan-modal').classList.remove('hidden')"
                    class="w-full md:w-auto text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Tambah Forecast Order
                </button>

                <!-- Kanan: Form Filter -->
                <form method="GET" action="{{ route('permintaan') }}" class="w-full md:w-auto">
                    <div class="flex flex-col space-y-2 md:flex-row md:items-end md:space-x-2 md:space-y-0">
                        <div class="w-full md:w-auto">
                            <label class="block mb-1 text-sm">Tanggal Awal</label>
                            <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}"
                                class="w-full p-2 border rounded md:w-auto">
                        </div>
                        <div class="w-full md:w-auto">
                            <label class="block mb-1 text-sm">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                                class="w-full p-2 border rounded md:w-auto">
                        </div>
                        <div class="flex space-x-2">
                            <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded">Filter</button>
                            <a href="{{ route('permintaan') }}" class="px-4 py-2 bg-gray-300 rounded">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="w-full">
                <table class="w-full text-xs text-gray-700 table-fixed">
                    <thead class="bg-gray-50">
                        <tr class="text-center">
                            <th class="w-6 p-2">No</th>
                            <th class="p-2">Kode Order</th>
                            <th class="p-2">Customer</th>
                            <th class="p-2">Cabang</th>
                            <th class="p-2">Tanggal</th>
                            <th class="p-2">Forecast Period</th>
                            <th class="p-2">Status</th>
                            <th class="p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="p-2">{{ $loop->iteration }}</td>
                                <td class="p-2">{{ $order->kode }}</td>
                                <td class="p-2">{{ $order->user->name }}</td>
                                <td class="p-2">{{ $order->cabang->nama_cabang ?? '-' }}</td>
                                <td class="p-2">{{ \Carbon\Carbon::parse($order->tanggal)->format('d-m-Y') }}</td>
                                {{-- <td class="p-2">{{ $order->forecast_period }}</td> --}}
                                <td class="p-2">{{ $order->forecast }}</td>
                                <td class="p-2">
                                    @if ($order->status == 0)
                                        <span class="px-2 py-0.5 text-xs text-red-600 bg-red-100 rounded">Pending</span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 text-xs text-green-600 bg-green-100 rounded">Confirm</span>
                                    @endif
                                </td>
                                <td class="p-2">
                                    <div class="flex justify-center gap-1">
                                        <button onclick="downloadPDF({{ $order->id }})"
                                            class="text-red-600 hover:text-red-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <!-- Icon PDF (contoh icon file) -->
                                                <path
                                                    d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6zM13 9V3.5L18.5 9H13z" />
                                            </svg>
                                        </button>
                                        <button onclick="viewOrder({{ $order->id }})"
                                            class="text-blue-600 hover:text-blue-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <!-- Eye Icon -->
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button @if ($order->status == 1) disabled @endif
                                            onclick="editOrder({{ $order->id }})"
                                            class="{{ $order->status == 1 ? 'text-gray-400 cursor-not-allowed' : 'text-blue-600 hover:text-blue-800' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <!-- Edit Path -->
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5m-5-11l5 5M18 2l4 4m-2 2L9 19H4v-5L16 4z" />
                                            </svg>
                                        </button>
                                        <button @if ($order->status == 1) disabled @endif
                                            onclick="deleteOrder({{ $order->id }})"
                                            class="{{ $order->status == 1 ? 'text-gray-400 cursor-not-allowed' : 'text-red-600 hover:text-red-800' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <!-- Trash Path -->
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4m16 0h-4" />
                                            </svg>
                                        </button>
                                        <button onclick="sendWA({{ $order->id }})"
                                            @if ($order->status == 1) disabled @endif
                                            class="{{ $order->status == 1 ? 'text-gray-400 cursor-not-allowed' : 'text-green-600 hover:text-green-800' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                viewBox="0 0 32 32" fill="currentColor">
                                                <!-- Icon WhatsApp simple -->
                                                <path
                                                    d="M16 2C8.3 2 2 8.3 2 16c0 2.8.8 5.5 2.3 7.9L2 30l6.2-2.2C10.5 29.2 13.2 30 16 30c7.7 0 14-6.3 14-14S23.7 2 16 2zm0 26c-2.4 0-4.7-.7-6.7-1.9l-.5-.3-3.7 1.3 1.2-3.6-.4-.6C4.7 20.9 4 18.5 4 16c0-6.6 5.4-12 12-12s12 5.4 12 12-5.4 12-12 12zm6.5-8c-.4-.2-2.4-1.2-2.8-1.4-.4-.2-.7-.2-1 .2-.3.4-1.1 1.4-1.3 1.7-.2.3-.5.4-.9.2-.4-.2-1.7-.6-3.2-2-1.2-1.1-2-2.4-2.3-2.8-.2-.4 0-.7.2-.9.2-.2.4-.5.6-.8.2-.2.3-.4.5-.7.2-.2.1-.5 0-.7-.2-.2-1-2.5-1.4-3.4-.3-.7-.7-.6-1-.6h-.9c-.3 0-.7.1-1 .4s-1.3 1.2-1.3 3c0 1.8 1.3 3.5 1.5 3.7.2.2 2.6 4 6.3 5.6.9.4 1.6.6 2.2.8.9.3 1.7.3 2.3.2.7-.1 2.4-1 2.7-1.9.3-.8.3-1.5.2-1.9-.1-.3-.4-.4-.8-.6z" />
                                            </svg>
                                        </button>
                                        @php
                                            $adminEmail = 'teddy.tancorp@gmail.com';
                                            $userName = auth()->user()->name;
                                            $userEmail = auth()->user()->email;
                                            $subject = 'Permintaan Order Baru dari Pelanggan';
                                            $body =
                                                "Halo Admin, Ada permintaan order baru dari *$userName* dengan Email: $userEmail, Kode Order: $order->kode Tanggal Order: " .
                                                \Carbon\Carbon::parse($order->tanggal)->translatedFormat('d F Y') .
                                                'Detail Permintaan:';

                                            foreach ($order->permintaans as $p) {
                                                $body .=
                                                    '• Merk: ' .
                                                    ($p->merk->name ?? '-') .
                                                    ', Ukuran: ' .
                                                    ($p->ukuran->name ?? '-') .
                                                    ', Motif: ' .
                                                    ($p->motif ?? '-') .
                                                    ', Estimasi: ' .
                                                    ($p->estimasi ?? '-') .
                                                    '';
                                            }

                                            $body .= ' Terima kasih.';

                                            $gmailUrl =
                                                "https://mail.google.com/mail/?view=cm&fs=1&to=$adminEmail&su=" .
                                                urlencode($subject) .
                                                '&body=' .
                                                urlencode($body);
                                        @endphp

                                        <a href="{{ $gmailUrl }}" target="_blank"
                                            class="{{ $order->status == 1 ? 'text-gray-400 cursor-not-allowed pointer-events-none' : 'text-red-600 hover:text-red-800' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M2.25 4.5A2.25 2.25 0 0 1 4.5 2.25h15a2.25 2.25 0 0 1 2.25 2.25v15a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 2.25 19.5v-15ZM4.5 5.318v13.364h15V5.318l-7.5 4.909L4.5 5.318Zm1.36-.818h12.28L12 9.023 5.86 4.5Z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $orders->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="add-permintaan-modal"
        class="fixed inset-0 z-50 flex items-start justify-center {{ $errors->any() ? '' : 'hidden' }} overflow-y-auto bg-gray-900 bg-opacity-50">
        <div class="w-full max-w-3xl max-h-screen p-6 my-10 overflow-y-auto bg-white rounded-lg shadow"
            x-data="{ permintaans: [{}], showConfirmModal: false }">
            <h3 class="mb-4 text-xl font-semibold">Tambah Forecast Order</h3>
            <form action="{{ route('permintaan.store') }}" method="POST">
                @csrf

                <!-- Cabang -->
                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Cabang</label>
                    <select name="id_cabang"
                        class="w-full p-2 border rounded-lg" required>
                        <option value="">Pilih Cabang</option>
                        @foreach ($cabangs as $cabang)
                            <option value="{{ $cabang->id_cabang }}">{{ $cabang->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>


                <template x-for="(item, index) in permintaans" :key="index">
                    <div class="p-4 mb-4 border rounded-lg">
                        <div class="grid grid-cols-2 gap-4">

                            <!-- Merk -->
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Merk</label>
                                <select :name="'permintaans[' + index + '][merk_id]'"
                                    x-on:change="fetchUkurans(index, $event.target.value)"
                                    class="w-full p-2 border rounded-lg" required>
                                    <option value="">Pilih Merk</option>
                                    @foreach ($merks as $merk)
                                        <option value="{{ $merk->id }}">{{ $merk->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Ukuran -->
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Ukuran</label>
                                <select :name="'permintaans[' + index + '][ukuran_id]'"
                                    x-on:change="fetchMotifs(index, $event.target.value)" :id="'ukuran-' + index"
                                    class="w-full p-2 border rounded-lg" required>
                                    <option value="">Pilih Ukuran</option>
                                </select>
                            </div>

                            <!-- Motif -->
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Motif</label>
                                <select :name="'permintaans[' + index + '][motif]'" :id="'motif-' + index"
                                    class="w-full p-2 border rounded-lg" required>
                                    <option value="">Pilih Motif</option>
                                </select>
                            </div>

                            <!-- Estimasi -->
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Estimasi</label>
                                <input type="number" min="1" :name="'permintaans[' + index + '][estimasi]'"
                                    class="w-full p-2 border rounded-lg" required>
                                @error('permintaans.*.estimasi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Prioritas -->
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Prioritas</label>
                                <select :name="'permintaans[' + index + '][prioritas]'" 
                                        :id="'prioritas-' + index"
                                        class="w-full p-2 border rounded-lg"
                                        :value="3" required>
                                    @foreach($daftarPrioritas as $prioritas)
                                        <option value="{{ $prioritas->id_prioritas }}">
                                            {{ $prioritas->id_prioritas }} - {{ $prioritas->nama_prioritas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <button type="button" class="mt-2 text-red-600 hover:underline"
                            x-show="permintaans.length > 1" @click="permintaans.splice(index, 1)">
                            Hapus Baris Ini
                        </button>
                    </div>
                </template>

                <button type="button" class="mb-4 text-blue-600 hover:underline" @click="permintaans.push({})">
                    + Tambah Baris Permintaan
                </button>

                <div class="flex justify-end space-x-2">
                    <button type="button"
                        onclick="document.getElementById('add-permintaan-modal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                    <button type="button" @click="showConfirmModal = true"
                        class="px-4 py-2 text-white bg-blue-600 rounded">
                        Final Order
                    </button>
                </div>
            </form>
            <!-- Modal Konfirmasi -->
            <div x-show="showConfirmModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                    <h2 class="mb-4 text-lg font-semibold">Konfirmasi</h2>
                    <p>Apakah permintaan yang anda inputkan sudah sesuai?</p>
                    <div class="flex justify-end mt-6 space-x-2">
                        <button @click="showConfirmModal = false" class="px-4 py-2 bg-gray-300 rounded">
                            Batal
                        </button>
                        <button @click="document.querySelector('#add-permintaan-modal form').submit()"
                            class="px-4 py-2 text-white bg-blue-600 rounded">
                            Ya, Kirim
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal View -->
    <div id="view-order-modal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden overflow-y-auto bg-gray-900 bg-opacity-50">
        <div
            class="w-full max-w-md md:max-w-xl lg:max-w-2xl max-h-[90vh] p-6 my-10 overflow-y-auto bg-white rounded-lg shadow">
            <h3 class="mb-4 text-xl font-semibold">Detail Order</h3>
            <div id="view-order-content" class="w-full">
                <!-- Tabel akan dimuat di sini -->
            </div>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="document.getElementById('view-order-modal').classList.add('hidden')"
                    class="px-4 py-2 bg-gray-300 rounded">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="edit-permintaan-modal"
        class="fixed inset-0 z-50 flex items-start justify-center hidden overflow-y-auto bg-gray-900 bg-opacity-50">
        <div class="w-full max-w-3xl max-h-screen p-6 my-10 overflow-y-auto bg-white rounded-lg shadow">
            <h3 class="mb-4 text-xl font-semibold">Edit Permintaan</h3>
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')

                <div id="edit-permintaan-list">
                    <!-- JS akan render baris di sini -->
                </div>

                <button type="button" id="add-edit-baris" class="mb-4 text-blue-600 hover:underline">
                    + Tambah Baris Permintaan
                </button>

                <div class="flex justify-end space-x-2">
                    <button type="button"
                        onclick="document.getElementById('edit-permintaan-modal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="delete-order-modal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
            <p class="mb-6 text-sm text-gray-700">Apakah Anda yakin ingin menghapus order ini?</p>
            <form id="delete-order-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">Hapus</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Script Cascade Merk -> Ukuran -> Motif -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function fetchUkurans(index, merkId) {
            fetch(`/get-ukurans/${merkId}`)
                .then(res => res.json())
                .then(data => {
                    let ukuranSelect = document.getElementById('ukuran-' + index);
                    ukuranSelect.innerHTML = '<option value="">Pilih Ukuran</option>';
                    data.forEach(item => {
                        ukuranSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });

                    let motifSelect = document.getElementById('motif-' + index);
                    motifSelect.innerHTML = '<option value="">Pilih Motif</option>';
                });
        }

        function fetchMotifs(index, ukuranId) {
            fetch(`/get-motifs/${ukuranId}`)
                .then(res => res.json())
                .then(data => {
                    let motifSelect = document.getElementById('motif-' + index);
                    motifSelect.innerHTML = '<option value="">Pilih Motif</option>';
                    data.forEach(item => {
                        motifSelect.innerHTML += `<option value="${item.name}">${item.name}</option>`;
                    });
                });
        }
    </script>
    <script>
        function editOrder(orderId) {
            // 1. Buka modal
            document.getElementById('edit-permintaan-modal').classList.remove('hidden');

            // 2. Reset container
            let listContainer = document.getElementById('edit-permintaan-list');
            listContainer.innerHTML = '';

            // 3. Ganti action form update
            let form = document.getElementById('edit-form');
            form.action = `/permintaan/${orderId}/update`;

            // 4. Fetch data permintaans
            fetch(`/permintaan/${orderId}/edit`)
                .then(res => res.json())
                .then(data => {
                    console.log(data);
                    data.permintaans.forEach((item, index) => {
                        addEditBaris(item, index);
                    });
                });

            // 5. Tambah baris baru jika klik + baris
            document.getElementById('add-edit-baris').onclick = function() {
                let index = document.querySelectorAll('.edit-permintaan-item').length;
                addEditBaris({}, index);
            };
        }

        function addEditBaris(item, index) {
            let listContainer = document.getElementById('edit-permintaan-list');

            let div = document.createElement('div');
            div.className = 'p-4 mb-4 border rounded-lg edit-permintaan-item';

            div.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                     <input type="hidden" name="permintaans[${index}][id]" value="${item.id ?? ''}">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Merk</label>
                        <select name="permintaans[${index}][merk_id]"
                            onchange="fetchUkuransEdit(${index}, this.value)"
                            class="w-full p-2 border rounded-lg" required>
                            <option value="">Pilih Merk</option>
                            @foreach ($merks as $merk)
                                <option value="{{ $merk->id }}">{{ $merk->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Ukuran</label>
                        <select name="permintaans[${index}][ukuran_id]"
                            id="ukuran-edit-${index}"
                            onchange="fetchMotifsEdit(${index}, this.value)"
                            class="w-full p-2 border rounded-lg" required>
                            <option value="">Pilih Ukuran</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Motif</label>
                        <select name="permintaans[${index}][motif]"
                            id="motif-edit-${index}"
                            class="w-full p-2 border rounded-lg" required>
                            <option value="">Pilih Motif</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Estimasi</label>
                        <input type="number" min="1"
                            name="permintaans[${index}][estimasi]"
                            value="${item.estimasi ?? ''}"
                            class="w-full p-2 border rounded-lg" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Prioritas</label>
                        <select name="permintaans[${index}][prioritas]"
                            id="prioritas-edit-${index}"
                            class="w-full p-2 border rounded-lg" required>
                            <option value="">Pilih Prioritas</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>

                    </div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="mt-2 text-red-600 hover:underline">
                    Hapus Baris Ini
                </button>
            `;

            listContainer.appendChild(div);

            // Preselect Merk, fetch Ukurans + Motifs jika ada data
            if (item.merk_id) {
                div.querySelector(`select[name="permintaans[${index}][merk_id]"]`).value = item.merk_id;
                fetchUkuransEdit(index, item.merk_id, item.ukuran_id, item.motif);
            }

            if (item.prioritas) {
                div.querySelector(`select[name="permintaans[${index}][prioritas]"]`).value = item.prioritas;
            }
        }

        function fetchUkuransEdit(index, merkId, selectedUkuranId = null, selectedMotif = null) {
            fetch(`/get-ukurans/${merkId}`)
                .then(res => res.json())
                .then(data => {
                    let ukuranSelect = document.getElementById('ukuran-edit-' + index);
                    ukuranSelect.innerHTML = '<option value="">Pilih Ukuran</option>';
                    data.forEach(item => {
                        ukuranSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });
                    if (selectedUkuranId) {
                        ukuranSelect.value = selectedUkuranId;
                        fetchMotifsEdit(index, selectedUkuranId, selectedMotif);
                    }
                });
        }

        function fetchMotifsEdit(index, ukuranId, selectedMotif = null) {
            fetch(`/get-motifs/${ukuranId}`)
                .then(res => res.json())
                .then(data => {
                    let motifSelect = document.getElementById('motif-edit-' + index);
                    motifSelect.innerHTML = '<option value="">Pilih Motif</option>';
                    data.forEach(item => {
                        motifSelect.innerHTML += `<option value="${item.name}">${item.name}</option>`;
                    });
                    if (selectedMotif) {
                        motifSelect.value = selectedMotif;
                    }
                });
        }
    </script>
    <script>
        function deleteOrder(orderId) {
            // Buka modal
            document.getElementById('delete-order-modal').classList.remove('hidden');
            // Pasang action form
            const form = document.getElementById('delete-order-form');
            form.action = `/permintaan/${orderId}/delete`;
        }

        function closeDeleteModal() {
            document.getElementById('delete-order-modal').classList.add('hidden');
        }
    </script>

    <script>
        function viewOrder(orderId) {
            // Buka modal
            document.getElementById('view-order-modal').classList.remove('hidden');

            // Kosongkan konten
            const container = document.getElementById('view-order-content');
            container.innerHTML = 'Memuat data...';

            // Fetch data
            fetch(`/permintaan/${orderId}/view`)
                .then(res => res.json())
                .then(order => {
                    if (order.permintaans.length === 0) {
                        container.innerHTML = '<p class="text-sm text-gray-500">Tidak ada permintaan.</p>';
                        return;
                    }

                    let totalQty = 0;

                    let tableHtml = `
              <table class="w-full border border-collapse table-fixed">
    <thead>
      <tr>
        <th class="px-4 py-2 text-left border whitespace-nowrap">Merk</th>
        <th class="px-4 py-2 text-left border">Motif</th>
        <th class="px-4 py-2 text-left border">Ukuran</th>
        <th class="px-4 py-2 text-right border">Prioritas</th>
        <th class="px-4 py-2 text-right border">Qty</th>
      </tr>
    </thead>
    <tbody>
          `;

                    order.permintaans.forEach(item => {
                        tableHtml += `
              <tr>
                <td class="px-4 py-2 border">${item.merk?.name || '-'}</td>
                <td class="px-4 py-2 border">${item.motif || '-'}</td>
                <td class="px-4 py-2 border">${item.ukuran?.name || '-'}</td>
                <td class="px-4 py-2 text-right border">
                    ${item.prioritas ? item.prioritas.id_prioritas + ' - ' + item.prioritas.nama_prioritas : '-'}
                </td>
                <td class="px-4 py-2 text-right border">${item.estimasi || 0}</td>

              </tr>
            `;
                        totalQty += parseInt(item.estimasi) || 0;
                    });

                    tableHtml += `
              <tr>
                <td class="px-4 py-2 font-semibold border" colspan="4">Total</td>
                <td class="px-4 py-2 font-semibold text-right border">${totalQty}</td>
              </tr>
            </tbody>
            </table>
          `;

                    container.innerHTML = tableHtml;
                })
                .catch(() => {
                    container.innerHTML = '<p class="text-sm text-red-500">Gagal memuat data.</p>';
                });
        }
    </script>

    <script>
        function downloadPDF(orderId) {
            window.open(`/permintaan/${orderId}/pdf`, '_blank');
        }
    </script>

    <script>
        function sendWA(orderId) {
            fetch(`/send-wa/${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.url) {
                        window.open(data.url, '_blank');
                    } else {
                        alert(data.error || 'Gagal generate link WA.');
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan.');
                    console.error(error);
                });
        }
    </script>

    {{-- <script>
        function sendEmail(orderId) {
            fetch(`/send-email/${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Email berhasil dikirim!');
                    } else {
                        alert(data.error || 'Gagal mengirim email.');
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan.');
                    console.error(error);
                });
        }
    </script> --}}

</x-app-layout>
