<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengundian Doorprize Kehadiran - Kobin Tiles</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .w-80 {
            width: 400px;
        }

        .h-80 {
            height: 180px;
        }

        /* Atau jika ingin lebih besar lagi */
        .w-96 {
            width: 384px;
        }

        .h-96 {
            height: 384px;
        }
        /* Reset dan base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            color: white;
            min-height: 100vh;
            background-image: url('/images/bg-doorprize-kehadiran.webp');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center center;
        }

        .pita{
            background-image: url('/images/pita.png');
            background-repeat: no-repeat;
            background-size: 100% auto;
            background-position: center 59%;
            width: 100%;
            height: 140px;
        }

        .pita h3{
            padding-top: 50px;
        }

        .container {
            padding: 10px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Layout utama */
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

        /* Header kompak */
        .compact-header {
            padding-top: 5px;
        }

        .compact-header h1 {
            font-size: 1.8em;
        }

        .compact-header p {
            font-size: 0.9em;
        }

        /* Voucher Card untuk single winner (besar) - style tiket emas, disamakan dengan halaman non-by-id */
        .voucher-card.single-winner {
            font-size: 1.3em;
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            -webkit-mask: radial-gradient(circle at -10px 50%, transparent 20px, black 20.5px) left / 51% 100% no-repeat, radial-gradient(circle at calc(100% + 10px) 50%, transparent 20px, black 20.5px) right / 51% 100% no-repeat;
            mask: radial-gradient(circle at -10px 50%, transparent 20px, black 20.5px) left / 51% 100% no-repeat, radial-gradient(circle at calc(100% + 10px) 50%, transparent 20px, black 20.5px) right / 51% 100% no-repeat;
            border-radius: 10px;
            margin: 5px;
            height: 150px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            padding: 8px;
            font-family: Arial;
            color: black;
            font-weight: bold;
            width: 50%;
            max-width: 400px;
            margin: 0 auto;
        }

        .voucher-card.multiple-winner {
            font-size: 1em;
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            -webkit-mask: radial-gradient(circle at -10px 50%, transparent 20px, black 20.5px) left / 51% 100% no-repeat, radial-gradient(circle at calc(100% + 10px) 50%, transparent 20px, black 20.5px) right / 51% 100% no-repeat;
            mask: radial-gradient(circle at -10px 50%, transparent 20px, black 20.5px) left / 51% 100% no-repeat, radial-gradient(circle at calc(100% + 10px) 50%, transparent 20px, black 20.5px) right / 51% 100% no-repeat;
            border-radius: 10px;
            margin: 5px;
            height: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            padding: 6px;
            font-family: Arial;
            color: black;
            font-weight: bold;
            min-width: 180px;
            flex: 1;
        }

        voucher-top, .voucher-middle, .voucher-bottom {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .voucher-top {
            flex: 60;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
            font-weight: bold;
            font-family: Arial;
            font-size: 0.85rem;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .voucher-middle {
            flex: 0;
            vertical-align: middle;
            font-size: 0.9rem;
            color: #333;
        }

        .voucher-bottom {
            flex: 40;
            border-top: 1px dashed rgba(0, 0, 0, 0.1);
            font-size: 0.8rem;
            color: #555;
        }

        .voucher-card.winner {
            animation: pulse 2s infinite;
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

        /* Tombol lingkaran untuk start/stop - style gold border, disamakan dengan halaman non-by-id */
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
            color: white;
            border: 5px solid transparent;
            background-image: linear-gradient(#cd1c21, #cd1c21), linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            background-origin: border-box;
            background-clip: padding-box, border-box;
        }

        .circle-btn.stop {
            color: white;
            border: 5px solid transparent;
            background-image: linear-gradient(#cd1c21, #cd1c21), linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            background-origin: border-box;
            background-clip: padding-box, border-box;
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

        /* Styling untuk gambar doorprize */
        .doorprize-item {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            width: 300px;
            height: 300px;
            margin: 0 auto;
        }

        .doorprize-item img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 10px;
            transition: all 0.3s ease;
            background-image: url('/images/dudukan.png');
            background-repeat: no-repeat;
            background-position: bottom;
            background-size: contain;
            background-position: center 100px;
            padding-bottom: 30px;
        }


        .doorprize-item:hover {
            transform: translateY(-5px);
        }

        .doorprize-item:hover img {
            filter: brightness(1.1);
        }

        .doorprize-item.selected img {
            transform: scale(1.15);
            filter: brightness(1.2);
        }

        .doorprize-label {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            color: white;
            padding: 5px;
            text-align: center;
            font-size: 0.75em;
            font-weight: bold;
            border-radius: 10px;
            background-image: linear-gradient(#f01c28, #b71c1c), linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            background-origin: border-box;
            background-clip: padding-box, border-box;
            border: 3px solid transparent;
        }

        /* Logo Kobin */
        .kobin-logo {
            max-width: 220px;
            height: auto;
            display: block;
            margin: 0 auto;
            margin-bottom: 1rem;
        }

        /* Control Card */
        .control-card {
            padding: 1px;
            width: 100%;
            max-width: 390px;
        }

        /* Info Doorprize */
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

        /* Utility classes */
        .hidden {
            display: none !important;
        }

        .text-center {
            text-align: center;
        }

        .text-xl {
            font-size: 1.25rem;
        }

        .text-2xl {
            font-size: 1.5rem;
        }

        .font-bold {
            font-weight: bold;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .flex {
            display: flex;
        }

        .justify-center {
            justify-content: center;
        }

        .items-center {
            align-items: center;
        }

        .flex-col {
            flex-direction: column;
        }

        .object-contain {
            object-fit: contain;
        }

        .w-50 {
            width: 200px;
        }

        .h-50 {
            height: 200px;
        }

        .bg-yellow-400 {
            background-color: #facc15;
        }

        .border-yellow-200 {
            border-color: #fef08a;
        }

        .border {
            border-width: 1px;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .p-4 {
            padding: 1rem;
        }

        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .bg-yellow-400 .text-black {
            color: #000000 !important;
        }

        /* Atau lebih spesifik */
        .bg-yellow-400 h3.text-black {
            color: #000000 !important;
        }

        /* Style khusus untuk gambar default */
        .doorprize-item img[src*="default.jpg"] {
            object-fit: contain;
            padding: 30px;
            background-color: #f8f8f8;
        }

        /* Tambahan untuk label jika gambar tidak ditemukan */
        .doorprize-item img.error + .doorprize-label {
            background: rgba(220, 20, 60, 0.9);
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
                <!-- Gambar Doorprize -->
                <div class="doorprize-gallery" id="doorprizeGallery">
                    <!-- Gambar doorprize akan diisi oleh JavaScript -->
                </div>

                <br>
                <br>
                
                <!-- Area Voucher -->
                <div id="voucherArea" class="hidden">
                    <div class="text-center mb-2">
                        <!-- Card dengan background kuning muda -->
                        <div class="pita">
                            <h3 class="text-xl font-bold text-black" id="currentDoorprizeInfo">{{ $doorprize->jumlah_doorprize }} Pemenang</h3>
                        </div>

                        <br>
                                
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
                    
                    <!-- Hidden input untuk doorprize_id -->
                    <input type="hidden" id="doorprize_id" value="{{ $doorprize->id }}">

                    <!-- Tombol Start/Stop (countdown ditampilkan di dalam tombol ini) -->
                    <button 
                        id="startStopBtn"
                        onclick="toggleUndian()"
                        class="circle-btn start"
                        style= "position: fixed; right: 195px; top: 48%; transform: translateY(-47%); z-index: 100; margin: 0;"
                    >
                        <div class="flex flex-col items-center">
                            <i class="fas fa-play text-xl"></i>
                            <span class="text-xs font-semibold">MULAI</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tsparticles/confetti@3.0.3/tsparticles.confetti.bundle.min.js"></script>
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
        let cukupTokoTersedia = true;
        const currentLokasi = "{{ $lokasi }}";
        const currentDoorprizeId = {{ $doorprize->id }};
        const currentDoorprizeName = "{{ $doorprize->nama_doorprize }}";
        const currentJumlahPemenang = {{ $doorprize->jumlah_doorprize }};
        
        // Cek apakah ini doorprize Voucher
        const isVoucherDoorprize = currentDoorprizeName.includes('Voucher') || currentDoorprizeName.includes('Uang');

        // Inisialisasi gallery doorprize untuk single item
        function initSingleDoorprizeGallery() {
            const gallery = document.getElementById('doorprizeGallery');
            
            // Ambil data dari server (Laravel blade)
            const imageFile = "{{ $doorprize->nama_file ?: 'default.jpg' }}";
            const doorprizeName = "{{ $doorprize->nama_doorprize }}";
            const doorprizeId = {{ $doorprize->id }};
            
            const doorprizeItem = document.createElement('div');
            doorprizeItem.className = 'doorprize-item selected';
            doorprizeItem.dataset.doorprizeId = doorprizeId;
            doorprizeItem.dataset.imageFile = imageFile;
            
            // Gunakan onerror untuk fallback jika gambar tidak ditemukan
            doorprizeItem.innerHTML = `
                <img src="/images/doorprizes/${imageFile}" 
                    alt="${doorprizeName}" 
                    onerror="this.src='/images/doorprizes/default.jpg'; this.alt='Gambar tidak tersedia'">
                <div class="doorprize-label">${doorprizeName}</div>
            `;
            
            // Tambahkan event click untuk refresh card
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

        // Fungsi untuk update gambar doorprize (jika diperlukan secara dinamis)
        function updateDoorprizeImage(newImageFile, newName) {
            const gallery = document.getElementById('doorprizeGallery');
            const existingItem = gallery.querySelector('.doorprize-item');
            
            if (existingItem) {
                const img = existingItem.querySelector('img');
                const label = existingItem.querySelector('.doorprize-label');
                
                if (img) {
                    img.src = `/images/doorprizes/${newImageFile}`;
                    img.alt = newName;
                    // Reset onerror handler
                    img.onerror = function() {
                        this.src = '/images/doorprizes/default.jpg';
                        this.alt = 'Gambar tidak tersedia';
                    };
                }
                
                if (label) {
                    label.textContent = newName;
                }
                
                // Update dataset
                existingItem.dataset.imageFile = newImageFile;
            }
        }

        // Fungsi untuk refresh voucher cards (kosongkan semua)
        function refreshVoucherCards() {
            resetVoucherCards();
        }

        function checkImageExists(imagePath) {
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => resolve(true);
                img.onerror = () => resolve(false);
                img.src = imagePath;
            });
        }

        // Fungsi untuk memuat gambar dengan fallback
        async function loadDoorprizeImage(imageFile, doorprizeName) {
            const imagePath = `/images/doorprizes/${imageFile}`;
            const exists = await checkImageExists(imagePath);
            
            if (!exists) {
                console.warn(`Gambar ${imageFile} tidak ditemukan, menggunakan default`);
                return `/images/doorprizes/default.jpg`;
            }
            
            return imagePath;
        }

        // Fungsi untuk generate voucher cards dengan layout yang sesuai (struktur top/bottom)
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
                        <div class="voucher-top">XXXXXXXX</div>
                        <div class="voucher-bottom">XXXXXXXX</div>
                    </div>
                `;
            }
            
            voucherContainer.appendChild(container);
            document.getElementById('voucherArea').classList.remove('hidden');
        }

        // Fungsi untuk cek jumlah toko tersedia dan disable tombol jika tidak cukup
        function updateTokoTersedia() {
            fetch(`/doorprize-kehadiran/${currentLokasi}/toko-tersedia`)
                .then(response => response.json())
                .then(data => {
                    const startStopBtn = document.getElementById('startStopBtn');
                    cukupTokoTersedia = data.tersedia >= currentJumlahPemenang;
                    startStopBtn.disabled = !cukupTokoTersedia;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Fungsi untuk toggle start/stop undian
        function toggleUndian() {
            if (isRandomizing) {
                // Confetti pojok bawah kanan
                confetti({
                    particleCount: 500,
                    spread: 90,
                    origin: { x: 1, y: 0.9 },
                });

                // Confetti pojok bawah kiri
                confetti({
                    particleCount: 500,
                    spread: 90,
                    origin: { x: 0, y: 0.9 },
                });

                // Confetti pojok atas kanan
                confetti({
                    particleCount: 500,
                    spread: 90,
                    origin: { x: 1, y: 0.1 },
                });

                // Confetti pojok atas kiri
                confetti({
                    particleCount: 500,
                    spread: 90,
                    origin: { x: 0, y: 0.1 },
                });
                stopUndian();
            } else {
                if (!cukupTokoTersedia) {
                    alert('Maaf, jumlah toko yang tersedia tidak cukup untuk mengundi ' + currentJumlahPemenang + ' pemenang.');
                    return;
                }
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

            // Bersihkan data pemenang lama agar hasil lama tidak ikut ditampilkan
            window.winnerData = null;
            
            // Ubah tombol menjadi stop
            const startStopBtn = document.getElementById('startStopBtn');
            startStopBtn.classList.remove('start');
            startStopBtn.classList.add('stop');

            remainingTime = 10;
            startStopBtn.innerHTML = `
                <div class="flex flex-col items-center">
                    <span style="font-size: 3em; font-weight: bold; line-height: 1;">${remainingTime}</span>
                </div>
            `;
            
            // Mulai countdown, update angka di dalam tombol tiap detik
            countdownInterval = setInterval(() => {
                remainingTime--;

                if (remainingTime > 0) {
                    startStopBtn.innerHTML = `
                        <div class="flex flex-col items-center">
                            <span style="font-size: 3em; font-weight: bold; line-height: 1;">${remainingTime}</span>
                        </div>
                    `;
                }
                
                if (remainingTime <= 0) {
                    // Confetti pojok bawah kanan
                    confetti({
                        particleCount: 500,
                        spread: 90,
                        origin: { x: 1, y: 0.9 },
                    });

                    // Confetti pojok bawah kiri
                    confetti({
                        particleCount: 500,
                        spread: 90,
                        origin: { x: 0, y: 0.9 },
                    });

                    // Confetti pojok atas kanan
                    confetti({
                        particleCount: 500,
                        spread: 90,
                        origin: { x: 1, y: 0.1 },
                    });

                    // Confetti pojok atas kiri
                    confetti({
                        particleCount: 500,
                        spread: 90,
                        origin: { x: 0, y: 0.1 },
                    });

                    stopUndian();
                }
            }, 1000);

            // Update info doorprize
            document.getElementById('currentDoorprizeInfo').textContent = 
                `${currentDoorprizeName} - ${currentJumlahPemenang} Pemenang`;

            // Load data untuk animasi
            if (allVouchersForAnimation.length === 0) {
                try {
                    const response = await fetch(`/doorprize-kehadiran/${currentLokasi}/animation-toko`);
                    allVouchersForAnimation = await response.json();
                } catch (error) {
                    console.error('Error loading animation vouchers:', error);
                }
            }

            // Mulai animasi random
            startRandomAnimation(currentJumlahPemenang);

            // Kirim request ke server untuk mendapatkan pemenang
            fetch(`/doorprize-kehadiran/${currentLokasi}/${currentDoorprizeId}/start-single`, {
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
                    // Simpan data pemenang untuk ditampilkan saat countdown selesai
                    window.winnerData = data.vouchers;
                } else {
                    stopUndian();
                    alert(data.message || 'Terjadi kesalahan saat mengundi');
                    console.log(data.message);
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
                updateTokoTersedia();
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
                            const top = voucherElement.querySelector('.voucher-top');
                            const bottom = voucherElement.querySelector('.voucher-bottom');

                            if (top) top.textContent = randomVoucher.nama_toko || 'XXXXXXXX';
                            if (bottom) bottom.textContent = randomVoucher.nama_pic || 'XXXXXXXX';
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
                    const top = voucherElement.querySelector('.voucher-top');
                    const bottom = voucherElement.querySelector('.voucher-bottom');

                    if (top) top.textContent = voucher.nama_toko;
                    if (bottom) bottom.textContent = voucher.nama_pic;
                    
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
                const top = card.querySelector('.voucher-top');
                const bottom = card.querySelector('.voucher-bottom');

                if (top) top.textContent = 'XXXXXXXX';
                if (bottom) bottom.textContent = 'XXXXXXXX';

                card.classList.remove('winner', 'blink');
            });
        }

        // Load jumlah toko tersedia saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            initSingleDoorprizeGallery();
            generateVoucherCards(currentJumlahPemenang);
            updateTokoTersedia();
            
            // Load pemenang yang sudah ada dari database, KECUALI untuk Voucher
            if (!isVoucherDoorprize) {
                loadExistingWinners();
            } else {
                console.log('Voucher doorprize - skip loading existing winners');
                refreshVoucherCards();
            }
        });

        // Fungsi untuk load pemenang yang sudah ada
        async function loadExistingWinners() {
            try {
                const response = await fetch(`/doorprize-kehadiran/${currentLokasi}/winners-by-doorprize/${currentDoorprizeId}`);
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

        // Shortcut tombol Spasi untuk memulai/menghentikan undian
        document.addEventListener('keydown', function(e) {
            if (e.code === 'Space' || e.key === ' ') {
                e.preventDefault();
                toggleUndian();
            }
        });
    </script>
</body>
</html>