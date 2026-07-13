<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Voucher Undian - Kobin Tiles</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Reset dan base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            min-height: 100svh;
            line-height: 1.5;
            color: #ffffff;
            position: relative;
            overflow-x: hidden;

            display: flex;
            flex-direction: column;

            background-image: url('/images/bg-doorprize.webp');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center center;
        }

        .main-container {
            max-width: 80rem;
            margin: 0 auto;
            padding: 2rem 1rem;
            flex: 1 0 auto;   /* <- INI KUNCINYA: main meregang, footer kedorong ke bawah */
            width: 100%;
        }

        /* Layer background terpisah supaya selalu full-cover, tidak terpotong di browser mobile */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -1;
            background-image: url('/images/bg-doorprize.webp');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center center;
            width: 100vw;
            height: 100vh;
            height: 100svh;
        }
        
        /* Header */
        header {
            background: rgba(80, 0, 0, 0.55);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            border-bottom: 2px solid transparent;
            border-image: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c) 1;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
        }
        
        .header-container {
            max-width: 80rem;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        @media (min-width: 640px) {
            .header-container {
                padding: 0 1.5rem;
            }
        }
        
        @media (min-width: 1024px) {
            .header-container {
                padding: 0 2rem;
            }
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
        }
        
        .header-left {
            display: flex;
            align-items: center;
        }
        
        .logo {
            width: 3rem;
            height: 3rem;
            background: linear-gradient(135deg, #cd1c21, #8B0000);
            border: 2px solid transparent;
            border-image: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c) 1;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
        }
        
        .logo i {
            color: #fcf6ba;
            font-size: 1.25rem;
        }
        
        .header-title h1 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff;
        }
        
        .header-title p {
            color: #f5d98c;
        }
        
        .header-right {
            text-align: right;
        }
        
        .header-date {
            font-size: 0.875rem;
            color: #f0d9a8;
        }
        
        @media (min-width: 640px) {
            .main-container {
                padding: 2rem 1.5rem;
            }
        }
        
        @media (min-width: 1024px) {
            .main-container {
                padding: 2rem 2rem;
            }
        }
        
        /* Form Section */
        .form-section {
            background-color: rgba(255, 255, 255, 0.97);
            border-radius: 0.75rem;
            border: 1px solid #e8c98a;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .form-header h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #8B0000;
            margin-bottom: 0.5rem;
        }
        
        .form-header p {
            color: #6b7280;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .kode-unik-row {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .kode-input {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .kode-input:focus {
            outline: none;
            border-color: #cd1c21;
            box-shadow: 0 0 0 2px rgba(205, 28, 33, 0.2);
        }
        
        .remove-row-btn {
            background-color: #b91c1c;
            color: white;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .remove-row-btn:hover {
            background-color: #7f1d1d;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.75rem;
        }
        
        .add-row-btn {
            color: #6b3e00;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            transition: filter 0.2s;
        }
        
        .add-row-btn:hover {
            filter: brightness(1.05);
        }
        
        .row-count {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .submit-btn {
            color: white;
            font-weight: bold;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            border: 2px solid transparent;
            background-image: linear-gradient(#cd1c21, #cd1c21), linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            background-origin: border-box;
            background-clip: padding-box, border-box;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin: 1.5rem auto 0;
            transition: filter 0.2s;
        }
        
        .submit-btn:hover {
            filter: brightness(1.1);
        }
        
        /* Error Message */
        .error-message {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        /* Results Section */
        .results-section {
            background-color: rgba(255, 255, 255, 0.97);
            border-radius: 0.75rem;
            border: 1px solid #e8c98a;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .search-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-input {
            padding-left: 2.5rem;
            padding-right: 1rem;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            width: 100%;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #cd1c21;
            box-shadow: 0 0 0 2px rgba(205, 28, 33, 0.2);
        }
        
        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 0.75rem;
            color: #9ca3af;
        }
        
        .voucher-count {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        /* Voucher Group */
        .voucher-group-container {
            margin-bottom: 2rem;
        }
        
        .group-header {
            background: linear-gradient(to right, rgba(205, 28, 33, 0.08), rgba(191, 149, 63, 0.15));
            border: 1px solid #f0d9a8;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .group-title {
            font-weight: bold;
            font-size: 1.125rem;
            color: #1f2937;
        }
        
        .group-code {
            color: #c8172d;
        }
        
        .group-count {
            font-size: 0.875rem;
            font-weight: normal;
            color: #6b7280;
            margin-left: 0.5rem;
        }
        
        /* Voucher Grid */
        .voucher-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        @media (min-width: 768px) {
            .voucher-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (min-width: 1024px) {
            .voucher-grid {
                grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
            margin-top: 1rem;
        }
        }
        
        /* Voucher Card */
        .voucher-card {
            background: linear-gradient(135deg, #dc143c 0%, #7f0000 100%);
            border: 1px solid rgba(255, 215, 128, 0.35);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 1.25rem;
            color: white;
        }
        
        .voucher-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.35);
        }
        
        .voucher-card.winner {
            background: linear-gradient(135deg, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            border: 1px solid #8B4513;
            color: #8B4513;
            position: relative;
            overflow: hidden;
        }
        
        .voucher-card.winner::before {
            content: "🏆 PEMENANG";
            position: absolute;
            top: 30px;
            right: -30px;
            background: #8B0000;
            color: #fcf6ba;
            padding: 5px 40px;
            font-size: 10px;
            font-weight: bold;
            transform: rotate(45deg);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .voucher-card.redeemed {
            background: linear-gradient(135deg, #5a5a5a 0%, #2b2b2b 100%);
            border: 1px solid rgba(230, 230, 230, 0.35);
            color: #f0f0f0;
            position: relative;
            overflow: hidden;
        }
        
        .voucher-card.redeemed::before {
            content: "✅ SUDAH DITUKAR";
            position: absolute;
            top: 30px;
            right: -30px;
            background: #8a8a8a;
            color: white;
            padding: 5px 40px;
            font-size: 10px;
            font-weight: bold;
            transform: rotate(45deg);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .voucher-content {
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .voucher-label {
            font-size: 0.75rem;
            opacity: 0.8;
            margin-bottom: 0.25rem;
        }
        
        .voucher-number {
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            font-size: 1.25rem;
            font-weight: bold;
        }
        
        .redeemed-badge {
            background: linear-gradient(45deg, #6b6b6b, #3a3a3a);
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            margin-top: 8px;
            border: 2px solid #9a9a9a;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
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
        
        .voucher-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-size: 0.875rem;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
        }
        
        .detail-label {
            /* opacity: 0.8; */
            font-weight: 600;
        }
        
        .detail-value {
            font-weight: 600;
        }
        
        .voucher-footer {
            margin-top: 1rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 1rem;
            font-weight: bold;
        }
        
        /* Not Found Message */
        .not-found {
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            color: #92400e;
            padding: 1.5rem;
            border-radius: 0.5rem;
            text-align: center;
        }
        
        .not-found i {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
        }
        
        .not-found h4 {
            font-weight: bold;
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
        }
        
        /* Footer */
        footer {
            background: rgba(80, 0, 0, 0.55);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            border-top: 2px solid transparent;
            border-image: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c) 1;
            margin-top: 3rem;
        }
        
        .footer-container {
            max-width: 80rem;
            margin: 0 auto;
            padding: 1.5rem 1rem;
        }
        
        .footer-content {
            text-align: center;
            color: #f0d9a8;
        }
        
        /* Animations */
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
        
        .search-highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="header-content">
                <div class="header-left">
                    <div class="logo">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="header-title">
                        <h1>Cek Voucher Undian</h1>
                        <p>Kobin Tiles</p>
                    </div>
                </div>
                <div class="header-right">
                    <p class="header-date">{{ date('d F Y') }}</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-container">
        <!-- Form Input -->
        <div class="form-section">
            <div class="form-header">
                <h2>Cek Status Voucher Undian</h2>
                <p>Masukkan kode unik voucher Anda untuk melihat detail voucher</p>
            </div>

            <form action="{{ route('voucher.proses') }}" method="POST" id="formCekVoucher">
                @csrf
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-key"></i> Kode Unik Voucher
                    </label>
                    
                    <div id="kodeUnikContainer">
                        <!-- Input baris pertama -->
                        <div class="kode-unik-row fade-in">
                            <input 
                                type="text" 
                                name="kode_unik[]" 
                                class="kode-input"
                                placeholder="Masukkan kode unik (contoh: ABC12345)"
                                maxlength="20"
                                style="text-transform: uppercase;"
                                oninput="this.value = this.value.toUpperCase()"
                                value="{{ old('kode_unik.0', isset($kodeUnikInput) ? explode("\n", $kodeUnikInput)[0] ?? '' : '') }}"
                            >
                            <button type="button" class="remove-row-btn" onclick="removeRow(this)" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <!-- Input baris tambahan dari old data -->
                        @if(old('kode_unik') && count(old('kode_unik')) > 1)
                            @for($i = 1; $i < count(old('kode_unik')); $i++)
                                @if(!empty(old('kode_unik.' . $i)))
                                <div class="kode-unik-row fade-in">
                                    <input 
                                        type="text" 
                                        name="kode_unik[]" 
                                        class="kode-input"
                                        placeholder="Masukkan kode unik (contoh: ABC12345)"
                                        maxlength="20"
                                        style="text-transform: uppercase;"
                                        oninput="this.value = this.value.toUpperCase()"
                                        value="{{ old('kode_unik.' . $i) }}"
                                    >
                                    <button type="button" class="remove-row-btn" onclick="removeRow(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endif
                            @endfor
                        @elseif(isset($kodeUnikInput) && count(explode("\n", $kodeUnikInput)) > 1)
                            @for($i = 1; $i < count(explode("\n", $kodeUnikInput)); $i++)
                                @if(!empty(explode("\n", $kodeUnikInput)[$i]))
                                <div class="kode-unik-row fade-in">
                                    <input 
                                        type="text" 
                                        name="kode_unik[]" 
                                        class="kode-input"
                                        placeholder="Masukkan kode unik (contoh: ABC12345)"
                                        maxlength="20"
                                        style="text-transform: uppercase;"
                                        oninput="this.value = this.value.toUpperCase()"
                                        value="{{ explode("\n", $kodeUnikInput)[$i] }}"
                                    >
                                    <button type="button" class="remove-row-btn" onclick="removeRow(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endif
                            @endfor
                        @endif
                    </div>

                    <div class="form-actions">
                        <button type="button" onclick="addRow()" class="add-row-btn">
                            <i class="fas fa-plus"></i>
                            Tambah Baris
                        </button>
                        
                        <span class="row-count" id="rowCount">1 kode unik</span>
                    </div>
                    
                    @error('kode_unik')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                    @error('kode_unik.*')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-search"></i>
                    Cek Voucher
                </button>
            </form>
        </div>

        <!-- Error Message -->
        @if(session('error'))
            <div class="error-message fade-in">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Results Section -->
        @if(isset($groupedVouchers) && $groupedVouchers->count() > 0)
        <div class="results-section">
            <div class="results-header">
                <div class="search-container">
                    <div class="search-box">
                        <input 
                            type="text" 
                            id="searchVoucher" 
                            placeholder="Cari voucher..." 
                            class="search-input"
                        >
                        <i class="fas fa-search search-icon"></i>
                    </div>
                    <span class="voucher-count">
                        Ditemukan: <span id="totalVouchers">{{ $vouchers->count() }}</span> voucher
                    </span>
                </div>
            </div>

            <!-- Group by Kode Unik -->
            @foreach($groupedVouchers as $kodeUnik => $voucherGroup)
            <div class="voucher-group-container">
                <div class="group-header">
                    <h4 class="group-title">
                        <i class="fas fa-hashtag"></i> Kode Unik: 
                        <span class="group-code">{{ $kodeUnik }}</span>
                        <span class="group-count">
                            ({{ $voucherGroup->count() }} voucher)
                        </span>
                    </h4>
                </div>
                
                <div class="voucher-grid voucher-group">
                    @foreach($voucherGroup as $voucher)
                    <div class="voucher-card voucher-item 
                        @if($voucher->sudah_ditukarkan == 1) 
                            redeemed 
                        @elseif($voucher->status == 1 && !empty($voucher->hadiah)) 
                            winner pulse-gold 
                        @endif">
                        
                        <div class="voucher-content">
                            <div class="voucher-label">NOMOR VOUCHER</div>
                            <div class="voucher-number">
                                {{ $voucher->nomor_voucher }}
                            </div>
                            
                            <!-- Tampilkan badge sudah ditukar -->
                            @if($voucher->sudah_ditukarkan == 1)
                            <div class="redeemed-badge">
                                <i class="fas fa-check-circle"></i>
                                Voucher ini sudah ditukar dengan hadiah:<br>
                                <strong>{{ $voucher->hadiah ?? 'Hadiah' }}</strong>
                            </div>
                            <!-- Tampilkan badge pemenang jika status = 1 dan hadiah tidak kosong -->
                            @elseif($voucher->status == 1 && !empty($voucher->hadiah))
                            <div class="winner-badge">
                                <i class="fas fa-trophy"></i>
                                SELAMAT! Anda memenangkan:<br>
                                <strong>{{ $voucher->hadiah }}</strong>
                            </div>
                            @endif
                        </div>
                        
                        <div class="voucher-details">
                            <div class="detail-row">
                                <span class="detail-label">Pelanggan:</span>
                                <span class="detail-value voucher-toko">{{ $voucher->nama_toko }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">PIC:</span>
                                <span class="detail-value voucher-pic">{{ $voucher->nama_pic }}</span>
                            </div>
                        </div>
                        
                        <div class="voucher-footer">
                            <div>Kode: {{ $voucher->kode_unik }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @elseif(isset($groupedVouchers) && $groupedVouchers->count() == 0)
        <div class="not-found fade-in">
            <i class="fas fa-exclamation-triangle"></i>
            <h4>Tidak Ditemukan</h4>
            <p>Voucher dengan kode unik yang dimasukkan tidak ditemukan.</p>
        </div>
        @endif
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <p>&copy; {{ date('Y') }} Kobin Tiles.</p>
            </div>
        </div>
    </footer>

    <script>
        // Fungsi untuk menambah baris input
        function addRow() {
            const container = document.getElementById('kodeUnikContainer');
            const newRow = document.createElement('div');
            newRow.className = 'kode-unik-row fade-in';
            newRow.innerHTML = `
                <input 
                    type="text" 
                    name="kode_unik[]" 
                    class="kode-input"
                    placeholder="Masukkan kode unik (contoh: ABC12345)"
                    maxlength="20"
                    style="text-transform: uppercase;"
                    oninput="this.value = this.value.toUpperCase()"
                >
                <button type="button" class="remove-row-btn" onclick="removeRow(this)">
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