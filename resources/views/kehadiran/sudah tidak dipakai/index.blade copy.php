<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Kehadiran Event') }}
            </h2>
            <a href="{{ route('kehadiran.export') }}" 
               class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                Export Excel
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    
                    <!-- Filter dan Stats -->
                    <div class="mb-6">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                            <!-- Filter Lokasi Event -->
                            <div class="w-full md:w-auto">
                                <label for="lokasi_event" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Lokasi Event
                                </label>
                                <select name="lokasi_event" id="lokasi_event" 
                                    class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Semarang" {{ $lokasiEvent == 'Semarang' ? 'selected' : '' }}>Semarang</option>
                                    <option value="Surabaya" {{ $lokasiEvent == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                                    <option value="Jakarta" {{ $lokasiEvent == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                                </select>
                            </div>
                            
                            <!-- Stats -->
                            <div id="stats-container" class="w-full md:w-auto grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <!-- Stats akan diupdate via AJAX -->
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Kehadiran -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Toko</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Toko</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agen</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kota</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Hadir</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Total Kehadiran</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="table-body">
                                @foreach ($tokos as $index => $toko)
                                    <tr class="hover:bg-gray-50" data-kode-toko="{{ $toko->kode_toko }}">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $toko->kode_toko }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $toko->nama_toko }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div class="text-xs">
                                                <div><strong>{{ $toko->kode_agen }}</strong></div>
                                                <div class="text-gray-600">{{ $toko->nama_agen }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ $toko->kotaData->nama ?? $toko->kota }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            <input type="checkbox" 
                                                class="hadir-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                data-kode-toko="{{ $toko->kode_toko }}"
                                                {{ $toko->hadir ? 'checked' : '' }}>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            <input type="number" 
                                                class="total-kehadiran-input w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                data-kode-toko="{{ $toko->kode_toko }}"
                                                value="{{ $toko->total_kehadiran ?? 0 }}" 
                                                min="0">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <span class="status-badge px-2 py-1 rounded-full text-xs font-medium 
                                                {{ $toko->hadir ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $toko->hadir ? 'Hadir' : 'Belum' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Loading Indicator -->
                    <div id="loading" class="hidden mt-4 text-center">
                        <div class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-md">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memperbarui data...
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentLokasiEvent = $('#lokasi_event').val();
            let updateTimeout;

            // Load stats awal
            loadStats(currentLokasiEvent);

            // Event ketika lokasi event berubah
            $('#lokasi_event').change(function() {
                currentLokasiEvent = $(this).val();
                // Redirect ke URL yang sama dengan parameter lokasi_event
                window.location.href = `{{ route('kehadiran.index') }}?lokasi_event=${currentLokasiEvent}`;
            });

            // Event untuk checkbox hadir
            $('.hadir-checkbox').change(function() {
                const kodeToko = $(this).data('kode-toko');
                const hadir = $(this).is(':checked');
                
                updateKehadiran(kodeToko, hadir, null);
            });

            // Event untuk input total kehadiran
            $('.total-kehadiran-input').on('input', function() {
                const kodeToko = $(this).data('kode-toko');
                const totalKehadiran = $(this).val();

                // Debounce untuk menghindari terlalu banyak request
                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(() => {
                    updateKehadiran(kodeToko, null, totalKehadiran);
                }, 500);
            });

            function updateKehadiran(kodeToko, hadir, totalKehadiran) {
                console.log('Data yang dikirim:', {
                    kode_toko: kodeToko,
                    lokasi_event: currentLokasiEvent,
                    hadir: hadir,
                    total_kehadiran: totalKehadiran
                });

                $('#loading').removeClass('hidden');

                // Ambil nilai hadir yang SEKARANG dari checkbox
                const currentCheckbox = $(`input[data-kode-toko="${kodeToko}"]`);
                let hadirValue = currentCheckbox.is(':checked') ? 1 : 0;

                // Ambil nilai total_kehadiran yang SEKARANG dari input
                const currentTotalInput = $(`input.total-kehadiran-input[data-kode-toko="${kodeToko}"]`);
                let totalKehadiranValue = currentTotalInput.val() || 0;

                // Jika fungsi dipanggil dengan nilai spesifik, gunakan itu
                if (hadir !== null) {
                    hadirValue = hadir ? 1 : 0;
                }
                
                if (totalKehadiran !== null) {
                    totalKehadiranValue = totalKehadiran;
                }

                $.ajax({
                    url: '{{ route("kehadiran.update") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        kode_toko: kodeToko,
                        lokasi_event: currentLokasiEvent,
                        hadir: hadirValue, // Selalu kirim nilai hadir yang terbaru
                        total_kehadiran: totalKehadiranValue // Selalu kirim nilai total yang terbaru
                    },
                    success: function(response) {
                        console.log('Response sukses:', response);
                        $('#loading').addClass('hidden');
                        
                        if (response.success) {
                            // Update UI status badge
                            const row = $(`tr[data-kode-toko="${kodeToko}"]`);
                            const badge = row.find('.status-badge');
                            
                            if (hadirValue === 1) {
                                badge.removeClass('bg-gray-100 text-gray-800')
                                    .addClass('bg-green-100 text-green-800')
                                    .text('Hadir');
                            } else {
                                badge.removeClass('bg-green-100 text-green-800')
                                    .addClass('bg-gray-100 text-gray-800')
                                    .text('Belum');
                            }

                            loadStats(currentLokasiEvent);
                            showTempMessage('Data berhasil disimpan', 'success');
                        } else {
                            showTempMessage('Gagal: ' + response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error detail:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText,
                            statusCode: xhr.status
                        });
                        
                        $('#loading').addClass('hidden');
                        
                        let errorMessage = 'Gagal menyimpan data';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            errorMessage = xhr.responseText || error;
                        }
                        
                        showTempMessage(errorMessage, 'error');
                    }
                });
            }

            // Event untuk checkbox hadir
            $('.hadir-checkbox').change(function() {
                const kodeToko = $(this).data('kode-toko');
                const hadir = $(this).is(':checked');
                
                // Kirim nilai hadir yang baru, tapi total_kehadiran ambil dari input yang sekarang
                updateKehadiran(kodeToko, hadir, null);
            });

            // Event untuk input total kehadiran
            $('.total-kehadiran-input').on('input', function() {
                const kodeToko = $(this).data('kode-toko');
                const totalKehadiran = $(this).val();

                // Debounce untuk menghindari terlalu banyak request
                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(() => {
                    // Kirim nilai total_kehadiran yang baru, tapi hadir ambil dari checkbox yang sekarang
                    updateKehadiran(kodeToko, null, totalKehadiran);
                }, 500);
            });

            function loadStats(lokasiEvent) {
                $.ajax({
                    url: `{{ url('kehadiran/stats') }}/${lokasiEvent}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const stats = response.data;
                            $('#stats-container').html(`
                                <div class="bg-blue-50 p-3 rounded-lg text-center">
                                    <div class="text-blue-800 font-bold text-lg">${stats.total_toko || 0}</div>
                                    <div class="text-blue-600 text-sm">Total Toko</div>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg text-center">
                                    <div class="text-green-800 font-bold text-lg">${stats.total_hadir || 0}</div>
                                    <div class="text-green-600 text-sm">Hadir</div>
                                </div>
                                <div class="bg-purple-50 p-3 rounded-lg text-center">
                                    <div class="text-purple-800 font-bold text-lg">${stats.total_jumlah_kehadiran || 0}</div>
                                    <div class="text-purple-600 text-sm">Total Kehadiran</div>
                                </div>
                            `);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading stats:', xhr.responseText);
                    }
                });
            }

            function showTempMessage(message, type) {
                const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
                const messageDiv = $(`
                    <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300">
                        ${message}
                    </div>
                `);
                
                $('body').append(messageDiv);
                
                setTimeout(() => {
                    messageDiv.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 3000);
            }
        });
    </script>
</x-app-layout>