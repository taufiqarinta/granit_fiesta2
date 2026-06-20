<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOP SPENDER TOKO - {{ ucfirst($lokasi) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            background: linear-gradient(135deg, #c8172d, #c8172d,rgb(255, 255, 255));
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .juara-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
        }

        /* Warna gradasi untuk peringkat 1-5 */
        .rank-1::before { background: linear-gradient(90deg, #8B0000, #B22222); }
        .rank-2::before { background: linear-gradient(90deg, #B22222, #DC143C); }
        .rank-3::before { background: linear-gradient(90deg, #DC143C, #FF4500); }
        .rank-4::before { background: linear-gradient(90deg, #FF4500, #FF6347); }
        .rank-5::before { background: linear-gradient(90deg, #FF6347, #FF7F50); }

        .rank-1 .rank-badge { background: #8B0000; }
        .rank-2 .rank-badge { background: #B22222; }
        .rank-3 .rank-badge { background: #DC143C; }
        .rank-4 .rank-badge { background: #FF4500; }
        .rank-5 .rank-badge { background: #FF6347; }

        .card {
            background: white;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .rank-badge {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.4rem;
            color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            flex-shrink: 0;
        }

        .card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .store-name {
            font-size: 1.4rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 2px;
        }


        .info-left, .info-right {
            flex: 1;
        }

        .kota {
            font-size: 1rem;
            color: #7f8c8d;
            font-weight: 500;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 2px 15px;
            align-items: center;
        }

        .info-label {
            font-weight: 600;
            color: #7f8c8d;
            min-width: 80px;
        }

        .info-value {
            color: #2c3e50;
        }

        .total-point {
            /* background: linear-gradient(135deg, #667eea, #764ba2); */
            background: linear-gradient(135deg, #c8172d, #c8172d,#c8172d, rgb(255, 255, 255));
            color: white;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            margin-top: 15px;
            font-size: 1.3rem;
            font-weight: bold;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: white;
            font-size: 1.2rem;
        }

        .loading-spinner {
            border: 4px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top: 4px solid white;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        .error-message {
            background: #e74c3c;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: white;
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }

        .last-update {
            text-align: center;
            color: white;
            margin-top: 20px;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .juara-container {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .card {
                padding: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏆 TOP 3 SPENDER</h1>
            <!-- <div class="subtitle">Lokasi Event: {{ ucfirst($lokasi) }}</div> -->
        </div>

        <div id="loading" class="loading">
            <!-- <div class="loading-spinner"></div>
            <div>Memuat data peringkat...</div> -->
        </div>

        <div id="error-message" class="error-message" style="display: none;"></div>

        <div id="juara-container" class="juara-container" style="display: none;"></div>

        <div id="empty-state" class="empty-state" style="display: none;">
            <h3>Belum ada data peringkat</h3>
            <p>Data untuk lokasi {{ ucfirst($lokasi) }} akan segera tersedia</p>
        </div>

        <div id="last-update" class="last-update" style="display: none;"></div>
    </div>

    <script>
        let pollingInterval = null;
        let lastUpdateTime = null;

        function init() {
            loadData();
            startPolling();
        }

        function loadData() {
            showLoading();
            hideError();
            hideJuaraContainer();
            hideEmptyState();

            fetch(`/ranking/{{ $lokasi }}/data`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }
                    return response.json();
                })
                .then(result => {
                    if (result.success) {
                        if (result.data && result.data.length > 0) {
                            renderCards(result.data);
                            showJuaraContainer();
                        } else {
                            showEmptyState();
                        }
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

        function renderCards(data) {
            const container = document.getElementById('juara-container');
            
            const cards = data.map(item => {
                const rankClass = `rank-${item.peringkat}`;
                
                return `
                    <div class="card ${rankClass}">
                        <div class="rank-badge">${item.peringkat}</div>
                        <div class="card-content">
                            <div class="store-name">${escapeHtml(item.nama_toko || '-')}</div>
                            <div class="info-grid">
                                <div class="info-label">Kota:</div>
                                <div class="info-value">${escapeHtml(item.kota || '-')}</div>
                                
                                <div class="info-label">PIC:</div>
                                <div class="info-value">${escapeHtml(item.pic || '-')}</div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            container.innerHTML = cards;
        }

        function updateLastUpdateTime() {
            lastUpdateTime = new Date();
            const timeString = formatDateTime(lastUpdateTime);
            
            const lastUpdateEl = document.getElementById('last-update');
            if (lastUpdateEl) {
                lastUpdateEl.textContent = `Terakhir update: ${timeString}`;
                lastUpdateEl.style.display = 'block';
            }
        }

        function formatDateTime(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            
            return `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
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
            document.getElementById('loading').style.display = 'block';
        }

        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
        }

        function showJuaraContainer() {
            document.getElementById('juara-container').style.display = 'grid';
        }

        function hideJuaraContainer() {
            document.getElementById('juara-container').style.display = 'none';
        }

        function showEmptyState() {
            document.getElementById('empty-state').style.display = 'block';
        }

        function hideEmptyState() {
            document.getElementById('empty-state').style.display = 'none';
        }

        function showError(message) {
            const errorEl = document.getElementById('error-message');
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }

        function hideError() {
            document.getElementById('error-message').style.display = 'none';
        }

        function startPolling() {
            pollingInterval = setInterval(() => {
                loadData();
            }, 15000); // Update setiap 15 detik
        }

        function stopPolling() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            init();
        });

        // Cleanup when page unloads
        window.addEventListener('beforeunload', function() {
            stopPolling();
        });
    </script>
</body>
</html>