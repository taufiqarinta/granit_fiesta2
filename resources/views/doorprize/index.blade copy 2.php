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
        }

        .container {
            background: rgba(255,255,255,0.05);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.1);
            max-width: 900px;
            margin: 20px auto;
        }

        .voucher-card {
            font-size: 1.4em;
            background: linear-gradient(135deg, #DC143C, #B22222);
            border-radius: 15px;
            margin: 10px 0;
            height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.4);
            border: 2px solid rgba(255,255,255,0.1);
        }

        .voucher-card.winner {
            animation: pulse 2s infinite;
            border: 3px solid #FFD700;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        .blink {
            animation: blink 0.5s infinite alternate;
        }

        @keyframes blink {
            from { opacity: 1; }
            to { opacity: 0.7; }
        }

        button {
            padding: 12px 30px;
            font-size: 1.1em;
            background: linear-gradient(135deg, #DC143C, #B22222);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        button:hover {
            background: linear-gradient(135deg, #FF1744, #DC143C);
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0,0,0,0.4);
        }

        button:disabled {
            background: #666;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .voucher-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .voucher-number {
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            font-weight: bold;
            font-size: 1.2em;
        }

        .voucher-info {
            font-size: 0.8em;
            opacity: 0.95;
            margin-top: 5px;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #DC143C;
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
    </style>
</head>
<body>
    <!-- Header -->
    <div class="text-center pt-8">
        <div class="flex items-center justify-center mb-4">
            <div class="w-16 h-16 header-icon rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-gift text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold">🎁 Pengundian Doorprize</h1>
                <p class="text-xl opacity-90">Kobin Tiles</p>
            </div>
        </div>
        <p class="opacity-80">{{ date('d F Y') }} | <span id="voucherTersedia">Loading...</span></p>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Pilih Doorprize -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold mb-4">Pilih Doorprize</h2>
            
            <div class="max-w-md mx-auto mb-6">
                <select 
                    id="doorprize_id" 
                    class="w-full px-4 py-3 rounded-lg transition-colors"
                >
                    <option value="">-- Pilih Doorprize --</option>
                    @foreach($doorprizes as $doorprize)
                        <option value="{{ $doorprize->id }}">
                            {{ $doorprize->nama_doorprize }} ({{ $doorprize->jumlah_doorprize }} pemenang)
                        </option>
                    @endforeach
                </select>
            </div>

            <button 
                id="startBtn"
                onclick="startUndian()"
                disabled
                class="mt-4"
            >
                <i class="fas fa-play mr-2"></i>
                MULAI UNDIAN
            </button>
        </div>

        <!-- Area Voucher -->
        <div id="voucherArea" class="hidden">
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold mb-2" id="infoDoorprize"></h3>
                <div class="voucher-grid" id="voucherList">
                    <!-- Voucher cards akan di-generate oleh JavaScript -->
                </div>
            </div>

            <!-- Tombol Undi Lagi -->
            <div class="text-center mt-8">
                <button onclick="startUndian()" class="btn-green">
                    <i class="fas fa-redo mr-2"></i>
                    UNDI LAGI
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading" class="hidden fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50">
        <div class="bg-white bg-opacity-10 rounded-lg p-8 text-center backdrop-blur-sm border border-red-600">
            <div class="loading mx-auto mb-4"></div>
            <p class="text-white text-lg font-semibold">Mengocok voucher...</p>
        </div>
    </div>

    <script>
        let isRandomizing = false;
        let rollingIntervals = [];
        let allVouchersForAnimation = [];

        // Update tombol start berdasarkan pilihan doorprize
        document.getElementById('doorprize_id').addEventListener('change', function() {
            const startBtn = document.getElementById('startBtn');
            startBtn.disabled = !this.value;
            
            // Generate voucher cards placeholder
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const jumlahPemenang = parseInt(selectedOption.text.match(/\((\d+)/)[1]);
                generateVoucherCards(jumlahPemenang);
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
            
            document.getElementById('voucherArea').classList.remove('hidden');
        }

        // Fungsi untuk update jumlah voucher tersedia
        function updateVoucherTersedia() {
            fetch('/doorprize/voucher-tersedia')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('voucherTersedia').textContent = 
                        `Voucher tersedia: ${data.tersedia}`;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
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
            document.getElementById('startBtn').disabled = true;

            const selectedOption = document.getElementById('doorprize_id').options[document.getElementById('doorprize_id').selectedIndex];
            const jumlahPemenang = parseInt(selectedOption.text.match(/\((\d+)/)[1]);
            const namaDoorprize = selectedOption.text.split(' (')[0];

            document.getElementById('infoDoorprize').textContent = `${namaDoorprize} - ${jumlahPemenang} Pemenang`;

            // Load data untuk animasi
            if (allVouchersForAnimation.length === 0) {
                try {
                    const response = await fetch('/doorprize/animation-vouchers');
                    allVouchersForAnimation = await response.json();
                } catch (error) {
                    console.error('Error loading animation vouchers:', error);
                }
            }

            // Mulai animasi random
            startRandomAnimation(jumlahPemenang);

            // Kirim request ke server untuk mendapatkan pemenang
            fetch('/doorprize/start', {
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
                    // Tampilkan hasil setelah delay untuk efek dramatis
                    setTimeout(() => {
                        stopRandomAnimation();
                        showResult(data.vouchers);
                        updateVoucherTersedia();
                    }, 5000);
                } else {
                    stopRandomAnimation();
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                stopRandomAnimation();
                alert('Terjadi kesalahan saat mengundi');
            })
            .finally(() => {
                isRandomizing = false;
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('startBtn').disabled = false;
            });
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
                        
                        voucherElement.querySelector('.voucher-number').textContent = randomVoucher.nomor_voucher;
                        voucherElement.querySelector('.voucher-info').innerHTML = `
                            <div>Toko: ${randomVoucher.nama_toko}</div>
                            <div>PIC: ${randomVoucher.nama_pic}</div>
                        `;
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
                
                voucherElement.querySelector('.voucher-number').textContent = voucher.nomor_voucher;
                voucherElement.querySelector('.voucher-info').innerHTML = `
                    <div>Toko: ${voucher.nama_toko}</div>
                    <div>PIC: ${voucher.nama_pic}</div>
                `;
                
                voucherElement.classList.add('winner', 'blink');
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