<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengundian Doorprize - Kobin Tiles</title>
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
            background: linear-gradient(135deg, #c8172d, #c8172d, rgb(255, 255, 255));
            color: white;
            min-height: 100vh;
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

        /* Voucher Card */
        .voucher-card {
            font-size: 1.1em;
            background: rgb(255, 255, 255);
            border-radius: 10px;
            margin: 5px;
            height: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            padding: 8px;
            font-family: Arial;
            color: black;
            font-weight: bold;
        }

        /* Voucher Card khusus untuk 1 pemenang */
        .voucher-card.single-winner {
            height: 150px;
            font-size: 1.3em;
            grid-column: 1 / -1;
            justify-self: center;
            width: 50%;
            max-width: 400px;
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

        /* Grid untuk voucher cards */
        .voucher-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 5px;
            margin: 7px 0;
        }

        /* Untuk layar kecil, turunkan jumlah kolom */
        @media (max-width: 1200px) {
            .voucher-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 900px) {
            .voucher-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 600px) {
            .voucher-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                margin: 10px 0;
                padding: 0 5px;
                width: 100%;
                box-sizing: border-box;
                overflow-x: hidden;
                justify-items: center;
            }
            
            .voucher-card {
                height: 90px;
                font-size: 0.9em;
                width: 100%;
                max-width: 180px;
            }
            
            .voucher-card.single-winner {
                width: 80%;
                height: 120px;
            }
        }

        .voucher-number {
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 1px;
            font-weight: bold;
            font-size: 1em;
            text-align: center;
            margin-bottom: 10px;
        }

        .voucher-info {
            font-size: 0.9em;
            opacity: 0.95;
            text-align: center;
            line-height: 1.2;
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
        
        /* Timer countdown */
        .timer {
            font-size: 1.5em;
            font-weight: bold;
            margin: 10px 0;
            color: #FFD700;
        }

        /* Styling untuk gambar doorprize */
        .doorprize-gallery {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .doorprize-item {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            width: 180px;
            height: 180px;
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
            padding: 5px;
            text-align: center;
            font-size: 0.75em;
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

        /* Control Card */
        .control-card {
            padding: 1px;
            width: 100%;
            max-width: 390px;
        }

        /* Card dengan background kuning */
        .bg-yellow-400 {
            background-color: #facc15;
        }

        .border-yellow-200 {
            border-color: #fef08a;
        }

        .border {
            border-width: 1px;
            border-style: solid;
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

        .text-black {
            color: #000000 !important;
        }

        /* Untuk layar kecil, ubah layout menjadi kolom */
        @media (max-width: 900px) {
            .main-layout {
                grid-template-columns: 1fr;
            }
            
            .doorprize-item {
                width: 100px;
                height: 100px;
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
                <!-- Gallery Doorprize -->
                <div class="doorprize-gallery" id="doorprizeGallery">
                    <!-- Gambar doorprize akan diisi oleh JavaScript -->
                </div>
                <br>
                
                <!-- Area Voucher -->
                <div id="voucherArea" class="hidden">
                    <div class="text-center mb-2">
                        <!-- Card dengan background kuning muda -->
                        <div class="bg-yellow-400 border border-yellow-200 rounded-lg p-4 mb-2 shadow-sm">
                            <h3 class="text-xl font-bold text-black" id="infoDoorprize">Pengundian Doorprize</h3>
                        </div>
                        
                        <div class="voucher-grid" id="voucherList">
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
                           class="w-80 h-80 object-contain">
                    </div>
                    
                    <!-- Hidden select untuk kompatibilitas -->
                    <select 
                        id="doorprize_id" 
                        class="hidden"
                    >
                        <option value="">-- Pilih Doorprize --</option>
                        @foreach($doorprizes as $doorprize)
                            <option value="{{ $doorprize->id }}">
                                {{ $doorprize->nama_doorprize }} ({{ $doorprize->jumlah_doorprize }} pemenang)
                            </option>
                        @endforeach
                    </select>

                    <!-- Timer Countdown -->
                    <div id="timerContainer" class="timer hidden text-center">
                        <span id="countdown">30</span> detik
                    </div>

                    <br>

                    <!-- Tombol Start/Stop -->
                    <button 
                        id="startStopBtn"
                        onclick="toggleUndian()"
                        disabled
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

        // Mapping nama doorprize ke file gambar
        const doorprizeImages = {
            'Air Fryer': 'airfyer.jpg',
            'Wireless Earbuds': 'earbuds.jpg',
            'Voucher': 'uangtunai.jpeg',
            'Hand Trolley': 'handtrolley.jpg',
            'Smart Watch': 'smartwatch.jpg',
            'Sepeda Motor Listrik': 'sepedamotorlistrik.jpeg'
        };

        // console.log('Doorprize Images Mapping:', doorprizeImages);

        // Inisialisasi gallery doorprize
        function initDoorprizeGallery() {
            const gallery = document.getElementById('doorprizeGallery');
            const doorprizeSelect = document.getElementById('doorprize_id');
            
            // Ambil opsi dari select
            const options = Array.from(doorprizeSelect.options).slice(1); // Skip opsi pertama
            
            options.forEach(option => {
                const doorprizeName = option.text.split(' (')[0];
                const imageFile = doorprizeImages[doorprizeName] || 'default.jpg';

                console.log(`Doorprize: ${doorprizeName}, Image File: ${imageFile}`);
                
                const doorprizeItem = document.createElement('div');
                doorprizeItem.className = 'doorprize-item';
                doorprizeItem.dataset.doorprizeId = option.value;
                
                // TAMBAHKAN INI: Beri ID khusus untuk Uang Tunai
                if (doorprizeName.includes('Voucher')) {
                    doorprizeItem.id = 'uang-tunai-item';
                    console.log('✅ Menambahkan ID uang-tunai-item ke elemen:', doorprizeName);
                }
                
                doorprizeItem.innerHTML = `
                    <img src="/images/doorprizes/${imageFile}" alt="${doorprizeName}">
                    <div class="doorprize-label">${doorprizeName}</div>
                `;
                
                doorprizeItem.addEventListener('click', function() {
                    const isAlreadySelected = this.classList.contains('selected');
                    const isVoucher = doorprizeName.includes('Voucher');
                    
                    // Hapus seleksi sebelumnya
                    document.querySelectorAll('.doorprize-item').forEach(item => {
                        item.classList.remove('selected');
                    });
                    
                    // Tandai yang dipilih
                    this.classList.add('selected');
                    
                    // Update select
                    doorprizeSelect.value = this.dataset.doorprizeId;
                    
                    // JIKA INI VOUCHER DAN SUDAH SELECTED SEBELUMNYA, RESET VOUCHER CARDS
                    if (isVoucher && isAlreadySelected) {
                        console.log('Voucher clicked while already selected - resetting cards');
                        resetVoucherCards();
                        
                        // Trigger generate ulang voucher cards
                        const jumlahPemenang = parseInt(option.text.match(/\((\d+)/)[1]);
                        generateVoucherCards(jumlahPemenang);
                        
                        // Update info doorprize
                        document.getElementById('infoDoorprize').textContent = 
                            `${doorprizeName} - ${jumlahPemenang} Pemenang`;
                    }
                    
                    // Trigger change event
                    doorprizeSelect.dispatchEvent(new Event('change'));
                });
                
                gallery.appendChild(doorprizeItem);
            });

            console.log('Gallery selesai diinisialisasi, memanggil autoSelectUangTunai...');
            setTimeout(autoSelectUangTunai, 100);
        }

        function autoSelectUangTunai() {
            console.log('Auto selecting Uang Tunai...');
            
            const doorprizeSelect = document.getElementById('doorprize_id');
            const uangTunaiItem = document.getElementById('uang-tunai-item');
            
            if (uangTunaiItem && doorprizeSelect) {
                // Hapus seleksi sebelumnya
                document.querySelectorAll('.doorprize-item').forEach(item => {
                    item.classList.remove('selected');
                });
                
                // Tandai Uang Tunai sebagai selected
                uangTunaiItem.classList.add('selected');
                
                // Update select value
                doorprizeSelect.value = uangTunaiItem.dataset.doorprizeId;
                
                // Reset voucher cards untuk Voucher
                if (isVoucherDoorprize()) {
                    resetVoucherCards();
                }
                
                // Trigger change event untuk memanggil semua fungsi yang diperlukan
                const changeEvent = new Event('change', { bubbles: true });
                doorprizeSelect.dispatchEvent(changeEvent);
                
                console.log('Uang Tunai berhasil dipilih secara visual');
            } else {
                console.log('Elemen Uang Tunai tidak ditemukan, retrying...');
                // Coba lagi setelah delay jika elemen belum ditemukan
                setTimeout(autoSelectUangTunai, 100);
            }
        }

        // Update tombol start berdasarkan pilihan doorprize
        document.getElementById('doorprize_id').addEventListener('change', function() {
            const startStopBtn = document.getElementById('startStopBtn');
            startStopBtn.disabled = !this.value;
            
            // Generate voucher cards placeholder
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const jumlahPemenang = parseInt(selectedOption.text.match(/\((\d+)/)[1]);
                const namaDoorprize = selectedOption.text.split(' (')[0];
                generateVoucherCards(jumlahPemenang);

                document.getElementById('infoDoorprize').textContent = `${namaDoorprize} - ${jumlahPemenang} Pemenang`;
                
                // Load pemenang yang sudah ada dari database, KECUALI untuk Voucher
                if (!isVoucherDoorprize()) {
                    loadExistingWinners(this.value);
                } else {
                    resetVoucherCards(); // Pastikan reset untuk Voucher
                }
            }
        });

        // Fungsi untuk generate voucher cards
        function generateVoucherCards(jumlah) {
            const voucherList = document.getElementById('voucherList');
            
            // Hanya generate jika jumlahnya berbeda atau belum ada cards
            const existingCards = voucherList.querySelectorAll('.voucher-card').length;
            if (existingCards !== jumlah) {
                voucherList.innerHTML = '';
                
                for (let i = 0; i < jumlah; i++) {
                    voucherList.innerHTML += `
                        <div class="voucher-card" id="voucher-${i}">
                            <div class="voucher-number">XXXX XXXX XXXX</div>
                            <div class="voucher-info">
                                <div>#####</div>
                                <div style="font-size: 0.6em;">#####</div>
                            </div>
                        </div>
                    `;
                }
                
                // Jika hanya ada 1 pemenang, berikan class khusus
                if (jumlah === 1) {
                    document.querySelector('.voucher-card').classList.add('single-winner');
                }
            }
            
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

            const doorprizeId = document.getElementById('doorprize_id').value;
            if (!doorprizeId) {
                // alert('Pilih doorprize terlebih dahulu!');
                console.log('Pilih doorprize terlebih dahulu!');
                return;
            }

            // RESET INFORMASI VOUCHER
            resetVoucherCards();

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

            const selectedOption = document.getElementById('doorprize_id').options[document.getElementById('doorprize_id').selectedIndex];
            const jumlahPemenang = parseInt(selectedOption.text.match(/\((\d+)/)[1]);
            const namaDoorprize = selectedOption.text.split(' (')[0];

            document.getElementById('infoDoorprize').textContent = `${namaDoorprize} - ${jumlahPemenang} Pemenang`;

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
            startRandomAnimation(jumlahPemenang);

            // Kirim request ke server untuk mendapatkan pemenang
            fetch(`/doorprize/${currentLokasi}/start`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    doorprize_id: doorprizeId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Simpan data pemenang untuk ditampilkan nanti
                    window.winnerData = data.vouchers;
                } else {
                    stopUndian();
                    // alert(data.message);
                    console.log(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                stopUndian();
                // alert('Terjadi kesalahan saat mengundi');
                console.log('Terjadi kesalahan saat mengundi');
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
                            // HANYA update nomor voucher, Toko dan PIC tetap *****
                            voucherElement.querySelector('.voucher-number').textContent = randomVoucher.nomor_voucher;
                            // Tidak update voucher-info selama animasi
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
                    
                    // Hanya tambah blink jika ini hasil undian baru (bukan loading existing)
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

        function isVoucherDoorprize() {
            const selectedOption = document.getElementById('doorprize_id').options[document.getElementById('doorprize_id').selectedIndex];
            const namaDoorprize = selectedOption.text.split(' (')[0];
            return namaDoorprize.includes('Voucher');
        }

        // Fungsi untuk load pemenang yang sudah ada saat doorprize dipilih
        async function loadExistingWinners(doorprizeId) {
            // JANGAN load existing winners jika doorprize adalah Voucher
            if (isVoucherDoorprize()) {
                console.log('Doorprize Voucher - Skip loading existing winners');
                resetVoucherCards();
                return;
            }

            try {
                const response = await fetch(`/doorprize/${currentLokasi}/winners-by-doorprize/${doorprizeId}`);
                const data = await response.json();
                
                if (data.success && data.winners.length > 0) {
                    console.log('Existing winners found:', data.winners);
                    showResult(data.winners);
                } else {
                    console.log('No existing winners found, resetting cards');
                    resetVoucherCards();
                }
            } catch (error) {
                console.error('Error loading existing winners:', error);
                resetVoucherCards();
            }
        }

        // Load jumlah voucher tersedia saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            initDoorprizeGallery();
            updateVoucherTersedia();
        });
    </script>
</body>
</html>