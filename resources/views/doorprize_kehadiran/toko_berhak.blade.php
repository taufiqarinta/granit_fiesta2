<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Berhak Doorprize Kehadiran - Kobin Tiles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        *{
            cursor: none;
        }

        button, select {
            cursor: none !important;
        }

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
            max-width: 1400px;
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

        .winner-table tbody tr.claimed-row {
            background: rgba(16, 185, 129, 0.08) !important;
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
        .eligible-badge {
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

        .won-badge {
            background: linear-gradient(135deg, #9CA3AF, #6B7280);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .time-badge {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            color: #374151;
            background: #F3F4F6;
            padding: 4px 10px;
            border-radius: 8px;
        }

        .prize-badge {
            color: #92400E;
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
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
            
            .eligible-badge,
            .won-badge {
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
    </style>
</head>
<body>
    <div class="custom-cursor" id="cursor"></div>
    <!-- Header -->
    <div class="text-center compact-header">
        <div class="flex items-center justify-center mb-2">
            <div>
                <h1 class="text-2xl font-bold text-white">🎫 Toko Berhak Undian Doorprize Kehadiran</h1>
                <p class="text-lg text-white opacity-90">Kobin Tiles - Event {{ strtoupper($lokasi) }}</p>
            </div>
        </div>
        <p class="text-white opacity-80 text-sm">Waktu hadir maksimal <span id="batasJamLabel">18:00:00</span> | {{ date('d F Y') }}</p>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Filter dan Info -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <div class="mb-4 md:mb-0">
                <h2 class="text-xl font-bold text-gray-800">Toko yang Berhak Ikut Undian</h2>
                <p class="text-sm text-gray-600" id="totalToko">Memuat data...</p>
            </div>
            <div class="flex items-center space-x-4">
                <div>
                    <label for="doorprize_id" class="block text-sm font-semibold text-gray-700 mb-1">Pilih Hadiah</label>
                    <select id="doorprize_id" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 bg-white text-gray-800">
                        <option value="">-- Pilih Hadiah --</option>
                        @foreach($doorprizes as $doorprize)
                            <option
                                value="{{ $doorprize->id }}"
                                data-batas="{{ $doorprize->batas_jam_kehadiran }}"
                                data-nama="{{ $doorprize->nama_doorprize }}"
                            >
                                {{ $doorprize->nama_doorprize }} ({{ $doorprize->jumlah_doorprize }} {{ $doorprize->jumlah_doorprize == 1 ? 'Winner' : 'Winners' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button onclick="refreshData()" class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 rounded-lg hover:from-red-700 hover:to-red-800 transition-all flex items-center text-white">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Tabel Toko dengan Container Scroll -->
        <div class="table-container">
            <table class="winner-table">
                <thead>
                    <tr>
                        <th class="w-16 text-center">No</th>
                        <th>Kode Toko</th>
                        <th>Nama Toko</th>
                        <th>Nama PIC</th>
                        <th>Kota</th>
                        <th>Waktu Hadir</th>
                        <th>Hadiah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="tokoTableBody">
                    <!-- Data akan diisi oleh JavaScript -->
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <div class="loading mx-auto mb-2"></div>
                            <p class="text-gray-600">Memuat data toko...</p>
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
        const itemsPerPage = 50;
        let totalToko = 0;
        let autoScrollInterval;
        let isAutoScrolling = false;

        function getDoorprizeId() {
            return document.getElementById('doorprize_id').value;
        }

        function updateBatasLabel() {
            const select = document.getElementById('doorprize_id');
            const option = select.options[select.selectedIndex];
            const batas = option && option.dataset.batas ? option.dataset.batas : '18:00:00';
            document.getElementById('batasJamLabel').textContent = batas;
        }

        // Fungsi untuk memuat data toko
        async function loadTokos(page = 1) {
            const tableBody = document.getElementById('tokoTableBody');
            const totalTokoElement = document.getElementById('totalToko');
            
            try {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <div class="loading mx-auto mb-2"></div>
                            <p class="text-gray-600">Memuat data toko...</p>
                        </td>
                    </tr>
                `;

                const doorprizeId = getDoorprizeId();
                const response = await fetch(`/doorprize-kehadiran/{{ $lokasi }}/toko-berhak/data?page=${page}&per_page=${itemsPerPage}&doorprize_id=${doorprizeId}`);
                const data = await response.json();

                if (data.success) {
                    totalToko = data.total;
                    totalTokoElement.textContent = `Total ${data.total} toko`;
                    if (data.batas_jam_kehadiran) {
                        document.getElementById('batasJamLabel').textContent = data.batas_jam_kehadiran;
                    }
                    
                    if (data.tokos.length === 0) {
                        const batas = document.getElementById('batasJamLabel').textContent;
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <i class="fas fa-store"></i>
                                    <p>Tidak ada toko yang berhak ikut undian</p>
                                    <p class="text-sm mt-2">Pastikan ada toko hadir dengan waktu kehadiran maksimal ${batas}</p>
                                </td>
                            </tr>
                        `;
                    } else {
                        tableBody.innerHTML = data.tokos.map((toko, index) => {
                            const actualIndex = (page - 1) * itemsPerPage + index + 1;
                            const rowClass = toko.sudah_menang ? 'claimed-row' : '';
                            return `
                                <tr class="hover:bg-red-50 transition-colors ${rowClass}">
                                    <td class="text-center">
                                        <div class="number-badge mx-auto">${actualIndex}</div>
                                    </td>
                                    <td class="font-mono text-red-600 font-bold">${toko.kode_toko}</td>
                                    <td class="font-medium text-gray-800">${toko.nama_toko}</td>
                                    <td class="text-gray-700">${toko.nama_pic}</td>
                                    <td class="text-gray-700">${toko.kota}</td>
                                    <td><span class="time-badge">${toko.waktu_kehadiran}</span></td>
                                    <td>
                                        ${toko.hadiah
                                            ? `<span class="prize-badge">${toko.hadiah}</span>`
                                            : '<span class="text-gray-400">-</span>'}
                                    </td>
                                    <td>
                                        ${toko.sudah_menang 
                                            ? '<span class="won-badge">Sudah Menang</span>' 
                                            : '<span class="eligible-badge">Berhak</span>'}
                                    </td>
                                </tr>
                            `;
                        }).join('');
                    }
                    
                    updatePagination(data.current_page, data.last_page);
                    
                    // Mulai auto scroll setelah data dimuat
                    if (data.tokos.length > 0) {
                        startAutoScroll();
                    }
                } else {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="8" class="text-center py-8 text-red-600">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Gagal memuat data toko
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error loading tokos:', error);
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-8 text-red-600">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Terjadi kesalahan saat memuat data
                        </td>
                    </tr>
                `;
            }
        }

        // Fungsi untuk update pagination
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
                    <button onclick="loadTokos(${currentPage - 1})" 
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
                        <button onclick="loadTokos(${i})" 
                                class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">
                            ${i}
                        </button>
                    `;
                }
            }

            // Tombol Next
            if (currentPage < totalPages) {
                paginationHTML += `
                    <button onclick="loadTokos(${currentPage + 1})" 
                            class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                `;
            }

            paginationHTML += `</div>`;
            paginationContainer.innerHTML = paginationHTML;
        }

        // Fungsi untuk auto scroll
        function startAutoScroll() {
            const tableContainer = document.querySelector('.table-container');
            const tableBody = document.getElementById('tokoTableBody');
            
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
            
            loadTokos(currentPage);
        }

        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Auto pilih hadiah pertama
            const doorprizeSelect = document.getElementById('doorprize_id');
            if (doorprizeSelect.options.length > 1) {
                doorprizeSelect.selectedIndex = 1;
            }
            updateBatasLabel();

            doorprizeSelect.addEventListener('change', function() {
                updateBatasLabel();
                // Hentikan auto scroll saat ganti hadiah
                if (autoScrollInterval) {
                    clearInterval(autoScrollInterval);
                    isAutoScrolling = false;
                }
                loadTokos(1);
            });

            loadTokos();
        });
    </script>
</body>
</html>
