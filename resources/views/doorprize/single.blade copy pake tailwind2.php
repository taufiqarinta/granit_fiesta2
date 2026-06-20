<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengundian Doorprize - Kobin Tiles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        *{
            cursor: none;
        }

        button, .doorprize-item, select {
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
            background: linear-gradient(135deg, #c8172d, #c8172d,rgb(255, 255, 255));
            color: white;
            min-height: 100vh;
        }

        .container {
            padding: 10px;
            max-width: 1400px;
            margin :0 ;
        }

        /* Voucher Card untuk single winner (besar) */
        .voucher-card.single-winner {
            font-size: 1.3em;
            background: rgb(255, 255, 255);
            border-radius: 10px;
            margin: 5px;
            height: 150px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            padding: 8px;
            font-family : Arial;
            color: black;
            font-weight : bold;
            width: 50%;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Voucher Card untuk multiple winners (kecil, horizontal) */
        .voucher-card.multiple-winner {
            font-size: 0.9em;
            background: rgb(255, 255, 255);
            border-radius: 10px;
            margin: 5px;
            height: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            padding: 6px;
            font-family : Arial;
            color: black;
            font-weight : bold;
            min-width: 180px;
            flex: 1;
        }

        .voucher-card.winner {
            animation: pulse 2s infinite;
            border: 2px solid #FFD700;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }

        .blink {
            animation: blink 0.5s infinite alternate;
        }

        @keyframes blink {
            from { opacity: 1; }
            to { opacity: 0.7; }
        }

        .voucher-number {
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 1px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
        }

        .voucher-card.single-winner .voucher-number {
            font-size: 1.2em;
        }

        .voucher-card.multiple-winner .voucher-number {
            font-size: 0.9em;
        }

        .voucher-info {
            opacity: 0.95;
            text-align: center;
            line-height: 1.2;
        }

        .voucher-card.single-winner .voucher-info {
            font-size: 1em;
        }

        .voucher-card.multiple-winner .voucher-info {
            font-size: 0.8em;
        }

        /* Container untuk multiple winners (horizontal layout) */
        .vouchers-horizontal {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin: 10px 0;
            max-width: 100%;
        }

        /* Grid layout untuk jumlah tertentu */
        .vouchers-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 10px 0;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .vouchers-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 10px 0;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .vouchers-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 10px 0;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        .vouchers-grid-5 {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin: 10px 0;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Tombol lingkaran untuk start/stop */
        .circle-btn {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            border: none;
            margin: 20px auto;
            padding: 15px;
        }

        .circle-btn .flex {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .circle-btn i {
            font-size: 1.5em;
            margin-bottom: 4px;
        }

        .circle-btn span {
            font-size: 0.75em;
            line-height: 1;
        }

        .circle-btn.start {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
        }

        .circle-btn.stop {
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: white;
        }

        .circle-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 15px rgba(0,0,0,0.4);
        }

        .circle-btn:disabled {
            background: #666;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .loading {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid #DC143C;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .header-icon {
            background: linear-gradient(135deg, #DC143C, #B22222);
            box-shadow: 0 4px 12px rgba(220, 20, 60, 0.4);
        }

        select {
            background-color: #f5f5f5 !important;
            color: #1a1a1a !important;
            border: 2px solid #DC143C !important;
        }

        select:focus {
            outline: none;
            border-color: #FF1744 !important;
            box-shadow: 0 0 0 3px rgba(220, 20, 60, 0.2) !important;
        }

        select option {
            background-color: white;
            color: #1a1a1a;
        }

        .btn-green {
            background: linear-gradient(135deg, #DC143C, #8B0000);
        }

        .btn-green:hover {
            background: linear-gradient(135deg, #FF1744, #DC143C);
        }

        /* Header yang lebih compact */
        .compact-header {
            padding-top: 5px;
        }

        .compact-header h1 {
            font-size: 1.8em;
        }

        .compact-header p {
            font-size: 0.9em;
        }
        
        /* Timer countdown */
        .timer {
            font-size: 1.5em;
            font-weight: bold;
            margin: 10px 0;
            color: #FFD700;
        }

        /* Styling untuk gambar doorprize */
        .doorprize-item {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            width: 200px;
            height: 200px;
            margin: 0 auto;
        }

        .doorprize-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .doorprize-item:hover {
            transform: translateY(-5px);
        }

        .doorprize-item:hover img {
            filter: brightness(1.1);
            box-shadow: 0 5px 15px rgba(220, 20, 60, 0.4);
        }

        .doorprize-item.selected {
            transform: scale(1.1);
            border: 3px solid #FFD700;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.6);
        }

        .doorprize-item.selected img {
            filter: brightness(1.2);
        }

        .doorprize-label {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 0.9em;
            font-weight: bold;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        /* Logo Kobin */
        .kobin-logo {
            max-width: 220px;
            height: auto;
            display: block;
            margin: 0 auto;
            margin-bottom: 1rem;
        }

        /* Layout baru dengan right section lebih kecil */
        .main-layout {
            display: grid;
            grid-template-columns: 3fr 1fr;
            gap: 10px;
        }

        .left-section {
            display: flex;
            flex-direction: column;
        }

        .right-section {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
        }

        .control-card {
            padding: 1px;
            width: 100%;
            max-width: 390px;
        }

        /* Untuk layar kecil, ubah layout menjadi kolom */
        @media (max-width: 900px) {
            .main-layout {
                grid-template-columns: 1fr;
            }
            
            .doorprize-item {
                width: 150px;
                height: 150px;
            }
            
            .voucher-card.single-winner {
                width: 80%;
                height: 120px;
                font-size: 1.1em;
            }
            
            .voucher-card.multiple-winner {
                height: 90px;
                font-size: 0.8em;
                min-width: 140px;
            }
            
            /* Adjust grid untuk mobile */
            .vouchers-grid-2,
            .vouchers-grid-3,
            .vouchers-grid-4,
            .vouchers-grid-5 {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                max-width: 100%;
                padding: 0 10px;
            }
            
            .circle-btn {
                width: 90px;
                height: 90px;
            }
            
            .circle-btn i {
                font-size: 1.3em;
            }
            
            .circle-btn span {
                font-size: 0.7em;
            }
            
            .control-card {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .vouchers-grid-2,
            .vouchers-grid-3,
            .vouchers-grid-4,
            .vouchers-grid-5 {
                grid-template-columns: 1fr;
                gap: 6px;
            }
            
            .voucher-card.multiple-winner {
                height: 80px;
                font-size: 0.75em;
                min-width: auto;
                width: 100%;
                max-width: 250px;
                margin: 0 auto;
            }
        }

        /* Utility classes */
        .hidden {
            display: none !important;
        }

        .single-doorprize-info {
            text-align: center;
            margin: 15px 0;
        }

        .single-doorprize-info h2 {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .single-doorprize-info p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        /* Refresh animation */
        .refresh-animation {
            animation: refreshSpin 0.5s ease-in-out;
        }

        @keyframes refreshSpin {
            0% { transform: scale(1); }
            50% { transform: scale(0.95); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="custom-cursor" id="cursor"></div>

    <br>
    
    <!-- Main Content dengan layout baru -->
    <div class="container">
        <div class="main-layout">
            <!-- Bagian Kiri: Gambar Doorprize dan Voucher -->
            <div class="left-section">
                <!-- Info Doorprize yang Dipilih -->
                <div class="single-doorprize-info">
                    <h2 id="infoDoorprize">{{ $doorprize->nama_doorprize }}</h2>
                    <p>{{ $doorprize->jumlah_doorprize }} Pemenang</p>
                </div>
                
                <!-- Gambar Doorprize -->
                <div class="doorprize-gallery" id="doorprizeGallery">
                    <!-- Gambar doorprize akan diisi oleh JavaScript -->
                </div>
                <br>
                
                <!-- Area Voucher -->
                <div id="voucherArea" class="hidden">
                    <div class="text-center mb-2">
                        <!-- Card dengan background kuning muda -->
                        <div class="bg-yellow-400 border border-yellow-200 rounded-lg p-4 mb-2 shadow-sm">
                            <h3 class="text-xl font-bold text-black" id="currentDoorprizeInfo">Pengundian Doorprize</h3>
                        </div>
                        
                        <!-- Container untuk voucher cards -->
                        <div id="voucherContainer">
                            <!-- Voucher cards akan di-generate oleh JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bagian Kanan: Informasi dan Kontrol -->
            <div class="right-section">
                <div class="control-card">
                    <img src="/images/kobin-logo.png" alt="Kobin Tiles Logo" class="kobin-logo">
                    <h1 class="text-2xl font-bold text-center mb-2">🎁 Pengundian Doorprize</h1>

                    <div class="flex justify-center mb-4">
                        <img src="{{ asset('images/gambar-granit-fiesta.png') }}" 
                            alt="Doorprize" 
                            class="w-50 h-50 object-contain">
                    </div>
                    
                    <!-- Hidden input untuk doorprize_id -->
                    <input type="hidden" id="doorprize_id" value="{{ $doorprize->id }}">

                    <!-- Timer Countdown -->
                    <div id="timerContainer" class="timer hidden text-center">
                        <span id="countdown">30</span> detik
                    </div>

                    <br>

                    <!-- Tombol Start/Stop -->
                    <button 
                        id="startStopBtn"
                        onclick="toggleUndian()"
                        class="circle-btn start"
                    >
                        <div class="flex flex-col items-center">
                            <i class="fas fa-play text-xl"></i>
                            <span class="text-xs font-semibold">MULAI</span>
                        </div>
                    </button>

                    <div class="flex justify-center" style="position: relative;">
                        <img src="{{ asset('images/gambar-hadiah.png') }}" 
                            alt="Doorprize" 
                            style="position: relative; max-width: 410px; object-fit: contain;">
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        const cursor = document.getElementById('cursor');
        document.addEventListener('mousemove', e => {
            cursor.style.left = e.pageX + 'px';
            cursor.style.top = e.pageY + 'px';
        });
        
        let isRandomizing = false;
        let rollingIntervals = [];
        let allVouchersForAnimation = [];
        let countdownInterval;
        let remainingTime = 10;
        const currentLokasi = "{{ $lokasi }}";
        const currentDoorprizeId = {{ $doorprize->id }};
        const currentDoorprizeName = "{{ $doorprize->nama_doorprize }}";
        const currentJumlahPemenang = {{ $doorprize->jumlah_doorprize }};
        
        // Cek apakah ini doorprize Voucher
        const isVoucherDoorprize = currentDoorprizeName.includes('Voucher') || currentDoorprizeId === 1;

        // Mapping nama doorprize ke file gambar
        const doorprizeImages = {
            'Air Fryer': 'airfyer.jpg',
            'Wireless Earbuds': 'earbuds.jpg',
            'Voucher': 'uangtunai.jpeg',
            'Hand Trolley': 'handtrolley.jpg',
            'Smart Watch': 'smartwatch.jpg',
            'Sepeda Motor Listrik': 'sepedamotorlistrik.jpeg'
        };

        // Inisialisasi gallery doorprize untuk single item
        function initSingleDoorprizeGallery() {
            const gallery = document.getElementById('doorprizeGallery');
            const imageFile = doorprizeImages[currentDoorprizeName] || 'default.jpg';
            
            const doorprizeItem = document.createElement('div');
            doorprizeItem.className = 'doorprize-item selected';
            doorprizeItem.dataset.doorprizeId = currentDoorprizeId;
            
            doorprizeItem.innerHTML = `
                <img src="/images/doorprizes/${imageFile}" alt="${currentDoorprizeName}">
                <div class="doorprize-label">${currentDoorprizeName}</div>
            `;
            
            // Tambahkan event click khusus untuk refresh card (terutama untuk Voucher)
            doorprizeItem.addEventListener('click', function() {
                refreshVoucherCards();
                
                // Tambahkan animasi refresh
                this.classList.add('refresh-animation');
                setTimeout(() => {
                    this.classList.remove('refresh-animation');
                }, 500);
            });
            
            gallery.appendChild(doorprizeItem);
        }

        // Fungsi untuk refresh voucher cards (kosongkan semua)
        function refreshVoucherCards() {
            console.log('Refreshing voucher cards...');
            resetVoucherCards();
            
            // Untuk Voucher, kita reset ke state awal (kosong)
            if (isVoucherDoorprize) {
                const voucherCards = document.querySelectorAll('.voucher-card');
                voucherCards.forEach(card => {
                    card.querySelector('.voucher-number').textContent = 'XXXX XXXX XXXX';
                    card.querySelector('.voucher-info').innerHTML = `
                        <div>#####</div>
                        <div style="font-size: 0.6em;">#####</div>
                    `;
                    card.classList.remove('winner', 'blink');
                });
            }
        }

        // Fungsi untuk generate voucher cards dengan layout yang sesuai
        function generateVoucherCards(jumlah) {
            const voucherContainer = document.getElementById('voucherContainer');
            voucherContainer.innerHTML = '';
            
            // Tentukan layout berdasarkan jumlah pemenang
            let containerClass = '';
            let cardClass = '';
            
            if (jumlah === 1) {
                containerClass = 'single-winner-container';
                cardClass = 'voucher-card single-winner';
            } else {
                // Gunakan grid layout berdasarkan jumlah
                if (jumlah === 2) {
                    containerClass = 'vouchers-grid-2';
                } else if (jumlah === 3) {
                    containerClass = 'vouchers-grid-3';
                } else if (jumlah === 4) {
                    containerClass = 'vouchers-grid-4';
                } else {
                    containerClass = 'vouchers-grid-5';
                }
                cardClass = 'voucher-card multiple-winner';
            }
            
            // Buat container
            const container = document.createElement('div');
            container.className = containerClass;
            container.id = 'voucherList';
            
            // Generate cards
            for (let i = 0; i < jumlah; i++) {
                container.innerHTML += `
                    <div class="${cardClass}" id="voucher-${i}">
                        <div class="voucher-number">XXXX XXXX XXXX</div>
                        <div class="voucher-info">
                            <div>#####</div>
                            <div style="font-size: 0.6em;">#####</div>
                        </div>
                    </div>
                `;
            }
            
            voucherContainer.appendChild(container);
            document.getElementById('voucherArea').classList.remove('hidden');
        }

        // Fungsi untuk update jumlah voucher tersedia
        function updateVoucherTersedia() {
            fetch(`/doorprize/${currentLokasi}/voucher-tersedia`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('voucherTersedia').textContent = 
                        `Voucher tersedia di Event ${data.lokasi}: ${data.tersedia}`;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Fungsi untuk toggle start/stop undian
        function toggleUndian() {
            if (isRandomizing) {
                stopUndian();
            } else {
                startUndian();
            }
        }

        // Fungsi untuk memulai undian
        async function startUndian() {
            if (isRandomizing) return;

            // RESET INFORMASI VOUCHER (terutama untuk Voucher)
            if (isVoucherDoorprize) {
                resetVoucherCards();
            }

            isRandomizing = true;
            
            // Ubah tombol menjadi stop
            const startStopBtn = document.getElementById('startStopBtn');
            startStopBtn.innerHTML = `
                <div class="flex flex-col items-center">
                    <i class="fas fa-stop text-xl mb-1"></i>
                    <span class="text-xs font-semibold">STOP</span>
                </div>
            `;
            startStopBtn.classList.remove('start');
            startStopBtn.classList.add('stop');
            
            // Tampilkan timer
            document.getElementById('timerContainer').classList.remove('hidden');
            remainingTime = 10;
            document.getElementById('countdown').textContent = remainingTime;
            
            // Mulai countdown
            countdownInterval = setInterval(() => {
                remainingTime--;
                document.getElementById('countdown').textContent = remainingTime;
                
                if (remainingTime <= 0) {
                    stopUndian();
                }
            }, 1000);

            // Update info doorprize
            document.getElementById('currentDoorprizeInfo').textContent = 
                `${currentDoorprizeName} - ${currentJumlahPemenang} Pemenang`;

            // Load data untuk animasi
            if (allVouchersForAnimation.length === 0) {
                try {
                    const response = await fetch(`/doorprize/${currentLokasi}/animation-vouchers`);
                    allVouchersForAnimation = await response.json();
                } catch (error) {
                    console.error('Error loading animation vouchers:', error);
                }
            }

            // Mulai animasi random
            startRandomAnimation(currentJumlahPemenang);

            // Kirim request ke server untuk mendapatkan pemenang
            fetch(`/doorprize/${currentLokasi}/start`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    doorprize_id: currentDoorprizeId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Simpan data pemenang untuk ditampilkan nanti
                    window.winnerData = data.vouchers;
                } else {
                    stopUndian();
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                stopUndian();
                alert('Terjadi kesalahan saat mengundi');
            });
        }

        // Fungsi untuk menghentikan undian
        function stopUndian() {
            if (!isRandomizing) return;
            
            isRandomizing = false;
            
            // Hentikan countdown
            clearInterval(countdownInterval);
            document.getElementById('timerContainer').classList.add('hidden');
            
            // Ubah tombol kembali ke start
            const startStopBtn = document.getElementById('startStopBtn');
            startStopBtn.innerHTML = `
                <div class="flex flex-col items-center">
                    <i class="fas fa-play text-xl mb-1"></i>
                    <span class="text-xs font-semibold">MULAI</span>
                </div>
            `;
            startStopBtn.classList.remove('stop');
            startStopBtn.classList.add('start');
            
            // Hentikan animasi random
            stopRandomAnimation();
            
            // Tampilkan hasil
            if (window.winnerData) {
                showResult(window.winnerData);
                updateVoucherTersedia();
            }
        }

        // Fungsi untuk animasi random
        function startRandomAnimation(jumlahPemenang) {
            rollingIntervals.forEach(interval => clearInterval(interval));
            rollingIntervals = [];

            for (let i = 0; i < jumlahPemenang; i++) {
                rollingIntervals[i] = setInterval(() => {
                    if (allVouchersForAnimation.length > 0) {
                        const randomVoucher = allVouchersForAnimation[Math.floor(Math.random() * allVouchersForAnimation.length)];
                        const voucherElement = document.getElementById(`voucher-${i}`);
                        
                        if (voucherElement) {
                            voucherElement.querySelector('.voucher-number').textContent = randomVoucher.nomor_voucher;
                        }
                    }
                }, 100);
            }
        }

        function stopRandomAnimation() {
            rollingIntervals.forEach(interval => clearInterval(interval));
            rollingIntervals = [];
        }

        // Fungsi untuk menampilkan hasil
        function showResult(vouchers) {
            // Reset semua kartu terlebih dahulu
            resetVoucherCards();
            
            // Isi dengan data pemenang
            vouchers.forEach((voucher, index) => {
                const voucherElement = document.getElementById(`voucher-${index}`);
                if (voucherElement) {
                    voucherElement.querySelector('.voucher-number').textContent = voucher.nomor_voucher;
                    voucherElement.querySelector('.voucher-info').innerHTML = `
                        <div>${voucher.nama_toko}</div>
                        <div style="font-size: 0.7em;">${voucher.nama_pic}</div>
                    `;
                    
                    voucherElement.classList.add('winner');
                    
                    if (!vouchers.isExisting) {
                        voucherElement.classList.add('blink');
                    }
                }
            });

            // Hentikan blink setelah beberapa detik (hanya untuk undian baru)
            if (!vouchers.isExisting) {
                setTimeout(() => {
                    document.querySelectorAll('.voucher-card').forEach(card => {
                        card.classList.remove('blink');
                    });
                }, 2000);
            }
        }

        // Update fungsi resetVoucherCards
        function resetVoucherCards() {
            const voucherCards = document.querySelectorAll('.voucher-card');
            voucherCards.forEach(card => {
                card.querySelector('.voucher-number').textContent = 'XXXX XXXX XXXX';
                card.querySelector('.voucher-info').innerHTML = `
                    <div>#####</div>
                    <div style="font-size: 0.6em;">#####</div>
                `;
                card.classList.remove('winner', 'blink');
            });
        }

        // Load jumlah voucher tersedia saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            initSingleDoorprizeGallery();
            generateVoucherCards(currentJumlahPemenang);
            updateVoucherTersedia();
            
            // Load pemenang yang sudah ada dari database, KECUALI untuk Voucher
            if (!isVoucherDoorprize) {
                loadExistingWinners();
            } else {
                console.log('Voucher doorprize - skip loading existing winners');
                // Untuk Voucher, pastikan cards dalam keadaan kosong
                refreshVoucherCards();
            }
        });

        // Fungsi untuk load pemenang yang sudah ada
        async function loadExistingWinners() {
            try {
                const response = await fetch(`/doorprize/${currentLokasi}/winners-by-doorprize/${currentDoorprizeId}`);
                const data = await response.json();
                
                if (data.success && data.winners.length > 0) {
                    console.log('Existing winners found:', data.winners);
                    showResult(data.winners);
                } else {
                    console.log('No existing winners found');
                }
            } catch (error) {
                console.error('Error loading existing winners:', error);
            }
        }
    </script>
</body>
</html>