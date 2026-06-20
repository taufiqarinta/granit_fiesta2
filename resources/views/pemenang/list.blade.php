<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Klaim Doorprize') }}
        </h2>
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
        
        .max-w-8xl {
            position: relative;
            z-index: 10;
        }
        
        table {
            background: white;
            position: relative;
            z-index: 15;
        }
    </style>

    <div class="py-6">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <!-- Filter Lokasi -->
                        <div class="flex-1">
                            <label for="lokasi_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                Filter Berdasarkan Lokasi Event:
                            </label>
                            <select id="lokasi_filter" name="lokasi_event" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($lokasiEvents as $lokasi)
                                    <option value="{{ $lokasi->nama_lokasi }}" 
                                        {{ $selectedLokasi == $lokasi->nama_lokasi ? 'selected' : '' }}>
                                        {{ $lokasi->nama_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Info Lokasi Terpilih -->
                        <div class="flex-1 hidden">
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    <strong>Lokasi yang dipilih:</strong> 
                                    <span id="currentLokasi">{{ $selectedLokasi }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="custom-cursor" id="cursor"></div>
                    
                    <!-- Header -->
                    <div class="text-center mb-6">
                        <div class="flex items-center justify-center mb-4">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">🏆 List Pemenang Doorprize</h1>
                                <p class="text-lg text-gray-600">Kobin Tiles - Event <span id="headerLokasi">{{ strtoupper($selectedLokasi) }}</span></p>
                            </div>
                        </div>
                        <p class="text-gray-500">{{ date('d F Y') }}</p>
                    </div>

                    <!-- Filter dan Info -->
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                        <div class="mb-4 md:mb-0">
                            <h2 class="text-xl font-bold text-gray-800">Daftar Pemenang</h2>
                            <p class="text-sm text-gray-600" id="totalPemenang">Memuat data...</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Search Input -->
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="searchInput" 
                                    placeholder="Cari toko, PIC, voucher..." 
                                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors w-64"
                                >
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                            <button onclick="refreshData()" class="refresh-btn">
                                Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Pemenang dengan Container Scroll -->
                    <div class="table-container">
                        <table class="winner-table">
                            <thead>
                                <tr>
                                    <th class="w-16 text-center">No</th>
                                    <th class="w-20 text-center">Sudah Diambil</th>
                                    <th class="w-40">Waktu Ditukarkan</th>
                                    <th>Hadiah</th>
                                    <th>Nama Toko</th>
                                    <th>Nama PIC</th>
                                    <th>Nomor Voucher</th>
                                    <th>Kode Unik</th>
                                </tr>
                            </thead>
                            <tbody id="winnerTableBody">
                                <tr>
                                    <td colspan="7" class="text-center py-8">
                                        <div class="loading mx-auto mb-2"></div>
                                        <p class="text-gray-600">Memuat data pemenang...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-6" id="paginationContainer">
                        <!-- Pagination akan diisi oleh JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .refresh-btn {
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #6b7280, #374151);
            border: none;
            border-radius: 0.5rem;
            color: white;
            margin-left: 10px;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .refresh-btn:hover {
            background: linear-gradient(135deg, #4b5563, #1f2937);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .refresh-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .refresh-btn i {
            margin-right: 0.5rem;
        }
        /* Tambahkan semua CSS yang sama seperti sebelumnya */
        .custom-cursor {
            width: 8px;
            height: 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            transform: translate(-50%, -50%);
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        .header-icon {
            background: linear-gradient(135deg, #DC143C, #B22222);
            box-shadow: 0 4px 12px rgba(220, 20, 60, 0.4);
        }

        .table-container {
            max-height: 600px;
            overflow-y: auto;
            position: relative;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .winner-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .winner-table thead {
            position: sticky;
            top: 0;
            z-index: 10;
            background: linear-gradient(135deg, #DC143C, #B22222);
        }

        .winner-table th {
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: white;
            position: sticky;
            top: 0;
            box-shadow: 0 1px 0 rgba(0,0,0,0.1);
        }

        .winner-table td {
            padding: 12px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            font-size: 0.9em;
            color: #333;
        }

        .winner-table tbody tr {
            transition: all 0.3s ease;
        }

        .winner-table tbody tr:hover {
            background: rgba(220, 20, 60, 0.1);
            transform: translateY(-1px);
        }

        .winner-table tbody tr.checked-row {
            background: rgba(220, 20, 60, 0.1) !important;
            transform: translateY(-1px);
            border-left: 3px solid #DC143C;
        }

        .winner-table tbody tr.checked-row:hover {
            background: rgba(220, 20, 60, 0.15) !important;
        }

        .number-badge {
            background: linear-gradient(135deg, #DC143C, #B22222);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.8em;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        .prize-badge {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-checkbox {
            width: 24px;
            height: 24px;
            border: 2px solid #DC143C;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .custom-checkbox.checked {
            background: #DC143C;
            border-color: #DC143C;
        }

        .custom-checkbox.checked i {
            color: white;
            display: block;
        }

        .custom-checkbox:not(.checked) i {
            display: none;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #DC143C;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .search-highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>

    <script>
        const cursor = document.getElementById('cursor');
        document.addEventListener('mousemove', e => {
            cursor.style.left = e.pageX + 'px';
            cursor.style.top = e.pageY + 'px';
        });

        let currentPage = 1;
        const itemsPerPage = 100;
        let allWinners = [];
        let currentSearchTerm = '';
        let currentLokasi = '{{ $selectedLokasi }}';

        // Event listener untuk filter lokasi
        document.getElementById('lokasi_filter').addEventListener('change', function() {
            currentLokasi = this.value;
            document.getElementById('currentLokasi').textContent = currentLokasi;
            document.getElementById('headerLokasi').textContent = currentLokasi.toUpperCase();
            
            // Reset search dan pagination
            currentSearchTerm = '';
            document.getElementById('searchInput').value = '';
            currentPage = 1;
            
            // Load data dengan lokasi baru
            loadWinners();
        });

        // Fungsi untuk memuat data pemenang
        async function loadWinners(page = 1) {
            const tableBody = document.getElementById('winnerTableBody');
            const totalPemenangElement = document.getElementById('totalPemenang');
            
            try {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-8">
                            <div class="loading mx-auto mb-2"></div>
                            <p class="text-gray-600">Memuat data pemenang...</p>
                        </td>
                    </tr>
                `;

                const response = await fetch(`/pemenang/${currentLokasi}/data?page=${page}&per_page=${itemsPerPage}`);
                const data = await response.json();

                if (data.success) {
                    allWinners = data.winners;
                    
                    if (currentSearchTerm) {
                        performSearch(currentSearchTerm);
                    } else {
                        renderTable(allWinners, page);
                    }
                    
                    updatePagination(data.current_page, data.last_page);
                } else {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center py-8 text-red-600">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Gagal memuat data pemenang
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error loading winners:', error);
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-8 text-red-600">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Terjadi kesalahan saat memuat data
                        </td>
                    </tr>
                `;
            }
        }

        // Fungsi untuk melakukan search
        function performSearch(searchTerm) {
            currentSearchTerm = searchTerm.toLowerCase().trim();
            
            if (!currentSearchTerm) {
                renderTable(allWinners, currentPage);
                return;
            }

            const filteredWinners = allWinners.filter(winner => {
                const toko = winner.nama_toko?.toLowerCase() || '';
                const pic = winner.nama_pic?.toLowerCase() || '';
                const voucher = winner.nomor_voucher?.toLowerCase() || '';
                const hadiah = winner.hadiah?.toLowerCase() || '';
                
                return toko.includes(currentSearchTerm) || 
                    pic.includes(currentSearchTerm) || 
                    voucher.includes(currentSearchTerm) ||
                    hadiah.includes(currentSearchTerm);
            });

            renderTable(filteredWinners, 1);
        }

        // Fungsi untuk render tabel
        function renderTable(winners, page = 1) {
            const tableBody = document.getElementById('winnerTableBody');
            const totalPemenangElement = document.getElementById('totalPemenang');
            
            if (winners.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="fas fa-search"></i>
                            <p>${currentSearchTerm ? 'Tidak ditemukan data yang sesuai' : 'Belum ada pemenang'}</p>
                            <p class="text-sm mt-2">${currentSearchTerm ? 'Coba dengan kata kunci lain' : 'Pemenang akan muncul setelah pengundian dilakukan'}</p>
                        </td>
                    </tr>
                `;
                totalPemenangElement.textContent = `Total 0 pemenang${currentSearchTerm ? ' (filtered)' : ''}`;
                updatePagination(1, 1);
                return;
            }

            const totalPages = Math.ceil(winners.length / itemsPerPage);
            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const paginatedWinners = winners.slice(startIndex, endIndex);

            tableBody.innerHTML = paginatedWinners.map((winner, index) => {
                const actualIndex = startIndex + index + 1;
                const isChecked = winner.sudah_ditukarkan == 1;
                
                // TAMBAHKAN CLASS checked-row JIKA SUDAH DICHECK
                const rowClass = isChecked ? 'checked-row' : '';
                
                const highlightText = (text) => {
                    if (!currentSearchTerm || !text) return text;
                    const regex = new RegExp(`(${currentSearchTerm})`, 'gi');
                    return text.toString().replace(regex, '<span class="search-highlight">$1</span>');
                };

                return `
                    <tr class="hover:bg-red-50 transition-colors ${rowClass}"> <!-- TAMBAHKAN VARIABLE rowClass DI SINI -->
                        <td class="text-center">
                            <div class="number-badge mx-auto">${actualIndex}</div>
                        </td>
                        <td class="text-center">
                            <div class="checkbox-container">
                                <div class="custom-checkbox ${isChecked ? 'checked' : ''}" 
                                    onclick="toggleStatus(${winner.id}, ${isChecked ? 0 : 1})">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                            </div>
                        </td>
                        <td class="time-text">
                            ${winner.ditukarkan_at || '-'}
                        </td>
                        <td>
                            <span class="prize-badge">${highlightText(winner.hadiah || 'Doorprize')}</span>
                        </td>
                        <td class="font-medium text-gray-800">${highlightText(winner.nama_toko)}</td>
                        <td class="text-gray-700">${highlightText(winner.nama_pic)}</td>
                        <td class="font-mono text-red-600 font-bold">${highlightText(winner.nomor_voucher)}</td>
                        <td class="font-mono text-red-600 font-bold">${highlightText(winner.kode_unik)}</td>
                    </tr>
                `;
            }).join('');

            totalPemenangElement.textContent = `Total ${winners.length} pemenang${currentSearchTerm ? ' (filtered)' : ''}`;
            updatePagination(page, totalPages);
        }

        // Fungsi untuk toggle status penukaran
        async function toggleStatus(voucherId, newStatus) {
            try {
                const response = await fetch(`/pemenang/${voucherId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                });

                const result = await response.json();

                if (result.success) {
                    await loadWinners(currentPage); // Ini akan otomatis merefresh tampilan dengan class yang sesuai
                    showNotification('Status penukaran berhasil diupdate!', 'success');
                } else {
                    showNotification('Gagal mengupdate status penukaran', 'error');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                showNotification('Terjadi kesalahan saat mengupdate status', 'error');
            }
        }

        // Fungsi refresh data
        function refreshData() {
            currentSearchTerm = '';
            document.getElementById('searchInput').value = '';
            loadWinners(currentPage);
            showNotification('Data berhasil di-refresh', 'success');
        }

        // Fungsi untuk menampilkan notifikasi
        function showNotification(message, type = 'info') {
            const existingNotification = document.querySelector('.custom-notification');
            if (existingNotification) {
                existingNotification.remove();
            }

            const notification = document.createElement('div');
            notification.className = `custom-notification fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${
                        type === 'success' ? 'fa-check-circle' : 
                        type === 'error' ? 'fa-exclamation-circle' : 
                        'fa-info-circle'
                    } mr-2"></i>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        function updatePagination(currentPage, totalPages) {
            const paginationContainer = document.getElementById('paginationContainer');
            
            if (totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            let paginationHTML = `
                <div class="text-sm text-gray-600">
                    Menampilkan halaman ${currentPage} dari ${totalPages}
                </div>
                <div class="flex space-x-2">
            `;

            if (currentPage > 1) {
                paginationHTML += `
                    <button onclick="goToPage(${currentPage - 1})" 
                            class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                `;
            }

            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);

            for (let i = startPage; i <= endPage; i++) {
                if (i === currentPage) {
                    paginationHTML += `
                        <button class="px-3 py-1 bg-red-600 text-white rounded font-bold">
                            ${i}
                        </button>
                    `;
                } else {
                    paginationHTML += `
                        <button onclick="goToPage(${i})" 
                                class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">
                            ${i}
                        </button>
                    `;
                }
            }

            if (currentPage < totalPages) {
                paginationHTML += `
                    <button onclick="goToPage(${currentPage + 1})" 
                            class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                `;
            }

            paginationHTML += `</div>`;
            paginationContainer.innerHTML = paginationHTML;
        }

        function goToPage(page) {
            currentPage = page;
            if (currentSearchTerm) {
                renderTable(allWinners, page);
            } else {
                loadWinners(page);
            }
        }

        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadWinners();
            
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch(this.value);
                }, 300);
            });
            
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch(this.value);
                }
            });
        });
    </script>
</x-app-layout>