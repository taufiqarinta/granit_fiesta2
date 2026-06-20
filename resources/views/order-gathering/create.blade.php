<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Perhitungan Point Event Gathering') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('order-gathering.store') }}" method="POST" id="orderForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Agen</label>
                                <select name="nama_agen" id="nama_agen" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Pilih Agen</option>
                                    <option value="AGEN A - C0001">AGEN A - C0001</option>
                                    <option value="AGEN B - C0002">AGEN B - C0002</option>
                                    <option value="AGEN C - C0003">AGEN C - C0003</option>
                                </select>
                                @error('nama_agen')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Toko</label>
                                <select name="nama_toko" id="nama_toko" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Pilih Toko</option>
                                    <option value="TOKO A - T0001">TOKO A - T0001</option>
                                    <option value="TOKO B - T0002">TOKO B - T0002</option>
                                    <option value="TOKO C - T0003">TOKO C - T0003</option>
                                </select>
                                @error('nama_toko')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
                                <input type="text" name="provinsi" value="{{ old('provinsi') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @error('provinsi')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kota/Kab</label>
                                <input type="text" name="kota_kab" value="{{ old('kota_kab') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @error('kota_kab')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                <input type="text" name="alamat" value="{{ old('alamat') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @error('alamat')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Target</label>
                                <select name="target" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Pilih Target</option>
                                    <option value="Umroh - 36000 Point">Umroh - 36000 Point</option>
                                    <option value="Sepeda Motor - 18000 Point">Sepeda Motor - 18000 Point</option>
                                    <option value="Kulkas - 9000 Point">Kulkas - 9000 Point</option>
                                </select>
                                @error('target')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Order</label>
                                <input type="date" name="tanggal_order" value="{{ old('tanggal_order', date('Y-m-d')) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @error('tanggal_order')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <button type="button" onclick="addBrand()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Tambah Data
                            </button>
                        </div>

                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Brand</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Motif</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Jumlah Box</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Jumlah Point</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="brandTableBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-6 py-3 text-right font-bold border">Total Point</td>
                                        <td class="px-6 py-3 font-bold border" id="totalPoint">0</td>
                                        <td class="border"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('order-gathering.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let brandIndex = 0;

        function addBrand() {
            const tbody = document.getElementById('brandTableBody');
            const row = document.createElement('tr');
            row.id = `brand-row-${brandIndex}`;
            
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap border">
                    <input type="text" name="brands[${brandIndex}][brand]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </td>
                <td class="px-6 py-4 whitespace-nowrap border">
                    <input type="text" name="brands[${brandIndex}][motif]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </td>
                <td class="px-6 py-4 whitespace-nowrap border">
                    <input type="number" name="brands[${brandIndex}][jumlah_box]" class="jumlah-box w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0" value="0" onchange="calculatePoint(${brandIndex})" required>
                </td>
                <td class="px-6 py-4 whitespace-nowrap border">
                    <input type="number" name="brands[${brandIndex}][jumlah_point]" class="jumlah-point w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0" value="0" onchange="calculateTotal()" required>
                </td>
                <td class="px-6 py-4 whitespace-nowrap border">
                    <button type="button" onclick="removeBrand(${brandIndex})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">
                        Hapus
                    </button>
                </td>
            `;
            
            tbody.appendChild(row);
            brandIndex++;
        }

        function removeBrand(index) {
            const row = document.getElementById(`brand-row-${index}`);
            if (row) {
                row.remove();
                calculateTotal();
            }
        }

        function calculatePoint(index) {
            const jumlahBox = document.querySelector(`input[name="brands[${index}][jumlah_box]"]`).value;
            const jumlahPoint = document.querySelector(`input[name="brands[${index}][jumlah_point]"]`);
            
            // Anda bisa menambahkan logika perhitungan point berdasarkan jumlah box di sini
            // Contoh: 1 box = 100 point
            jumlahPoint.value = jumlahBox * 100;
            
            calculateTotal();
        }

        function calculateTotal() {
            const points = document.querySelectorAll('.jumlah-point');
            let total = 0;
            
            points.forEach(point => {
                total += parseInt(point.value) || 0;
            });
            
            document.getElementById('totalPoint').textContent = total;
        }

        // Add initial row on page load
        document.addEventListener('DOMContentLoaded', function() {
            addBrand();
        });
    </script>
</x-app-layout>