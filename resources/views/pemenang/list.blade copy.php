<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Pemenang Doorprize - Kobin Tiles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* *{
            cursor: none;
        }

        button, .doorprize-item, select, .checkbox-container {
            cursor: none !important;
        } */

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
            background: #c8172d;
            color: #333;
            min-height: 100vh;
            margin: 0;
            padding: 10px;
        }

        .container {
            background: white;
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            border: 1px solid rgba(0,0,0,0.1);
            max-width: 1600px;
            margin: 10px auto;
        }

        .header-icon {
            background: linear-gradient(135deg, #DC143C, #B22222);
            box-shadow: 0 4px 12px rgba(220, 20, 60, 0.4);
        }

        /* Container untuk tabel dengan scroll */
        .table-container {
            max-height: 600px;
            overflow-y: auto;
            position: relative;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        /* Styling untuk tabel */
        .winner-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        /* Freeze header */
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

        .winner-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badge untuk nomor urut */
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

        /* Status badge */
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

        /* Checkbox styling */
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

        .custom-checkbox:hover {
            border-color: #B22222;
            transform: scale(1.1);
        }

        .custom-checkbox.checked:hover {
            background: #B22222;
        }

        /* Status sudah ditukarkan */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: 600;
            text-align: center;
        }

        .status-sudah {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
        }

        .status-belum {
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: white;
        }

        /* Waktu ditukarkan */
        .time-text {
            font-size: 0.8em;
            color: #666;
            font-family: monospace;
        }

        /* Scrollbar styling */
        .table-container::-webkit-scrollbar {
            width: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #DC143C;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #B22222;
        }

        /* Responsive table */
        @media (max-width: 768px) {
            .table-container {
                max-height: 500px;
            }
            
            .winner-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .winner-table th,
            .winner-table td {
                padding: 10px 8px;
                font-size: 0.85em;
            }
            
            .number-badge {
                width: 25px;
                height: 25px;
                font-size: 0.75em;
            }
            
            .prize-badge {
                padding: 4px 10px;
                font-size: 0.75em;
            }
        }

        @media (max-width: 480px) {
            .table-container {
                max-height: 400px;
            }
            
            .winner-table th,
            .winner-table td {
                padding: 8px 6px;
                font-size: 0.8em;
            }
            
            .container {
                padding: 15px;
            }
        }

        /* Loading animation */
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

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 3em;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .search-highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
        }

        .search-no-match {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="custom-cursor" id="cursor"></div>
    
    <!-- Header -->
    <div class="text-center compact-header">
        <div class="flex items-center justify-center mb-2">
            <div>
                <h1 class="text-2xl font-bold text-white">🏆 List Pemenang Doorprize</h1>
                <p class="text-lg text-white opacity-90">Kobin Tiles - Event {{ strtoupper($lokasi) }}</p>
            </div>
        </div>
        <p class="text-white opacity-80 text-sm">{{ date('d F Y') }}</p>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Filter dan Info -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <div class="mb-4 md:mb-0">
                <h2 class="text-xl font-bold text-gray-800">Daftar Pemenang</h2>
                <p class="text-sm text-gray-600" id="totalPemenang">Memuat data...</p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Tambahkan input search di sini -->
                <div class="relative">
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="Cari toko, PIC, voucher..." 
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors w-64"
                    >
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                <button onclick="refreshData()" class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 rounded-lg hover:from-red-700 hover:to-red-800 transition-all flex items-center text-white">
                    <i class="fas fa-sync-alt mr-2"></i>
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
                    </tr>
                </thead>
                <tbody id="winnerTableBody">
                    <!-- Data akan diisi oleh JavaScript -->
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

    <script>
        const cursor = document.getElementById('cursor');
        document.addEventListener('mousemove', e => {
            cursor.style.left = e.pageX + 'px';
            cursor.style.top = e.pageY + 'px';
        });

        let currentPage = 1;
        const itemsPerPage = 100;
        let totalWinners = 0;
        let autoScrollInterval;
        let isAutoScrolling = false;

        // Tambahkan variabel global
        let allWinners = [];
        let currentSearchTerm = '';

        // Fungsi untuk melakukan search
        function performSearch(searchTerm) {
            currentSearchTerm = searchTerm.toLowerCase().trim();
            
            if (!currentSearchTerm) {
                // Jika search kosong, tampilkan semua data
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

            renderTable(filteredWinners, 1); // Reset ke halaman 1 saat search
        }

        // Fungsi untuk render tabel dengan data yang diberikan
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

            // Hitung pagination
            const totalPages = Math.ceil(winners.length / itemsPerPage);
            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const paginatedWinners = winners.slice(startIndex, endIndex);

            tableBody.innerHTML = paginatedWinners.map((winner, index) => {
                const actualIndex = startIndex + index + 1;
                const isChecked = winner.sudah_ditukarkan == 1;
                
                // Fungsi untuk highlight text
                const highlightText = (text) => {
                    if (!currentSearchTerm || !text) return text;
                    const regex = new RegExp(`(${currentSearchTerm})`, 'gi');
                    return text.toString().replace(regex, '<span class="search-highlight">$1</span>');
                };

                return `
                    <tr class="hover:bg-red-50 transition-colors">
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
                    </tr>
                `;
            }).join('');

            totalPemenangElement.textContent = `Total ${winners.length} pemenang${currentSearchTerm ? ' (filtered)' : ''}`;
            updatePagination(page, totalPages);
        }

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

                const response = await fetch(`/pemenang/{{ $lokasi }}/data?page=${page}&per_page=${itemsPerPage}`);
                const data = await response.json();

                if (data.success) {
                    // Simpan semua data untuk keperluan search
                    allWinners = data.winners;
                    totalWinners = data.total;
                    
                    if (currentSearchTerm) {
                        // Jika sedang dalam mode search, lakukan filter
                        performSearch(currentSearchTerm);
                    } else {
                        // Jika tidak, tampilkan data normal
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

        // Update fungsi refreshData untuk reset search
        function refreshData() {
            // Reset search
            currentSearchTerm = '';
            document.getElementById('searchInput').value = '';
            
            // Hentikan auto scroll saat refresh
            if (autoScrollInterval) {
                clearInterval(autoScrollInterval);
                isAutoScrolling = false;
            }
            
            loadWinners(currentPage);
            showNotification('Data berhasil di-refresh', 'success');
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
                    // Refresh data untuk update realtime
                    await loadWinners(currentPage);
                    
                    // Tampilkan notifikasi sukses
                    showNotification('Status penukaran berhasil diupdate!', 'success');
                } else {
                    showNotification('Gagal mengupdate status penukaran', 'error');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                showNotification('Terjadi kesalahan saat mengupdate status', 'error');
            }
        }

        // Fungsi untuk menampilkan notifikasi
        function showNotification(message, type = 'info') {
            // Hapus notifikasi sebelumnya jika ada
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

            // Hapus notifikasi setelah 3 detik
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

            // Tombol Previous
            if (currentPage > 1) {
                paginationHTML += `
                    <button onclick="goToPage(${currentPage - 1})" 
                            class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                `;
            }

            // Tombol halaman
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

            // Tombol Next
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


        // Fungsi untuk auto scroll
        function startAutoScroll() {
            const tableContainer = document.querySelector('.table-container');
            const tableBody = document.getElementById('winnerTableBody');
            
            // Hentikan scroll sebelumnya jika ada
            if (autoScrollInterval) {
                clearInterval(autoScrollInterval);
            }
            
            isAutoScrolling = true;
            let scrollDirection = 1; // 1 untuk scroll ke bawah, -1 untuk scroll ke atas
            let scrollSpeed = 30; // Kecepatan scroll (ms)
            
            autoScrollInterval = setInterval(() => {
                // Cek apakah sudah mencapai bagian bawah
                const isAtBottom = tableContainer.scrollTop + tableContainer.clientHeight >= tableContainer.scrollHeight - 5;
                
                // Cek apakah sudah mencapai bagian atas
                const isAtTop = tableContainer.scrollTop <= 5;
                
                if (isAtBottom) {
                    // Jika sudah sampai bawah, tunggu sebentar lalu reset ke atas
                    setTimeout(() => {
                        tableContainer.scrollTop = 0;
                        // Tunggu sebentar sebelum mulai scroll lagi
                        setTimeout(() => {
                            scrollDirection = 1;
                        }, 2000);
                    }, 2000);
                } else if (isAtTop && scrollDirection === -1) {
                    // Jika sudah sampai atas dan sedang scroll ke atas, ubah arah ke bawah
                    scrollDirection = 1;
                    setTimeout(() => {
                        scrollDirection = 1;
                    }, 2000);
                } else {
                    // Scroll sesuai arah
                    tableContainer.scrollTop += scrollDirection;
                }
            }, scrollSpeed);
            
            // Hentikan auto scroll saat user menginteraksi
            tableContainer.addEventListener('mouseenter', () => {
                if (autoScrollInterval) {
                    clearInterval(autoScrollInterval);
                    isAutoScrolling = false;
                }
            });
            
            // Lanjutkan auto scroll saat user tidak menginteraksi
            tableContainer.addEventListener('mouseleave', () => {
                if (!isAutoScrolling) {
                    startAutoScroll();
                }
            });
        }

        // Fungsi refresh data
        function refreshData() {
            // Hentikan auto scroll saat refresh
            if (autoScrollInterval) {
                clearInterval(autoScrollInterval);
                isAutoScrolling = false;
            }
            
            loadWinners(currentPage);
            showNotification('Data berhasil di-refresh', 'success');
        }

        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadWinners();
            
            // Tambahkan event listener untuk search
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch(this.value);
                }, 300); // Delay 300ms setelah user berhenti mengetik
            });
            
            // Enter key untuk search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch(this.value);
                }
            });
            
            // Auto refresh setiap 30 detik
            // setInterval(refreshData, 30000);
        });
    </script>
</body>
</html>