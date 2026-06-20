<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Voucher Undian - Kobin Tiles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .voucher-card {
            background: linear-gradient(135deg, rgb(248, 88, 88) 0%, #c8172d 100%);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .voucher-card.winner {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #8B4513;
            position: relative;
            overflow: hidden;
        }
        .voucher-card.winner::before {
            content: "🏆 PEMENANG";
            position: absolute;
            top: 30px;
            right: -30px;
            background: #FF4500;
            color: white;
            padding: 5px 40px;
            font-size: 10px;
            font-weight: bold;
            transform: rotate(45deg);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .voucher-card.redeemed {
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        .voucher-card.redeemed::before {
            content: "✅ SUDAH DITUKAR";
            position: absolute;
            top: 30px;
            right: -30px;
            background: #059669;
            color: white;
            padding: 5px 40px;
            font-size: 10px;
            font-weight: bold;
            transform: rotate(45deg);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .redeemed-badge {
            background: linear-gradient(45deg, #3B82F6, #1D4ED8);
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            margin-top: 8px;
            border: 2px solid #60A5FA;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }
        .voucher-number {
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }
        .winner-badge {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: #8B4513;
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            margin-top: 8px;
            border: 2px solid #FF8C00;
            box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
        }
        .search-highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .pulse-gold {
            animation: pulseGold 2s infinite;
        }
        @keyframes pulseGold {
            0% { box-shadow: 0 0 0 0 rgba(255, 215, 0, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(255, 215, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 215, 0, 0); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-ticket-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Cek Voucher Undian</h1>
                        <p class="text-gray-600">Kobin Tiles</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ date('d F Y') }}</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Form Input -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Cek Status Voucher Undian</h2>
                <p class="text-gray-600">Masukkan kode unik voucher Anda untuk melihat detail voucher</p>
            </div>

            <form action="{{ route('voucher.proses') }}" method="POST" id="formCekVoucher">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2"></i>Kode Unik Voucher
                    </label>
                    
                    <div id="kodeUnikContainer">
                        <!-- Input baris pertama -->
                        <div class="kode-unik-row flex gap-2 mb-2 fade-in">
                            <input 
                                type="text" 
                                name="kode_unik[]" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Masukkan kode unik (contoh: ABC12345)"
                                maxlength="20"
                                style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase()"
                                value="{{ old('kode_unik.0', isset($kodeUnikInput) ? explode("\n", $kodeUnikInput)[0] ?? '' : '') }}"
                            >
                            <button type="button" class="remove-row-btn bg-red-500 hover:bg-red-600 text-white w-10 h-10 rounded-lg flex items-center justify-center transition-colors" onclick="removeRow(this)" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <!-- Input baris tambahan dari old data -->
                        @if(old('kode_unik') && count(old('kode_unik')) > 1)
                            @for($i = 1; $i < count(old('kode_unik')); $i++)
                                @if(!empty(old('kode_unik.' . $i)))
                                <div class="kode-unik-row flex gap-2 mb-2 fade-in">
                                    <input 
                                        type="text" 
                                        name="kode_unik[]" 
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Masukkan kode unik (contoh: ABC12345)"
                                        maxlength="20"
                                        style="text-transform: uppercase;"
                                        oninput="this.value = this.value.toUpperCase()"
                                        value="{{ old('kode_unik.' . $i) }}"
                                    >
                                    <button type="button" class="remove-row-btn bg-red-500 hover:bg-red-600 text-white w-10 h-10 rounded-lg flex items-center justify-center transition-colors" onclick="removeRow(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endif
                            @endfor
                        @elseif(isset($kodeUnikInput) && count(explode("\n", $kodeUnikInput)) > 1)
                            @for($i = 1; $i < count(explode("\n", $kodeUnikInput)); $i++)
                                @if(!empty(explode("\n", $kodeUnikInput)[$i]))
                                <div class="kode-unik-row flex gap-2 mb-2 fade-in">
                                    <input 
                                        type="text" 
                                        name="kode_unik[]" 
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Masukkan kode unik (contoh: ABC12345)"
                                        maxlength="20"
                                        style="text-transform: uppercase;"
                                        oninput="this.value = this.value.toUpperCase()"
                                        value="{{ explode("\n", $kodeUnikInput)[$i] }}"
                                    >
                                    <button type="button" class="remove-row-btn bg-red-500 hover:bg-red-600 text-white w-10 h-10 rounded-lg flex items-center justify-center transition-colors" onclick="removeRow(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endif
                            @endfor
                        @endif
                    </div>

                    <div class="flex justify-between items-center mt-3">
                        <button type="button" onclick="addRow()" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg transition-colors flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Baris
                        </button>
                        
                        <span class="text-sm text-gray-500" id="rowCount">1 kode unik</span>
                    </div>
                    
                    @error('kode_unik')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('kode_unik.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-center mt-6">
                    <button 
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors duration-200 flex items-center"
                    >
                        <i class="fas fa-search mr-2"></i>
                        Cek Voucher
                    </button>
                </div>
            </form>
        </div>

        <!-- Error Message -->
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 fade-in">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Results Section -->
        @if(isset($groupedVouchers) && $groupedVouchers->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="searchVoucher" 
                            placeholder="Cari voucher..." 
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        >
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                    <span class="text-sm text-gray-600">
                        Ditemukan: <span id="totalVouchers">{{ $vouchers->count() }}</span> voucher
                    </span>
                </div>
            </div>

            <!-- Group by Kode Unik -->
            @foreach($groupedVouchers as $kodeUnik => $voucherGroup)
            <div class="mb-8">
                <div class="bg-gray-100 p-3 rounded-lg mb-4">
                    <h4 class="font-bold text-lg text-gray-800">
                        <i class="fas fa-hashtag mr-2"></i>Kode Unik: 
                        <span class="text-blue-600">{{ $kodeUnik }}</span>
                        <span class="text-sm font-normal text-gray-600 ml-2">
                            ({{ $voucherGroup->count() }} voucher)
                        </span>
                    </h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 voucher-group">
                    @foreach($voucherGroup as $voucher)
                    <div class="voucher-card p-5 text-white voucher-item 
                        @if($voucher->sudah_ditukarkan == 1) 
                            redeemed 
                        @elseif($voucher->status == 1 && !empty($voucher->hadiah)) 
                            winner pulse-gold 
                        @endif">
                        
                        <div class="text-center mb-4">
                            <div class="text-xs opacity-80 mb-1">NOMOR VOUCHER</div>
                            <div class="voucher-number text-xl font-bold tracking-wider">
                                {{ $voucher->nomor_voucher }}
                            </div>
                            
                            <!-- Tampilkan badge sudah ditukar -->
                            @if($voucher->sudah_ditukarkan == 1)
                            <div class="redeemed-badge">
                                <i class="fas fa-check-circle mr-1"></i>
                                Voucher ini sudah ditukar dengan hadiah:<br>
                                <strong>{{ $voucher->hadiah ?? 'Hadiah' }}</strong>
                            </div>
                            <!-- Tampilkan badge pemenang jika status = 1 dan hadiah tidak kosong -->
                            @elseif($voucher->status == 1 && !empty($voucher->hadiah))
                            <div class="winner-badge">
                                <i class="fas fa-trophy mr-1"></i>
                                SELAMAT! Anda memenangkan:<br>
                                <strong>{{ $voucher->hadiah }}</strong>
                            </div>
                            @endif
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="opacity-80">Toko:</span>
                                <span class="font-semibold voucher-toko">{{ $voucher->nama_toko }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="opacity-80">PIC:</span>
                                <span class="font-semibold voucher-pic">{{ $voucher->nama_pic }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-t 
                            @if($voucher->sudah_ditukarkan == 1) 
                                border-blue-300 
                            @elseif($voucher->status == 1 && !empty($voucher->hadiah)) 
                                border-amber-300 
                            @else 
                                border-white border-opacity-20 
                            @endif text-xs opacity-70">
                            <div>Kode: {{ $voucher->kode_unik }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @elseif(isset($groupedVouchers) && $groupedVouchers->count() == 0)
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-6 rounded-lg text-center fade-in">
            <i class="fas fa-exclamation-triangle text-2xl mb-3"></i>
            <h4 class="font-bold text-lg mb-2">Tidak Ditemukan</h4>
            <p>Voucher dengan kode unik yang dimasukkan tidak ditemukan.</p>
        </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-gray-600">
                <p>&copy; {{ date('Y') }} Kobin Tiles.</p>
            </div>
        </div>
    </footer>

    <script>
        // Fungsi untuk menambah baris input
        function addRow() {
            const container = document.getElementById('kodeUnikContainer');
            const newRow = document.createElement('div');
            newRow.className = 'kode-unik-row flex gap-2 mb-2 fade-in';
            newRow.innerHTML = `
                <input 
                    type="text" 
                    name="kode_unik[]" 
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Masukkan kode unik (contoh: ABC12345)"
                    maxlength="20"
                    style="text-transform: uppercase;"
                    oninput="this.value = this.value.toUpperCase()"
                >
                <button type="button" class="remove-row-btn bg-red-500 hover:bg-red-600 text-white w-10 h-10 rounded-lg flex items-center justify-center transition-colors" onclick="removeRow(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(newRow);
            
            // Auto-focus ke input baru
            const newInput = newRow.querySelector('input');
            newInput.focus();
            
            updateRowCount();
        }

        // Fungsi untuk menghapus baris input
        function removeRow(button) {
            const row = button.closest('.kode-unik-row');
            if (row) {
                row.remove();
                updateRowCount();
            }
        }

        // Update fungsi updateRowCount untuk handle old data
        function updateRowCount() {
            const rows = document.querySelectorAll('.kode-unik-row');
            const rowCount = rows.length;
            document.getElementById('rowCount').textContent = `${rowCount} kode unik`;
            
            // Sembunyikan tombol hapus jika hanya ada 1 baris
            const removeButtons = document.querySelectorAll('.remove-row-btn');
            if (rowCount === 1) {
                removeButtons.forEach(btn => {
                    btn.style.display = 'none';
                });
            } else {
                removeButtons.forEach(btn => {
                    btn.style.display = 'flex';
                });
            }
        }

        // Inisialisasi saat halaman load
        document.addEventListener('DOMContentLoaded', function() {
            updateRowCount();
            
            // Juga inisialisasi search functionality jika ada
            const searchInput = document.getElementById('searchVoucher');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const voucherItems = document.querySelectorAll('.voucher-item');
                    let visibleCount = 0;

                    voucherItems.forEach(item => {
                        const toko = item.querySelector('.voucher-toko').textContent.toLowerCase();
                        const pic = item.querySelector('.voucher-pic').textContent.toLowerCase();
                        const nomor = item.querySelector('.voucher-number').textContent.toLowerCase();
                        
                        const matches = toko.includes(searchTerm) || 
                                    pic.includes(searchTerm) || 
                                    nomor.includes(searchTerm);
                        
                        item.style.display = matches ? 'block' : 'none';
                        if (matches) visibleCount++;
                    });

                    document.getElementById('totalVouchers').textContent = visibleCount;
                });
            }
        });
    </script>
</body>
</html>