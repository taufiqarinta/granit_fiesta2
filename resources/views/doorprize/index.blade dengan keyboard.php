<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengundian Doorprize - Kobin Tiles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: rgb(255, 255, 255);
            color: white;
            min-height: 100vh;
            margin: 0;
            /* cursor: none; */
            padding: 10px;
        }

        .container {
            background: #cd0e2f;
            padding: 20px;
            border-radius: 25px;
            border: 1px solid rgba(255,255,255,0.1);
            max-width: 1400px;
            /* margin: 10px auto; */
        }

        /* Voucher Card yang lebih kecil */
        .voucher-card {
            font-size: 0.9em;
            background: linear-gradient(135deg, #DC143C, #B22222);
            border-radius: 10px;
            margin: 5px;
            height: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 8px;
        }

        /* Voucher Card khusus untuk 1 pemenang */
        .voucher-card.single-winner {
            height: 120px;
            font-size: 1.1em;
            grid-column: 1 / -1;
            justify-self: center;
            width: 50%;
            max-width: 300px;
        }

        .voucher-card.winner {
            animation: pulse 2s infinite;
            border: 2px solid #FFD700;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
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

        /* Grid untuk 5 card per baris */
        .voucher-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin: 15px 0;
        }

        /* Untuk layar kecil, turunkan menjadi 3 atau 2 card per baris */
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
            }
            
            .voucher-card {
                height: 70px;
                font-size: 0.8em;
            }
            
            .voucher-card.single-winner {
                width: 80%;
                height: 100px;
            }
        }

        .voucher-number {
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            font-weight: bold;
            font-size: 0.9em;
            text-align: center;
            margin-bottom: 2px;
        }

        .voucher-info {
            font-size: 0.65em;
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

        /* Header yang lebih compact */
        .compact-header {
            padding-top: 5px;
            margin-bottom: 10px;
        }

        .compact-header h1 {
            font-size: 1.8em;
            margin-bottom: 2px;
        }

        .compact-header p {
            font-size: 0.9em;
            margin-bottom: 5px;
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
            /* margin: 20px 0; */
        }

        .doorprize-item {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            width: 120px;
            height: 120px;
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
            width: 220px;
            height: 50px;
            border-radius: 10px;
            overflow: hidden;
            margin: 0 auto 20px;
        }

        .kobin-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Layout baru */
        .main-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .left-section {
            display: flex;
            flex-direction: column;
        }

        .right-section {
            /* background: rgba(255, 255, 255, 0.1); */
            /* border-radius: 15px; */
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            /* box-shadow: 0 4px 15px rgba(0,0,0,0.2); */
        }

        .control-card {
            /* background: rgb(157, 157, 157); */
            background: #8b8a8c;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
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
        }

        /* Styling untuk keyboard shortcut info */
        .keyboard-shortcuts {
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            font-size: 0.85em;
        }

        .keyboard-shortcuts h3 {
            margin-bottom: 10px;
            font-size: 1em;
            text-align: center;
        }

        .keyboard-shortcuts ul {
            list-style-type: none;
            padding: 0;
        }

        .keyboard-shortcuts li {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }

        .keyboard-key {
            display: inline-block;
            padding: 2px 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            margin-right: 8px;
            font-weight: bold;
            min-width: 20px;
            text-align: center;
        }

        /* Styling untuk feedback keyboard */
        .keyboard-feedback {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 1.2em;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }

        .keyboard-feedback.show {
            opacity: 1;
        }
    </style>
</head>
<body>
    <!-- Keyboard feedback element -->
    <div id="keyboardFeedback" class="keyboard-feedback"></div>

    <!-- Header dengan logo di tengah -->
    <div class="text-center compact-header">
        <div class="kobin-logo">
            <img src="/images/kobin-logo.jpg" alt="Kobin Tiles Logo">
        </div>
        <!-- <p class="opacity-80 text-sm">{{ date('d F Y') }} | <span id="voucherTersedia">Loading...</span></p> -->
    </div>

    <!-- Main Content dengan layout baru -->
    <div class="container">
        <div class="main-layout">
            <!-- Bagian Kiri: Gambar Doorprize dan Voucher -->
            <div class="left-section">
                <!-- Gallery Doorprize -->
                <div class="doorprize-gallery" id="doorprizeGallery">
                    <!-- Gambar doorprize akan diisi oleh JavaScript -->
                </div>
                
                <!-- Area Voucher -->
                <div id="voucherArea" class="hidden">
                    <div class="text-center mb-4">
                        <h3 class="text-xl font-bold mb-2 mt-4" id="infoDoorprize"></h3>
                        <div class="voucher-grid" id="voucherList">
                            <!-- Voucher cards akan di-generate oleh JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bagian Kanan: Informasi dan Kontrol -->
            <div class="right-section">
                <div class="control-card">
                    <h1 class="text-2xl font-bold text-center mb-2">🎁 Pengundian Doorprize</h1>
                    <p class="text-lg opacity-90 text-center mb-4">Kobin Tiles - Event {{ strtoupper($lokasi) }}</p>
                    
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

                    <!-- Tombol Start/Stop -->
                    <button 
                        id="startStopBtn"
                        onclick="toggleUndian()"
                        disabled
                        class="circle-btn start"
                    >
                        <div class="flex flex-col items-center">
                            <i class="fas fa-play text-xl mb-1"></i>
                            <span class="text-xs font-semibold">MULAI</span>
                        </div>
                    </button>
                    
                    <!-- Informasi Keyboard Shortcuts -->
                    <div class="keyboard-shortcuts">
                        <h3>Shortcut Keyboard</h3>
                        <ul>
                            <li><span class="keyboard-key">1</span> Uang Tunai</li>
                            <li><span class="keyboard-key">2</span> Air Fryer</li>
                            <li><span class="keyboard-key">3</span> Hand Trolley</li>
                            <li><span class="keyboard-key">4</span> Smart Watch</li>
                            <li><span class="keyboard-key">5</span> Sepeda Listrik</li>
                            <li><span class="keyboard-key">Enter</span> Mulai/Hentikan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading" class="hidden fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50">
        <div class="bg-white bg-opacity-10 rounded-lg p-6 text-center backdrop-blur-sm border border-red-600">
            <div class="loading mx-auto mb-3"></div>
            <p class="text-white text-md font-semibold">Memulai undian...</p>
        </div>
    </div>

    <script>
        let isRandomizing = false;
        let rollingIntervals = [];
        let allVouchersForAnimation = [];
        let countdownInterval;
        let remainingTime = 30;
        const currentLokasi = "{{ $lokasi }}";

        // Mapping nama doorprize ke file gambar dan keyboard shortcut
        const doorprizeImages = {
            'Air Fryer': 'airfyer.jpg',
            'Uang Tunai': 'uangtunai.jpeg',
            'Hand Trolley': 'handtrolley.jpg',
            'Smart Watch': 'smartwatch.jpg',
            'Sepeda Motor Listrik': 'sepedamotorlistrik.jpeg'
        };

        // Mapping keyboard shortcut ke nama doorprize
        const keyboardDoorprizeMap = {
            '1': 'Uang Tunai',
            '2': 'Air Fryer',
            '3': 'Hand Trolley',
            '4': 'Smart Watch',
            '5': 'Sepeda Motor Listrik'
        };

        // Inisialisasi gallery doorprize
        function initDoorprizeGallery() {
            const gallery = document.getElementById('doorprizeGallery');
            const doorprizeSelect = document.getElementById('doorprize_id');
            
            // Ambil opsi dari select
            const options = Array.from(doorprizeSelect.options).slice(1); // Skip opsi pertama
            
            options.forEach(option => {
                const doorprizeName = option.text.split(' (')[0];
                const imageFile = doorprizeImages[doorprizeName] || 'default.jpg';
                
                const doorprizeItem = document.createElement('div');
                doorprizeItem.className = 'doorprize-item';
                doorprizeItem.dataset.doorprizeId = option.value;
                doorprizeItem.dataset.doorprizeName = doorprizeName;
                
                doorprizeItem.innerHTML = `
                    <img src="/images/doorprizes/${imageFile}" alt="${doorprizeName}">
                    <div class="doorprize-label">${doorprizeName}</div>
                `;
                
                doorprizeItem.addEventListener('click', function() {
                    selectDoorprize(this);
                });
                
                gallery.appendChild(doorprizeItem);
            });
        }

        // Fungsi untuk memilih doorprize
        function selectDoorprize(element) {
            // Hapus seleksi sebelumnya
            document.querySelectorAll('.doorprize-item').forEach(item => {
                item.classList.remove('selected');
            });
            
            // Tandai yang dipilih
            element.classList.add('selected');
            
            // Update select
            const doorprizeSelect = document.getElementById('doorprize_id');
            doorprizeSelect.value = element.dataset.doorprizeId;
            
            // Trigger change event
            doorprizeSelect.dispatchEvent(new Event('change'));
            
            // Tampilkan feedback
            showKeyboardFeedback(`Dipilih: ${element.dataset.doorprizeName}`);
        }

        // Fungsi untuk menampilkan feedback keyboard
        function showKeyboardFeedback(message) {
            const feedback = document.getElementById('keyboardFeedback');
            feedback.textContent = message;
            feedback.classList.add('show');
            
            setTimeout(() => {
                feedback.classList.remove('show');
            }, 1500);
        }

        // Fungsi untuk memilih doorprize berdasarkan keyboard shortcut
        function selectDoorprizeByKey(key) {
            const doorprizeName = keyboardDoorprizeMap[key];
            if (!doorprizeName) return;
            
            const doorprizeItems = document.querySelectorAll('.doorprize-item');
            for (const item of doorprizeItems) {
                if (item.dataset.doorprizeName === doorprizeName) {
                    selectDoorprize(item);
                    return;
                }
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
                generateVoucherCards(jumlahPemenang);
                
                // Scroll ke area voucher
                document.getElementById('voucherArea').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        });

        // Fungsi untuk generate voucher cards
        function generateVoucherCards(jumlah) {
            const voucherList = document.getElementById('voucherList');
            voucherList.innerHTML = '';
            
            for (let i = 0; i < jumlah; i++) {
                voucherList.innerHTML += `
                    <div class="voucher-card" id="voucher-${i}">
                        <div class="voucher-number">XXXX XXXX XXXX</div>
                        <div class="voucher-info">
                            <div>Toko: *****</div>
                            <div>PIC: *****</div>
                        </div>
                    </div>
                `;
            }
            
            // Jika hanya ada 1 pemenang, berikan class khusus
            if (jumlah === 1) {
                document.querySelector('.voucher-card').classList.add('single-winner');
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
                alert('Pilih doorprize terlebih dahulu!');
                return;
            }

            isRandomizing = true;
            document.getElementById('loading').classList.remove('hidden');
            
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
            remainingTime = 30;
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
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                stopUndian();
                alert('Terjadi kesalahan saat mengundi');
            })
            .finally(() => {
                document.getElementById('loading').classList.add('hidden');
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
            vouchers.forEach((voucher, index) => {
                const voucherElement = document.getElementById(`voucher-${index}`);
                if (voucherElement) {
                    voucherElement.querySelector('.voucher-number').textContent = voucher.nomor_voucher;
                    voucherElement.querySelector('.voucher-info').innerHTML = `
                        <div>Toko: ${voucher.nama_toko}</div>
                        <div>PIC: ${voucher.nama_pic}</div>
                    `;
                    
                    voucherElement.classList.add('winner', 'blink');
                }
            });

            // Hentikan blink setelah beberapa detik
            setTimeout(() => {
                document.querySelectorAll('.voucher-card').forEach(card => {
                    card.classList.remove('blink');
                });
            }, 2000);
        }

        // Event listener untuk keyboard
        document.addEventListener('keydown', function(event) {
            // Jangan tangani jika sedang di input field
            if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') {
                return;
            }
            
            // Tombol angka 1-5 untuk memilih doorprize
            if (['1', '2', '3', '4', '5'].includes(event.key)) {
                selectDoorprizeByKey(event.key);
            }
            
            // Tombol Enter untuk memulai/menghentikan undian
            if (event.key === 'Enter') {
                const startStopBtn = document.getElementById('startStopBtn');
                if (!startStopBtn.disabled) {
                    toggleUndian();
                    showKeyboardFeedback(isRandomizing ? "Undian dihentikan" : "Undian dimulai");
                }
            }
        });

        // Load jumlah voucher tersedia saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            initDoorprizeGallery();
            updateVoucherTersedia();
        });
    </script>
</body>
</html>