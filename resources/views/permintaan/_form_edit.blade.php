<form action="{{ route('permintaan.update', $order->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="w-full max-w-3xl max-h-screen p-6 my-10 overflow-y-auto bg-white rounded-lg shadow">
        <h3 class="mb-4 text-xl font-semibold">Edit Permintaan</h3>

        <!-- Container -->
        <div id="permintaan-container">
            @foreach ($order->permintaans as $index => $item)
                <div class="p-4 mb-4 border rounded-lg permintaan-item">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Tanggal -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Tanggal</label>
                            <input type="date" name="permintaans[{{ $index }}][tanggal]" value="{{ $item->tanggal }}"
                                class="w-full p-2 border rounded-lg" required>
                        </div>

                        <!-- Merk -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Merk</label>
                            <select name="permintaans[{{ $index }}][merk_id]"
                                class="w-full p-2 border rounded-lg"
                                onchange="handleMerkChange(this)">
                                <option value="">Pilih Merk</option>
                                @foreach ($merks as $merk)
                                    <option value="{{ $merk->id }}" {{ $merk->id == $item->merk_id ? 'selected' : '' }}>
                                        {{ $merk->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Ukuran -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Ukuran</label>
                            <select name="permintaans[{{ $index }}][ukuran_id]"
                                class="w-full p-2 border rounded-lg"
                                onchange="handleUkuranChange(this)">
                                <option value="">Pilih Ukuran</option>
                                @foreach ($item->ukurans as $ukuran)
                                    <option value="{{ $ukuran->id }}" {{ $ukuran->id == $item->ukuran_id ? 'selected' : '' }}>
                                        {{ $ukuran->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Motif -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Motif</label>
                            <select name="permintaans[{{ $index }}][motif]" class="w-full p-2 border rounded-lg">
                                <option value="">Pilih Motif</option>
                                @foreach ($item->motifs as $motif)
                                    <option value="{{ $motif->name }}" {{ $motif->name == $item->motif ? 'selected' : '' }}>
                                        {{ $motif->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Estimasi -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Estimasi</label>
                            <input type="number" min="1" name="permintaans[{{ $index }}][estimasi]" value="{{ $item->estimasi }}"
                                class="w-full p-2 border rounded-lg">
                        </div>

                        <!-- Prioritas -->
                        <!-- <div>
                            <label class="block mb-1 text-sm font-medium">Prioritas</label>
                            <select name="permintaans[{{ $index }}][prioritas]" class="w-full p-2 border rounded-lg">
                                <option value="">Pilih Prioritas</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                            </select>
                        </div> -->
                    </div>
                    <button type="button" onclick="removePermintaan(this)" class="mt-2 text-red-600 hover:underline">Hapus Baris Ini</button>
                </div>
            @endforeach
        </div>

        <!-- Tombol tambah -->
        <button type="button" onclick="addPermintaan()" class="mb-4 text-blue-600 hover:underline">+ Tambah Baris Permintaan</button>

        <!-- Tombol aksi -->
        <div class="flex justify-end space-x-2">
            <button type="button" onclick="document.getElementById('edit-permintaan-modal').classList.add('hidden')"
                class="px-4 py-2 bg-gray-300 rounded">Batal</button>
            <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded">Simpan</button>
        </div>
    </div>
</form>

<!-- Template hidden -->
<template id="permintaan-template">
    <div class="p-4 mb-4 border rounded-lg permintaan-item">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 text-sm font-medium">Tanggal</label>
                <input type="date" name="__name__[tanggal]" class="w-full p-2 border rounded-lg" required>
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium">Merk</label>
                <select name="__name__[merk_id]" class="w-full p-2 border rounded-lg" onchange="handleMerkChange(this)">
                    <option value="">Pilih Merk</option>
                    @foreach ($merks as $merk)
                        <option value="{{ $merk->id }}">{{ $merk->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium">Ukuran</label>
                <select name="__name__[ukuran_id]" class="w-full p-2 border rounded-lg" onchange="handleUkuranChange(this)">
                    <option value="">Pilih Ukuran</option>
                </select>
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium">Motif</label>
                <select name="__name__[motif]" class="w-full p-2 border rounded-lg">
                    <option value="">Pilih Motif</option>
                </select>
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium">Estimasi</label>
                <input type="number" min="1" name="__name__[estimasi]" class="w-full p-2 border rounded-lg">
            </div>
        </div>
        <button type="button" onclick="removePermintaan(this)" class="mt-2 text-red-600 hover:underline">Hapus Baris Ini</button>
    </div>
</template>

<!-- Script vanilla JS -->
<script>
    let permintaanIndex = {{ count($order->permintaans) }};

    function addPermintaan() {
        let template = document.getElementById('permintaan-template').innerHTML;
        template = template.replaceAll('__name__', 'permintaans['+permintaanIndex+']');

        let container = document.createElement('div');
        container.innerHTML = template.trim();
        document.getElementById('permintaan-container').appendChild(container.firstChild);

        permintaanIndex++;
    }

    function removePermintaan(button) {
        button.closest('.permintaan-item').remove();
    }

    function handleMerkChange(select) {
        let merkId = select.value;
        let grid = select.closest('.grid');
        let ukuranSelect = grid.querySelector('select[name*="[ukuran_id]"]');
        let motifSelect = grid.querySelector('select[name*="[motif]"]');

        fetch(`/get-ukurans/${merkId}`)
            .then(res => res.json())
            .then(data => {
                ukuranSelect.innerHTML = '<option value="">Pilih Ukuran</option>';
                data.forEach(item => {
                    ukuranSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                });
                motifSelect.innerHTML = '<option value="">Pilih Motif</option>';
            });
    }

    function handleUkuranChange(select) {
        let ukuranId = select.value;
        let grid = select.closest('.grid');
        let motifSelect = grid.querySelector('select[name*="[motif]"]');

        fetch(`/get-motifs/${ukuranId}`)
            .then(res => res.json())
            .then(data => {
                motifSelect.innerHTML = '<option value="">Pilih Motif</option>';
                data.forEach(item => {
                    motifSelect.innerHTML += `<option value="${item.name}">${item.name}</option>`;
                });
            });
    }
</script>
