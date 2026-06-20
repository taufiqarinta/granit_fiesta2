<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Pemenang Doorprize - Kobin Tiles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            /* background: linear-gradient(135deg, #1a1a1a, #2d2d2d); */
            background: #c8172d;
            color: white;
            min-height: 100vh;
            margin: 0;
            padding: 10px;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.1);
            max-width: 1400px;
            margin: 10px auto;
        }

        .header-icon {
            background: linear-gradient(135deg, #DC143C, #B22222);
            box-shadow: 0 4px 12px rgba(220, 20, 60, 0.4);
        }

        /* Styling untuk tabel */
        .winner-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: rgba(255,255,255,0.08);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        .winner-table thead {
            background: linear-gradient(135deg, #DC143C, #B22222);
        }

        .winner-table th {
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .winner-table td {
            padding: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9em;
        }

        .winner-table tbody tr {
            transition: all 0.3s ease;
        }

        .winner-table tbody tr:hover {
            background: rgba(220, 20, 60, 0.15);
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

        /* Responsive table */
        @media (max-width: 768px) {
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
            color: rgba(255,255,255,0.6);
        }

        .empty-state i {
            font-size: 3em;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="text-center compact-header">
        <div class="flex items-center justify-center mb-2">
            <div>
                <h1 class="text-2xl font-bold">🏆 List Pemenang Doorprize</h1>
                <p class="text-lg opacity-90">Kobin Tiles - Event {{ strtoupper($lokasi) }}</p>
            </div>
        </div>
        <p class="opacity-80 text-sm">{{ date('d F Y') }}</p>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Filter dan Info -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <div class="mb-4 md:mb-0">
                <h2 class="text-xl font-bold">Daftar Pemenang</h2>
                <p class="text-sm opacity-80" id="totalPemenang">Memuat data...</p>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="refreshData()" class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 rounded-lg hover:from-red-700 hover:to-red-800 transition-all flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
                <button onclick="exportToExcel()" class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 rounded-lg hover:from-green-700 hover:to-green-800 transition-all flex items-center">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export Excel
                </button>
            </div>
        </div>

        <!-- Tabel Pemenang -->
        <div class="overflow-hidden rounded-lg">
            <table class="winner-table">
                <thead>
                    <tr>
                        <th class="w-16 text-center">No</th>
                        <th>Nama Toko</th>
                        <th>Nama PIC</th>
                        <th>Nomor Voucher</th>
                        <th>Hadiah</th>
                    </tr>
                </thead>
                <tbody id="winnerTableBody">
                    <!-- Data akan diisi oleh JavaScript -->
                    <tr>
                        <td colspan="5" class="text-center py-8">
                            <div class="loading mx-auto mb-2"></div>
                            <p>Memuat data pemenang...</p>
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
        let currentPage = 1;
        const itemsPerPage = 50;
        let totalWinners = 0;

        // Fungsi untuk memuat data pemenang
        async function loadWinners(page = 1) {
            const tableBody = document.getElementById('winnerTableBody');
            const totalPemenangElement = document.getElementById('totalPemenang');
            
            try {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-8">
                            <div class="loading mx-auto mb-2"></div>
                            <p>Memuat data pemenang...</p>
                        </td>
                    </tr>
                `;

                const response = await fetch(`/doorprize/{{ $lokasi }}/winners?page=${page}&per_page=${itemsPerPage}`);
                const data = await response.json();

                if (data.success) {
                    totalWinners = data.total;
                    totalPemenangElement.textContent = `Total ${data.total} pemenang`;
                    
                    if (data.winners.length === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="fas fa-trophy"></i>
                                    <p>Belum ada pemenang</p>
                                    <p class="text-sm mt-2">Pemenang akan muncul setelah pengundian dilakukan</p>
                                </td>
                            </tr>
                        `;
                    } else {
                        tableBody.innerHTML = data.winners.map((winner, index) => {
                            const actualIndex = (page - 1) * itemsPerPage + index + 1;
                            return `
                                <tr class="hover:bg-red-50 hover:bg-opacity-10 transition-colors">
                                    <td class="text-center">
                                        <div class="number-badge mx-auto">${actualIndex}</div>
                                    </td>
                                    <td class="font-medium">${winner.nama_toko}</td>
                                    <td>${winner.nama_pic}</td>
                                    <td class="font-mono text-red-300">${winner.nomor_voucher}</td>
                                    <td>
                                        <span class="prize-badge">${winner.hadiah || 'Doorprize'}</span>
                                    </td>
                                </tr>
                            `;
                        }).join('');
                    }
                    
                    updatePagination(data.current_page, data.last_page);
                } else {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center py-8 text-red-300">
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
                        <td colspan="5" class="text-center py-8 text-red-300">
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
                <div class="text-sm opacity-80">
                    Menampilkan halaman ${currentPage} dari ${totalPages}
                </div>
                <div class="flex space-x-2">
            `;

            // Tombol Previous
            if (currentPage > 1) {
                paginationHTML += `
                    <button onclick="loadWinners(${currentPage - 1})" 
                            class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600 transition-colors">
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
                        <button class="px-3 py-1 bg-red-600 rounded font-bold">
                            ${i}
                        </button>
                    `;
                } else {
                    paginationHTML += `
                        <button onclick="loadWinners(${i})" 
                                class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600 transition-colors">
                            ${i}
                        </button>
                    `;
                }
            }

            // Tombol Next
            if (currentPage < totalPages) {
                paginationHTML += `
                    <button onclick="loadWinners(${currentPage + 1})" 
                            class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600 transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                `;
            }

            paginationHTML += `</div>`;
            paginationContainer.innerHTML = paginationHTML;
        }

        // Fungsi refresh data
        function refreshData() {
            loadWinners(currentPage);
        }

        // Fungsi export ke Excel (placeholder)
        function exportToExcel() {
            alert('Fitur export Excel akan segera tersedia!');
            // Implementasi export Excel bisa ditambahkan kemudian
        }

        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadWinners();
            
            // Auto refresh setiap 30 detik
            // setInterval(refreshData, 30000);
        });
    </script>
</body>
</html>