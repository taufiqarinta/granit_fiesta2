<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Form Order Granit') }}
        </h2>
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Logo dan Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center">
                            <div class="w-48">
                                <img src="/images/kobin-logo.png" alt="Kobin Tiles" class="h-16" onerror="this.style.display='none'">
                            </div>
                        </div>
                        <div class="text-right">
                            <h3 class="text-xl font-bold">Form Order Granit</h3>
                            <p class="text-gray-600">{{ date('d F Y') }}</p>
                        </div>
                    </div>

                    <form action="{{ route('form-order.store') }}" method="POST" id="formOrder">
                        @csrf

                        <!-- Informasi Toko dan Agen -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div class="space-y-4">
                                @if($user->department == 'SLS')
                                    <!-- Tampilkan dropdown agen untuk department SLS -->
                                    <div class="mb-4">
                                        <label for="nama_agen" class="block text-gray-700 text-sm font-bold mb-2">NAMA AGEN:</label>
                                        <select name="nama_agen" id="nama_agen" 
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline select2-agen @error('nama_agen') border-red-500 @enderror" required>
                                            <option value="">Pilih Agen</option>
                                            @foreach($daftarAgen ?? [] as $agenItem)
                                                <option value="{{ $agenItem->id }}" {{ old('nama_agen') == $agenItem->id ? 'selected' : '' }}>
                                                    {{ $agenItem->nama_agen }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('nama_agen')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @else
                                    <!-- Tampilkan input readonly untuk non-SLS -->
                                    <div class="mb-4">
                                        <label for="nama_agen" class="block text-gray-700 text-sm font-bold mb-2">NAMA AGEN: <span class="text-red-500">*</span></label>
                                        <input type="text" name="nama_agen" id="nama_agen" 
                                            value="{{ $agen->nama_agen ?? auth()->user()->name }}" 
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline"
                                            readonly>
                                        <input type="hidden" name="nama_agen_id" value="{{ $agen->id ?? '' }}">
                                    </div>
                                @endif

                                <div class="mb-4">
                                    <label for="nama_toko" class="block text-gray-700 text-sm font-bold mb-2">NAMA TOKO: <span class="text-red-500">*</span></label>
                                    <select name="nama_toko" id="nama_toko" 
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline select2-toko @error('nama_toko') border-red-500 @enderror" required>
                                        <option value="">Pilih Toko</option>
                                        @foreach($daftarTokoUnique ?? [] as $toko)
                                            <option value="{{ $toko->id }}" 
                                                    data-kota="{{ $toko->kota }}"
                                                    data-pic="{{ $toko->pic }}"
                                                    data-nomor-pic="{{ $toko->nomor_pic }}"
                                                    data-nama-sales="{{ $toko->nama_sales }}"
                                                    data-lokasi-event="{{ $toko->lokasi_event }}"
                                                    {{ old('nama_toko') == $toko->id ? 'selected' : '' }}>
                                                {{ $toko->nama_toko }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('nama_toko')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="brand" class="block text-gray-700 text-sm font-bold mb-2">BRAND: <span class="text-red-500">*</span></label>
                                    @if($user->department == 'SLS')
                                        <!-- Untuk SLS, brand akan diisi via JavaScript berdasarkan agen yang dipilih -->
                                        <input type="text" name="brand" id="brand" value="{{ old('brand') }}" 
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline"
                                            readonly placeholder="Pilih agen terlebih dahulu">
                                    @else
                                        <!-- Untuk non-SLS, brand langsung diisi -->
                                        <input type="text" name="brand" id="brand" 
                                            value="{{ implode(', ', $brands) }}" 
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline"
                                            readonly>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="mb-4">
                                    <label for="nama_sales" class="block text-gray-700 text-sm font-bold mb-2">NAMA SALES:</label>
                                    <input type="text" name="nama_sales" id="nama_sales" value="{{ old('nama_sales') }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nama_sales') border-red-500 @enderror"
                                        style="text-transform: uppercase;"
                                        oninput="this.value = this.value.toUpperCase();"
                                        placeholder="Nama Sales akan terisi otomatis" readonly>
                                    @error('nama_sales')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="lokasi_event" class="block text-gray-700 text-sm font-bold mb-2">LOKASI EVENT: <span class="text-red-500">*</span></label>
                                    <input type="text" name="lokasi_event" id="lokasi_event" value="{{ old('lokasi_event') }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('lokasi_event') border-red-500 @enderror"
                                        style="text-transform: uppercase;"
                                        oninput="this.value = this.value.toUpperCase();"
                                        placeholder="Lokasi Event akan terisi otomatis" readonly>
                                    @error('lokasi_event')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="pic" class="block text-gray-700 text-sm font-bold mb-2">PIC : <span class="text-red-500">*</span></label>
                                    <input type="text" name="pic" id="pic" value="{{ old('pic') }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('pic') border-red-500 @enderror"
                                        placeholder="Masukkan PIC" readonly>
                                    @error('pic')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="no_hp" class="block text-gray-700 text-sm font-bold mb-2">NO.HP: <span class="text-red-500">*</span></label>
                                    <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('no_hp') border-red-500 @enderror"
                                        placeholder="Masukkan No. HP" readonly>
                                    @error('no_hp')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="kota" class="block text-gray-700 text-sm font-bold mb-2">KOTA/KAB: <span class="text-red-500">*</span></label>
                                    <input type="text" name="kota" id="kota" value="{{ old('kota') }}" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('kota') border-red-500 @enderror"
                                        placeholder="Masukkan kota" readonly>
                                    @error('kota')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal Order (Hidden) -->
                        <input type="hidden" name="tanggal_order" value="{{ date('Y-m-d') }}">

                        <br>
                        <br>

                        <!-- Tabel Purchase Order -->
                        <div class="overflow-x-auto mb-6 -mx-4 sm:mx-0">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden">
                                    <table class="min-w-full border-2 border-gray-800">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-2 sm:px-4 py-3 text-center text-xs sm:text-sm font-bold text-gray-900 uppercase border-2 border-gray-800">PAKET</th>
                                                <th class="px-2 sm:px-4 py-3 text-center text-xs sm:text-sm font-bold text-gray-900 uppercase border-2 border-gray-800 whitespace-nowrap">MIN POINT</th>
                                                <th class="px-2 sm:px-4 py-3 text-center text-xs sm:text-sm font-bold text-gray-900 uppercase border-2 border-gray-800 whitespace-nowrap">JML PAKET</th>
                                                <th class="px-2 sm:px-4 py-3 text-center text-xs sm:text-sm font-bold text-gray-900 uppercase border-2 border-gray-800 whitespace-nowrap">TOTAL POINT</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-800">
                                            @foreach($masterTargets as $index => $target)
                                                <tr>
                                                    <td class="px-2 sm:px-4 py-4 border-2 border-gray-800 font-medium text-gray-900 text-sm">
                                                        {{ strtoupper($target->target) }}
                                                        <input type="hidden" name="targets[{{ $index }}][master_target_id]" value="{{ $target->id }}">
                                                    </td>
                                                    
                                                    <td class="px-2 sm:px-4 py-4 text-center border-2 border-gray-800 font-bold text-sm whitespace-nowrap">
                                                        <span id="point_{{ $target->id }}">{{ number_format($target->point, 0, ',', '.') }}</span>
                                                    </td>
                                                    
                                                    <td class="px-2 sm:px-4 py-4 border-2 border-gray-800">
                                                        <input type="number" 
                                                            name="targets[{{ $index }}][jumlah_pengambilan]" 
                                                            id="jumlah_{{ $target->id }}"
                                                            class="w-full text-center border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-2 text-sm transition-all duration-200" 
                                                            min="0" 
                                                            value="{{ old('targets.' . $index . '.jumlah_pengambilan', 0) }}"
                                                            data-point="{{ $target->point }}"
                                                            oninput="calculateRowTotal({{ $target->id }})"
                                                            onkeyup="calculateRowTotal({{ $target->id }})"
                                                            onchange="calculateRowTotal({{ $target->id }})">
                                                    </td>
                                                    <td class="px-2 sm:px-4 py-4 text-center border-2 border-gray-800 font-bold whitespace-nowrap">
                                                        <span id="total_{{ $target->id }}" class="text-blue-600 text-sm sm:text-lg transition-all duration-300">0</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td colspan="3" class="px-2 sm:px-4 py-4 text-right font-bold text-sm sm:text-lg border-2 border-gray-800">
                                                    TOTAL POINT
                                                </td>
                                                <td class="px-2 sm:px-4 py-4 text-center font-bold border-2 border-gray-800 whitespace-nowrap">
                                                    <span id="grandTotal" class="text-green-600 text-base sm:text-xl transition-all duration-500">0</span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('form-order.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded transition duration-200">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded transition duration-200">
                                Simpan Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Data toko dari PHP dengan informasi lengkap
            const tokoData = {
                @foreach($daftarTokoUnique ?? [] as $toko)
                    "{{ $toko->id }}": {
                        pic: "{{ $toko->pic }}",
                        nomor_pic: "{{ $toko->nomor_pic }}",
                        nama_sales: "{{ $toko->nama_sales }}",
                        lokasi_event: "{{ $toko->lokasi_event }}",
                        kode_kota: "{{ $toko->kota }}",
                        nama_toko: "{{ $toko->nama_toko }}",
                        kota: "{{ $toko->kota }}"
                    },
                @endforeach
            };

            @if($user->department == 'SLS')
            // Data agen brand dari PHP (multiple brands) untuk department SLS
            const agenBrandData = {
                @foreach($daftarAgen ?? [] as $agenItem)
                    "{{ $agenItem->id }}": {
                        brands: [
                            @foreach($agenItem->brand_names ?? [] as $brandName)
                                "{{ $brandName }}",
                            @endforeach
                        ],
                        nama_agen: "{{ $agenItem->nama_agen }}"
                    },
                @endforeach
            };

            // Event ketika agen dipilih (hanya untuk SLS)
            $('#nama_agen').on('change', function() {
                const agenId = $(this).val();
                
                // Reset hanya brand field saja
                $('#brand').val('');
                
                if (agenId && agenBrandData[agenId]) {
                    // Update brand
                    const agenData = agenBrandData[agenId];
                    const brands = agenData.brands;
                    const brandText = brands.filter(brand => brand !== '').join(', ');
                    $('#brand').val(brandText);
                    
                    // Check for duplicate when toko is already selected
                    const tokoId = $('#nama_toko').val();
                    if (tokoId) {
                        checkDuplicateOrder(agenId, tokoId);
                    }
                } else {
                    $('#brand').val('');
                }
            });
            @endif

            // Event ketika toko dipilih
            $('#nama_toko').on('change', function() {
                const tokoId = $(this).val();
                const selectedOption = $(this).find('option:selected');
                
                // Reset fields yang terkait toko
                $('#pic').val('');
                $('#no_hp').val('');
                $('#kota').val('');
                $('#lokasi_event').val('');
                $('#nama_sales').val('');
                
                if (tokoId && tokoData[tokoId]) {
                    const data = tokoData[tokoId];
                    $('#pic').val(data.pic || '');
                    $('#no_hp').val(data.nomor_pic || '');
                    $('#nama_sales').val(data.nama_sales || '');
                    $('#lokasi_event').val(data.lokasi_event || '');
                    
                    // Load kota via AJAX
                    loadKotaByKode(data.kode_kota);
                    
                    // Check for duplicate
                    @if($user->department == 'SLS')
                    const agenId = $('#nama_agen').val();
                    if (agenId) {
                        checkDuplicateOrder(agenId, tokoId);
                    }
                    @else
                    // Untuk non-SLS, langsung check dengan agen yang login
                    checkDuplicateOrder('{{ $agen->id ?? "" }}', tokoId);
                    @endif
                }
            });

            // Function untuk check duplicate order
            function checkDuplicateOrder(agenId, tokoId) {
                if (!agenId || !tokoId) return;
                
                @if($user->department == 'SLS')
                const agenData = agenBrandData[agenId];
                @else
                const agenData = { nama_agen: '{{ $agen->nama_agen ?? "" }}' };
                @endif
                
                const tokoDataItem = tokoData[tokoId];
                
                if (!agenData || !tokoDataItem) return;
                
                // Check if combination already exists
                $.ajax({
                    url: '{{ route("check-duplicate-order") }}',
                    type: 'GET',
                    data: {
                        nama_agen: agenData.nama_agen,
                        nama_toko: tokoDataItem.nama_toko
                    },
                    success: function(response) {
                        if (response.exists) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Data Sudah Ada!',
                                html: `Form order untuk toko <strong>${tokoDataItem.nama_toko}</strong> pada agen <strong>${agenData.nama_agen}</strong> sudah terdaftar!`,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function() {
                        console.error('Error checking duplicate order');
                    }
                });
            }

            // Function untuk load kota by kode
            function loadKotaByKode(kodeKota) {
                if (!kodeKota) {
                    $('#kota').val('');
                    return;
                }
                
                $('#kota').val('Loading...');
                
                $.ajax({
                    url: '{{ route("get-kota-by-kode") }}',
                    type: 'GET',
                    data: {
                        kode: kodeKota
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#kota').val(response.nama_kota);
                        } else {
                            $('#kota').val('Kota tidak ditemukan');
                        }
                    },
                    error: function() {
                        $('#kota').val('Error loading kota');
                    }
                });
            }

            // Trigger change event jika ada nilai old (setelah validasi error)
            @if(old('nama_toko'))
                $('#nama_toko').val('{{ old('nama_toko') }}').trigger('change');
            @endif

            @if($user->department == 'SLS' && old('nama_agen'))
                $('#nama_agen').val('{{ old('nama_agen') }}').trigger('change');
            @endif
        });
    </script>

    <!-- Script untuk kalkulasi point (tetap sama) -->
    <script>
        function calculateRowTotal(targetId) {
            const input = document.getElementById(`jumlah_${targetId}`);
            const totalPointCell = document.getElementById(`total_point_${targetId}`);
            const totalKuponCell = document.getElementById(`total_kupon_${targetId}`);
            const point = parseInt(input.dataset.point);
            const kupon = parseInt(input.dataset.kupon) || 0;
            const jumlah = parseInt(input.value) || 0;
            
            const totalPoint = jumlah * point;
            const totalKupon = jumlah * kupon;
            
            // Add animation effect
            totalPointCell.style.transform = 'scale(1.1)';
            totalPointCell.textContent = totalPoint.toLocaleString('id-ID');
            
            totalKuponCell.style.transform = 'scale(1.1)';
            totalKuponCell.textContent = totalKupon.toLocaleString('id-ID');
            
            setTimeout(() => {
                totalPointCell.style.transform = 'scale(1)';
                totalKuponCell.style.transform = 'scale(1)';
            }, 150);
            
            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let grandTotalPoint = 0;
            let grandTotalKupon = 0;
            
            // Hitung total point dan kupon
            document.querySelectorAll('[id^="total_point_"]').forEach(cell => {
                const value = parseInt(cell.textContent.replace(/\./g, '')) || 0;
                grandTotalPoint += value;
            });
            
            document.querySelectorAll('[id^="total_kupon_"]').forEach(cell => {
                const value = parseInt(cell.textContent.replace(/\./g, '')) || 0;
                grandTotalKupon += value;
            });
            
            const grandTotalPointElement = document.getElementById('grandTotalPoint');
            const grandTotalKuponElement = document.getElementById('grandTotalKupon');
            const totalVoucherElement = document.getElementById('totalVoucher');
            
            // Update tampilan
            grandTotalPointElement.style.transform = 'scale(1.05)';
            grandTotalPointElement.textContent = grandTotalPoint.toLocaleString('id-ID');
            
            grandTotalKuponElement.style.transform = 'scale(1.05)';
            grandTotalKuponElement.textContent = grandTotalKupon.toLocaleString('id-ID');
            
            totalVoucherElement.style.transform = 'scale(1.05)';
            totalVoucherElement.textContent = grandTotalKupon.toLocaleString('id-ID');
            
            setTimeout(() => {
                grandTotalPointElement.style.transform = 'scale(1)';
                grandTotalKuponElement.style.transform = 'scale(1)';
                totalVoucherElement.style.transform = 'scale(1)';
            }, 200);
        }

        // Add real-time calculation for all inputs
        document.addEventListener('input', function(e) {
            if (e.target.id && e.target.id.startsWith('jumlah_')) {
                const targetId = e.target.id.replace('jumlah_', '');
                calculateRowTotal(targetId);
            }
        });

        // Initialize totals on page load
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($masterTargets as $target)
                calculateRowTotal({{ $target->id }});
            @endforeach
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 untuk toko
            $('.select2-toko').select2({
                placeholder: "Pilih Toko",
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Toko tidak ditemukan";
                    }
                }
            });

            @if($user->department == 'SLS')
            // Inisialisasi Select2 untuk agen (hanya untuk SLS)
            $('.select2-agen').select2({
                placeholder: "Pilih Agen",
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Agen tidak ditemukan";
                    }
                }
            });
            @endif
        });
    </script>
</x-app-layout>