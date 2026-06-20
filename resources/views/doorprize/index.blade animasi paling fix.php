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
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
            color: white;
            min-height: 100vh;
            margin: 0;
            padding: 10px;
        }

        .container {
            background: rgba(255,255,255,0.05);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.1);
            max-width: 1400px;
            margin: 10px auto;
        }

        /* Voucher Card yang lebih kecil */
        .voucher-card {
            font-size: 0.9em;
            background: linear-gradient(135deg, #DC143C, #B22222);
            border-radius: 10px;
            margin: 5px;
            height: 80px; /* Diperkecil dari 120px */
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
            grid-template-columns: repeat(5, 1fr); /* 5 card per baris */
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
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            border: none;
            margin: 20px auto;
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
    </style>
</head>
<body>
    <!-- Header yang lebih compact -->
    <div class="text-center compact-header">
        <div class="flex items-center justify-center mb-2">
            <div class="w-12 h-12 header-icon rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-gift text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold">🎁 Pengundian Doorprize</h1>
                <p class="text-lg opacity-90">Kobin Tiles - Event {{ strtoupper($lokasi) }}</p>
            </div>
        </div>
        <p class="opacity-80 text-sm">{{ date('d F Y') }} | <span id="voucherTersedia">Loading...</span></p>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Pilih Doorprize -->
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold mb-3">Pilih Doorprize</h2>
            
            <div class="max-w-md mx-auto mb-4">
                <select 
                    id="doorprize_id" 
                    class="w-full px-3 py-2 rounded-lg transition-colors text-sm"
                >
                    <option value="">-- Pilih Doorprize --</option>
                    @foreach($doorprizes as $doorprize)
                        <option value="{{ $doorprize->id }}">
                            {{ $doorprize->nama_doorprize }} ({{ $doorprize->jumlah_doorprize }} pemenang)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Timer Countdown -->
            <!-- <div id="timerContainer" class="timer hidden">
                <span id="countdown">30</span> detik
            </div> -->

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
        </div>

        <!-- Area Voucher -->
        <div id="voucherArea" class="hidden">
            <div class="text-center mb-4">
                <h3 class="text-xl font-bold mb-2" id="infoDoorprize"></h3>
                <div class="voucher-grid" id="voucherList">
                    <!-- Voucher cards akan di-generate oleh JavaScript -->
                </div>
            </div>

            <!-- Tombol Undi Lagi -->
            <!-- <div class="text-center mt-6">
                <button onclick="startUndian()" class="btn-green">
                    <i class="fas fa-redo mr-2"></i>
                    UNDI LAGI
                </button>
            </div> -->
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
                            <div>Toko: -</div>
                            <div>PIC: -</div>
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
            // Clear existing intervals
            rollingIntervals.forEach(interval => clearInterval(interval));
            rollingIntervals = [];

            for (let i = 0; i < jumlahPemenang; i++) {
                rollingIntervals[i] = setInterval(() => {
                    if (allVouchersForAnimation.length > 0) {
                        const randomVoucher = allVouchersForAnimation[Math.floor(Math.random() * allVouchersForAnimation.length)];
                        const voucherElement = document.getElementById(`voucher-${i}`);
                        
                        if (voucherElement) {
                            voucherElement.querySelector('.voucher-number').textContent = randomVoucher.nomor_voucher;
                            voucherElement.querySelector('.voucher-info').innerHTML = `
                                <div>Toko: ${randomVoucher.nama_toko}</div>
                                <div>PIC: ${randomVoucher.nama_pic}</div>
                            `;
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

        // Load jumlah voucher tersedia saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            updateVoucherTersedia();
        });
    </script>
</body>
</html>