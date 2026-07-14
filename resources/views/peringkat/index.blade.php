<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Peringkat Toko') }}
        </h2>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>

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
        
        .max-w-9xl {
            position: relative;
            z-index: 10;
        }
        
        table {
            background: white;
            position: relative;
            z-index: 15;
        }

        #peringkat-container .detail-btn {  
            background-color: #4f46e5;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        #peringkat-container .detail-btn:hover {
            background-color: #4338ca;
        }

        .modal-body {
            padding: 20px;
            overflow-y: auto;
        }

        .modal-body .peringkat-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .modal-body .table-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 10px 14px;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .modal-body .table-body {
            background-color: white;
        }

        .modal-body .table-row {
            transition: background-color 0.15s ease;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-body .table-row:hover {
            background-color: #f9fafb;
        }

        .modal-body .table-cell {
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .modal-body .point-regular {
            font-weight: bold;
            text-align: center;
            color: #2563eb;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-overlay.hidden {
            display: none !important;
        }

        .modal-box {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
            color: #6b7280;
        }

        .modal-close-btn:hover {
            color: #1f2937;
        }
    </style>

    <div class="py-12">
        <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Search and Filter Section -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Search Box -->
                        <div>
                            <label for="search_input" class="block text-sm font-medium text-gray-700 mb-2">
                                Cari Toko/PIC/Kota:
                            </label>
                            <div class="relative">
                                <input type="text" id="search_input" name="search"
                                       placeholder="Masukkan nama toko, PIC, atau kota..."
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <!-- <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div> -->
                            </div>
                        </div>

                        <!-- Filter and Export Section -->
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Filter Lokasi -->
                            <div class="flex-1">
                                <label for="lokasi_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                    Filter Berdasarkan Lokasi Event:
                                </label>
                                <select id="lokasi_filter" name="lokasi_event" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach($lokasiEvents as $lokasi)
                                        <option value="{{ $lokasi->nama_lokasi }}" 
                                            {{ $defaultLokasi && $lokasi->nama_lokasi == $defaultLokasi->nama_lokasi ? 'selected' : '' }}>
                                            {{ $lokasi->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Export Button -->
                            <div class="flex items-end">
                                <button id="export-btn" 
                                        class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Last Update Info -->
                    <div id="last-update-info" class="mb-4 hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-blue-700">
                                    Terakhir update: <span id="last-update-time" class="font-medium"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div id="summary-cards" class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg p-4 text-white shadow-sm">
                            <p class="text-sm font-medium opacity-90">Total Point Keseluruhan</p>
                            <p id="summary-total-point" class="text-2xl font-bold mt-1">0</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg p-4 text-white shadow-sm">
                            <p class="text-sm font-medium opacity-90">Total Voucher Keseluruhan</p>
                            <p id="summary-total-voucher" class="text-2xl font-bold mt-1">0</p>
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <!-- <div id="loading" class="hidden flex justify-center items-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                        <span class="ml-3 text-gray-600">Memuat data...</span>
                    </div> -->

                    <!-- Error Message -->
                    <div id="error-message" class="hidden mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    </div>

                    <!-- Peringkat Table -->
                    <div id="peringkat-container">
                        <div class="relative">
                            <div class="overflow-x-auto table-wrapper">
                                <div class="inline-block min-w-full align-middle">
                                    <table class="peringkat-table">
                                        <thead>
                                            <tr>
                                                <th class="table-header">Peringkat</th>
                                                <th class="table-header hidden">Kode Agen</th>
                                                <th class="table-header">Nama Toko</th>
                                                <th class="table-header">Jumlah Point</th>
                                                <th class="table-header">Jumlah Voucher</th>
                                                <th class="table-header">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="peringkat-body" class="table-body">
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-gray-500">
                                                    Data akan dimuat...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="empty-state" class="hidden text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                        <p class="mt-1 text-sm text-gray-500">Tidak ada data peringkat untuk lokasi yang dipilih.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentFilter = '';
        let currentSearch = '';
        let pollingInterval = null;
        let lastUpdateTime = null;
        let searchTimeout = null;

        function init() {
            const filterSelect = document.getElementById('lokasi_filter');
            const searchInput = document.getElementById('search_input');
            const exportBtn = document.getElementById('export-btn');

            if (filterSelect) {
                currentFilter = filterSelect.value;
                filterSelect.addEventListener('change', function(e) {
                    currentFilter = e.target.value;
                    loadData();
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    currentSearch = e.target.value;
                    
                    searchTimeout = setTimeout(() => {
                        loadData();
                    }, 500);
                });
            }

            if (exportBtn) {
                exportBtn.addEventListener('click', exportToExcel);
            }

            loadData();
            startPolling();
        }

        function loadData() {
            showLoading();
            hideEmptyState();
            hideError();

            const url = new URL('{{ route("api.peringkat.data") }}');
            if (currentFilter) {
                url.searchParams.append('lokasi_event', currentFilter);
            }
            if (currentSearch) {
                url.searchParams.append('search', currentSearch);
            }

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }
                    return response.json();
                })
                .then(result => {
                    if (result.success) {
                        renderTable(result.data);
                        updateLastUpdateTime();
                    } else {
                        throw new Error(result.message || 'Unknown error');
                    }
                })
                .catch(error => {
                    showError('Gagal memuat data: ' + error.message);
                })
                .finally(() => {
                    hideLoading();
                });
        }

        function exportToExcel() {
            const url = new URL('{{ route("peringkat.export") }}');
            if (currentFilter) {
                url.searchParams.append('lokasi_event', currentFilter);
            }
            if (currentSearch) {
                url.searchParams.append('search', currentSearch);
            }

            window.location.href = url;
        }

        function renderTable(data) {
            const tbody = document.getElementById('peringkat-body');

            if (!data || data.length === 0) {
                showEmptyState();
                tbody.innerHTML = '';
                updateSummaryCards([]);
                return;
            }

            const rows = data.map(item => {
                let rankClass = (item.peringkat >= 1 && item.peringkat <= 5) ? 'rank-' + item.peringkat : 'rank-regular';
                let pointClass = (item.peringkat >= 1 && item.peringkat <= 5) ? 'point-top' : 'point-regular';

                return `
                <tr class="table-row">
                    <td class="table-cell ${rankClass}">
                        <div class="rank-container">
                            <span class="rank-badge">${item.peringkat}</span>
                        </div>
                    </td>
                    <td class="table-cell hidden">
                        ${escapeHtml(item.kode_agen || '-')}
                    </td>
                    <td class="table-cell ${item.peringkat <= 5 ? 'store-name-top' : 'store-name-regular'}">
                        ${escapeHtml(item.nama_toko || '-')}
                    </td>
                    <td class="table-cell ${pointClass}">
                        ${formatNumber(item.total_point_accumulated || 0)}
                    </td>
                    <td class="table-cell point-regular">
                        ${formatNumber(item.total_voucher_accumulated || 0)}
                    </td>
                    <td class="table-cell" style="text-align: center;">
                        <button type="button" class="detail-btn"
                                data-nama_toko="${escapeHtml(item.nama_toko || '')}"
                                data-no_hp="${escapeHtml(item.no_hp || '')}"
                                data-pic="${escapeHtml(item.pic || '')}"
                                data-kota="${escapeHtml(item.kota || '')}"
                                onclick="openDetailModal(this)">
                            Detail
                        </button>
                    </td>
                </tr>
            `}).join('');

            tbody.innerHTML = rows;
            updateSummaryCards(data);
        }

        function updateSummaryCards(data) {
            const totalPoint = data.reduce((sum, item) => sum + (Number(item.total_point_accumulated) || 0), 0);
            const totalVoucher = data.reduce((sum, item) => sum + (Number(item.total_voucher_accumulated) || 0), 0);

            document.getElementById('summary-total-point').textContent = formatNumber(totalPoint);
            document.getElementById('summary-total-voucher').textContent = formatNumber(totalVoucher);
        }

        function openDetailModal(btn) {
            const namaToko = btn.dataset.nama_toko;
            const noHp = btn.dataset.no_hp;
            const pic = btn.dataset.pic;
            const kota = btn.dataset.kota;

            const modal = document.getElementById('detail-modal');
            const title = document.getElementById('detail-modal-title');
            const body = document.getElementById('detail-modal-body');
            const loading = document.getElementById('detail-loading');
            const errorEl = document.getElementById('detail-error');
            const summaryEl = document.getElementById('detail-summary');

            title.textContent = 'Detail Toko: ' + namaToko;
            body.innerHTML = '';
            errorEl.classList.add('hidden');
            summaryEl.classList.add('hidden'); // tambahan
            loading.classList.remove('hidden');
            modal.classList.remove('hidden');

            const url = new URL('{{ route("api.peringkat.detail") }}');
            url.searchParams.append('nama_toko', namaToko);
            url.searchParams.append('no_hp', noHp);
            url.searchParams.append('pic', pic);
            url.searchParams.append('kota', kota);
            if (currentFilter) {
                url.searchParams.append('lokasi_event', currentFilter);
            }

            fetch(url)
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        renderDetailTable(result.data);
                    } else {
                        throw new Error(result.message || 'Gagal memuat detail');
                    }
                })
                .catch(err => {
                    errorEl.textContent = 'Gagal memuat detail: ' + err.message;
                    errorEl.classList.remove('hidden');
                })
                .finally(() => {
                    loading.classList.add('hidden');
                });
        }

        function renderDetailTable(data) {
            const body = document.getElementById('detail-modal-body');
            const summaryEl = document.getElementById('detail-summary');

            if (!data || data.length === 0) {
                body.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-gray-500">Tidak ada data detail.</td></tr>';
                summaryEl.classList.add('hidden');
                return;
            }

            body.innerHTML = data.map(item => `
                <tr class="table-row">
                    <td class="table-cell">${escapeHtml(item.kode_agen || '-')}</td>
                    <td class="table-cell">${escapeHtml(item.nama_toko || '-')}</td>
                    <td class="table-cell">${escapeHtml(item.pic || '-')}</td>
                    <td class="table-cell">${escapeHtml(item.no_hp || '-')}</td>
                    <td class="table-cell">${escapeHtml(item.kota || '-')}</td>
                    <td class="table-cell point-regular">${formatNumber(item.total_point || 0)}</td>
                    <td class="table-cell point-regular">${formatNumber(item.jumlah_voucher || 0)}</td>
                </tr>
            `).join('');

            const totalPoint = data.reduce((sum, item) => sum + (Number(item.total_point) || 0), 0);
            const totalVoucher = data.reduce((sum, item) => sum + (Number(item.jumlah_voucher) || 0), 0);

            document.getElementById('detail-summary-point').textContent = formatNumber(totalPoint);
            document.getElementById('detail-summary-voucher').textContent = formatNumber(totalVoucher);
            summaryEl.classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detail-modal').classList.add('hidden');
        }

        function updateLastUpdateTime() {
            lastUpdateTime = new Date();
            const timeString = formatDateTime(lastUpdateTime);
            
            const lastUpdateTimeEl = document.getElementById('last-update-time');
            if (lastUpdateTimeEl) {
                lastUpdateTimeEl.textContent = timeString;
            }
            
            const lastUpdateInfo = document.getElementById('last-update-info');
            if (lastUpdateInfo) {
                lastUpdateInfo.classList.remove('hidden');
            }
        }

        function formatDateTime(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            
            return `${day}-${month}-${year} | ${hours}:${minutes}:${seconds}`;
        }

        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function escapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return unsafe.toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function showLoading() {
            document.getElementById('loading')?.classList.remove('hidden');
            document.getElementById('peringkat-container')?.classList.add('hidden');
        }

        function hideLoading() {
            document.getElementById('loading')?.classList.add('hidden');
            document.getElementById('peringkat-container')?.classList.remove('hidden');
        }

        function showEmptyState() {
            document.getElementById('empty-state')?.classList.remove('hidden');
            document.getElementById('peringkat-container')?.classList.add('hidden');
            document.getElementById('last-update-info')?.classList.add('hidden');
        }

        function hideEmptyState() {
            document.getElementById('empty-state')?.classList.add('hidden');
            document.getElementById('peringkat-container')?.classList.remove('hidden');
        }

        function showError(message) {
            const errorEl = document.getElementById('error-message');
            if (errorEl) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
            }
        }

        function hideError() {
            document.getElementById('error-message')?.classList.add('hidden');
        }

        function startPolling() {
            pollingInterval = setInterval(() => {
                loadData();
            }, 15000);
        }

        function stopPolling() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            init();
        });

        window.addEventListener('beforeunload', function() {
            stopPolling();
        });
    </script>

    <style>
        /* Pastikan app-layout tetap utuh */
        x-app-layout {
            display: block !important;
        }

        /* Perbaikan untuk navigasi */
        .fixed.inset-y-0.left-0 {
            position: fixed !important;
            z-index: 40 !important;
        }

        /* Tabel Styles - Perbaikan lebih spesifik */
        #peringkat-container .peringkat-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        #peringkat-container .table-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 12px 16px;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: 1px solid #e5e7eb;
            white-space: nowrap;
            min-width: 120px;
        }

        #peringkat-container .table-header:first-child {
            min-width: 80px;
        }

        #peringkat-container .table-body {
            background-color: white;
        }

        #peringkat-container .table-row {
            transition: all 0.2s ease;
            border-bottom: 1px solid #e5e7eb;
        }

        #peringkat-container .table-row:hover {
            background-color: #f9fafb;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #peringkat-container .table-cell {
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            font-size: 0.875rem;
            white-space: nowrap;
        }

        /* Rank Styles */
        #peringkat-container .rank-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #peringkat-container .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-weight: bold;
            font-size: 0.875rem;
            border: 2px solid;
        }

        /* Warna untuk peringkat 1-5 */
        #peringkat-container .rank-1 .rank-badge {
            background-color: #dc2626;
            color: white;
            border-color: white;
        }

        #peringkat-container .rank-2 .rank-badge {
            background-color: #ef4444;
            color: white;
            border-color: white;
        }

        #peringkat-container .rank-3 .rank-badge {
            background-color: #f87171;
            color: #1f2937;
            border-color: #fecaca;
        }

        #peringkat-container .rank-4 .rank-badge {
            background-color: #fca5a5;
            color: #1f2937;
            border-color: #fecaca;
        }

        #peringkat-container .rank-5 .rank-badge {
            background-color: #fecaca;
            color: #1f2937;
            border-color: #fecaca;
        }

        #peringkat-container .rank-regular .rank-badge {
            background-color: #e5e7eb;
            color: #6b7280;
            border-color: #d1d5db;
        }

        /* Point Styles */
        #peringkat-container .point-top {
            font-weight: bold;
            text-align: center;
            color: #2563eb;
        }

        #peringkat-container .point-regular {
            font-weight: 400;
            text-align: center;
            color: #2563eb;
        }

        /* Store Name Styles */
        #peringkat-container .store-name-top {
            font-weight: bold;
            color: #1f2937;
        }

        #peringkat-container .store-name-regular {
            font-weight: 500;
            color: #1f2937;
        }

        /* Table Wrapper */
        #peringkat-container .table-wrapper {
            overflow-x: auto;
            border-radius: 8px;
        }

        #peringkat-container .table-wrapper::-webkit-scrollbar {
            height: 8px;
        }

        #peringkat-container .table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        #peringkat-container .table-wrapper::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        #peringkat-container .table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Utility Classes dengan scope yang lebih ketat */
        #peringkat-container .hidden {
            display: none !important;
        }

        .text-center {
            text-align: center;
        }

        /* Pastikan tidak ada overflow hidden yang mempengaruhi layout utama */
        body {
            overflow-x: visible !important;
        }

        /* Reset untuk element yang mungkin terpengaruh */
        .min-h-screen {
            min-height: 100vh !important;
        }
    </style>

    <div id="detail-modal" class="modal-overlay hidden">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="detail-modal-title" class="modal-title">Detail Toko</h3>
                <button type="button" class="modal-close-btn" onclick="closeDetailModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="detail-loading" class="text-center py-4 text-gray-500 hidden">Memuat detail...</div>
                <div id="detail-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-3"></div>

                <!-- Summary di dalam modal -->
                <div id="detail-summary" class="hidden mb-4 grid grid-cols-2 gap-3">
                    <div class="bg-indigo-50 border border-indigo-200 rounded-md p-3">
                        <p class="text-xs text-indigo-600 font-medium">Total Point Toko Ini</p>
                        <p id="detail-summary-point" class="text-lg font-bold text-indigo-700 mt-1">0</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-md p-3">
                        <p class="text-xs text-green-600 font-medium">Total Voucher Toko Ini</p>
                        <p id="detail-summary-voucher" class="text-lg font-bold text-green-700 mt-1">0</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="peringkat-table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="table-header">Kode Agen</th>
                                <th class="table-header">Nama Toko</th>
                                <th class="table-header">PIC</th>
                                <th class="table-header">No HP</th>
                                <th class="table-header">Kota Toko</th>
                                <th class="table-header">Total Point</th>
                                <th class="table-header">Jumlah Voucher</th>
                            </tr>
                        </thead>
                        <tbody id="detail-modal-body" class="table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>