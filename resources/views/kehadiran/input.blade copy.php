<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Input Kehadiran Event') }}
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
        
        .form-container {
            position: relative;
            z-index: 10;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .success-message {
            display: none;
            background-color: #d1fae5;
            border: 1px solid #6ee7b7;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .success-message.show {
            display: block;
        }

        .error-message {
            display: none;
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .error-message.show {
            display: block;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .loading-spinner.show {
            display: block;
        }

        .spinner {
            border: 4px solid #e5e7eb;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .form-section {
            display: none;
        }

        .form-section.show {
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .qr-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-size: 1.25rem;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-switch .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 24px;
        }

        .toggle-switch .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        .toggle-switch input:checked + .slider {
            background-color: #10b981;
        }

        .toggle-switch input:checked + .slider:before {
            transform: translateX(26px);
        }

        .readonly-field {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            color: #374151;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .agen-list {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .agen-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .agen-list th,
        .agen-list td {
            border-bottom: 1px solid #e5e7eb;
            padding: 0.5rem 0.75rem;
            text-align: left;
            font-size: 0.875rem;
        }

        .agen-list th {
            background: #f9fafb;
            color: #374151;
            font-weight: 600;
        }

        .agen-list tbody tr:last-child td {
            border-bottom: none;
        }

        .scanner-box {
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            padding: 1rem;
            background: #f9fafb;
            margin-bottom: 1rem;
        }

        #qr-reader {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
            min-height: 260px;
            border-radius: 0.5rem;
            overflow: hidden;
            background: #111827;
        }

        .scanner-status {
            text-align: center;
            font-size: 0.875rem;
            color: #4b5563;
            margin-top: 0.75rem;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="form-container">
                    <!-- Success Message -->
                    <div id="successMessage" class="success-message">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span id="successText">Data kehadiran berhasil disimpan!</span>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="error-message">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <span id="errorText">Terjadi kesalahan</span>
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="loading-spinner">
                        <div class="spinner"></div>
                        <p class="mt-3 text-gray-600">Mencari data...</p>
                    </div>

                    <!-- Step 1: Scan QR atau Input Kode Toko -->
                    <div id="step1" class="form-section show">
                        <h3 class="text-lg font-semibold mb-6 text-gray-900">Langkah 1: Scan QR Code atau Input Kode Toko</h3>

                        <div class="scanner-box">
                            <div id="qr-reader"></div>
                            <p id="scannerStatus" class="scanner-status">Mengaktifkan kamera...</p>
                        </div>
                        
                        <div class="mb-6">
                            <label for="kodeToko" class="block text-sm font-medium text-gray-700 mb-2">
                                Kode Toko / Kode Agen <span class="text-red-600">*</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text" 
                                    id="kodeToko" 
                                    name="kode_toko"
                                    placeholder="Scan QR code atau ketik kode toko/agen..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    autocomplete="off">
                                <span class="qr-icon">📱</span>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">
                                Letakkan QR code di depan kamera atau ketik kode secara manual
                            </p>
                        </div>

                        <button type="button" 
                            id="searchBtn"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            Cari Data
                        </button>
                    </div>

                    <!-- Step 2: Form Input Detail -->
                    <div id="step2" class="form-section">
                        <div class="mb-8 pb-6 border-b-2">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm mr-3">✓</span>
                                Langkah 2: Informasi Kehadiran
                            </h3>
                        </div>

                        <form id="kehadiranForm">
                            @csrf
                            <input type="hidden" id="formId" name="id">

                            <!-- Info Toko -->
                            <div class="mb-6 pb-6 border-b">
                                <h4 class="text-md font-semibold text-gray-800 mb-4">Informasi Toko / Agen</h4>
                                
                                <div class="form-row">
                                    <div>
                                        <label for="tipeDisplay" class="block text-sm font-medium text-gray-700 mb-1">
                                            Tipe
                                        </label>
                                        <div class="readonly-field" id="tipeDisplay">-</div>
                                    </div>

                                    <div>
                                        <label for="kodeTokoDisplay" class="block text-sm font-medium text-gray-700 mb-1">
                                            Kode Toko / Agen
                                        </label>
                                        <div class="readonly-field" id="kodeTokoDisplay">-</div>
                                    </div>

                                    <div>
                                        <label for="namaTokoDisplay" class="block text-sm font-medium text-gray-700 mb-1">
                                            Nama Toko / Agen <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text" 
                                            id="namaTokoDisplay" 
                                            name="nama_toko"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Kode Agen dan Nama Agen
                                    </label>
                                    <div class="agen-list">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th style="width: 40%">Kode Agen</th>
                                                    <th>Nama Agen</th>
                                                </tr>
                                            </thead>
                                            <tbody id="agenInfoBody">
                                                <tr>
                                                    <td colspan="2" class="text-gray-500">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="form-row mt-4">
                                    <div>
                                        <label for="picDisplay" class="block text-sm font-medium text-gray-700 mb-1">
                                            PIC <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text" 
                                            id="picDisplay" 
                                            name="pic"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label for="nomorPicDisplay" class="block text-sm font-medium text-gray-700 mb-1">
                                            Nomor PIC <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text" 
                                            id="nomorPicDisplay" 
                                            name="nomor_pic"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <div class="form-row mt-4">
                                    <div>
                                        <label for="kotaDisplay" class="block text-sm font-medium text-gray-700 mb-1">
                                            Kota <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text" 
                                            id="kotaDisplay" 
                                            name="kota"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label for="alamatDisplay" class="block text-sm font-medium text-gray-700 mb-1">
                                        Alamat <span class="text-red-600">*</span>
                                    </label>
                                    <textarea id="alamatDisplay" 
                                        name="alamat"
                                        required
                                        rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                            </div>

                            <!-- Info Kehadiran -->
                            <div class="mb-6">
                                <h4 class="text-md font-semibold text-gray-800 mb-4">Status Kehadiran</h4>
                                
                                <div class="form-row">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">
                                            Status Hadir
                                        </label>
                                        <div class="flex items-center">
                                            <label class="toggle-switch">
                                                <input type="checkbox" id="hadirCheckbox" name="hadir" checked disabled>
                                                <span class="slider"></span>
                                            </label>
                                            <span class="ml-4 text-sm text-emerald-700 font-semibold" id="hadirText">✓ Hadir</span>
                                        </div>
                                        <input type="hidden" name="hadir" value="1">
                                    </div>

                                    <div>
                                        <label for="jumlahKehadiran" class="block text-sm font-medium text-gray-700 mb-1">
                                            Jumlah Orang Hadir <span class="text-red-600">*</span>
                                        </label>
                                        <input type="number" 
                                            id="jumlahKehadiran" 
                                            name="jumlah_kehadiran"
                                            min="0"
                                            value="0"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t">
                                <button type="button" 
                                    id="backBtn"
                                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                    ← Kembali
                                </button>
                                <button type="submit" 
                                    id="submitBtn"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                    Kirim Data Kehadiran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        const kodeTokoInput = document.getElementById('kodeToko');
        const searchBtn = document.getElementById('searchBtn');
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');
        const kehadiranForm = document.getElementById('kehadiranForm');
        const backBtn = document.getElementById('backBtn');
        const hadirCheckbox = document.getElementById('hadirCheckbox');
        const hadirText = document.getElementById('hadirText');
        const scannerStatus = document.getElementById('scannerStatus');

        let html5QrCode = null;
        let scannerRunning = false;
        let scanLock = false;
        let scannerBooting = false;
        let searchInProgress = false;
        let lastProcessedCode = '';

        // Event listeners
        kodeTokoInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                searchBtn.click();
            }
        });

        searchBtn.addEventListener('click', searchData);
        backBtn.addEventListener('click', goBack);
        kehadiranForm.addEventListener('submit', submitForm);

        async function startScanner() {
            if (scannerRunning || scannerBooting) {
                return;
            }

            if (typeof Html5Qrcode === 'undefined') {
                showError('Library QR scanner gagal dimuat. Coba refresh halaman.');
                return;
            }

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                scannerStatus.textContent = 'Browser tidak mendukung akses kamera.';
                showError('Browser tidak mendukung kamera. Gunakan Chrome/Edge terbaru.');
                return;
            }

            if (!window.isSecureContext) {
                scannerStatus.textContent = 'Kamera membutuhkan HTTPS atau localhost.';
                showError('Akses kamera butuh HTTPS atau localhost.');
                return;
            }

            try {
                scannerBooting = true;
                html5QrCode = html5QrCode || new Html5Qrcode('qr-reader');
                scannerStatus.textContent = 'Meminta izin kamera...';

                const scanConfig = {
                    fps: 10,
                    qrbox: function(viewfinderWidth, viewfinderHeight) {
                        const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                        const size = Math.floor(minEdge * 0.75);
                        return { width: size, height: size };
                    }
                };

                let cameraConfig = { facingMode: { ideal: 'environment' } };

                // Fallback ke cameraId agar lebih kompatibel antar device/browser.
                try {
                    const cameras = await Html5Qrcode.getCameras();
                    if (Array.isArray(cameras) && cameras.length > 0) {
                        const preferred = cameras.find((cam) => {
                            const label = (cam.label || '').toLowerCase();
                            return label.includes('back') || label.includes('rear') || label.includes('environment');
                        }) || cameras[0];

                        cameraConfig = { deviceId: { exact: preferred.id } };
                    }
                } catch (cameraListErr) {
                    // Tetap lanjut pakai facingMode jika list kamera gagal diambil.
                }

                await html5QrCode.start(cameraConfig, scanConfig, onScanSuccess, () => {});

                scannerRunning = true;
                scannerStatus.textContent = 'Kamera aktif. Arahkan QR code ke frame.';
            } catch (err) {
                scannerStatus.textContent = 'Kamera gagal diaktifkan.';
                showError('Tidak bisa mengakses kamera: ' + (err?.message || 'unknown error'));
            } finally {
                scannerBooting = false;
            }
        }

        async function stopScanner() {
            if (!scannerRunning || !html5QrCode) {
                return;
            }

            try {
                await html5QrCode.stop();
                await html5QrCode.clear();
            } catch (err) {
                // Ignored on purpose if scanner already stopped by browser.
            }

            scannerRunning = false;
            scannerStatus.textContent = 'Kamera dihentikan.';
        }

        function onScanSuccess(decodedText) {
            const scannedCode = (decodedText || '').trim();

            if (scanLock || !scannedCode || scannedCode === lastProcessedCode) {
                return;
            }

            scanLock = true;
            kodeTokoInput.value = scannedCode;
            scannerStatus.textContent = 'QR berhasil dibaca. Mencari data...';
            searchData();
            setTimeout(() => {
                scanLock = false;
            }, 1500);
        }

        function searchData() {
            const kodeToko = kodeTokoInput.value.trim();
            
            if (!kodeToko) {
                showError('Mohon masukkan kode toko atau kode agen');
                return;
            }

            if (searchInProgress) {
                return;
            }

            if (kodeToko === lastProcessedCode && step2.classList.contains('show')) {
                return;
            }

            searchInProgress = true;

            loadingSpinner.classList.add('show');
            step1.classList.remove('show');

            fetch(`{{ url('/api/kehadiran/get-toko') }}/${encodeURIComponent(kodeToko)}`)
                .then(response => response.json())
                .then(data => {
                    loadingSpinner.classList.remove('show');
                    
                    if (data.success) {
                        lastProcessedCode = kodeToko;
                        populateForm(data);
                        step2.classList.add('show');
                        successMessage.classList.remove('show');
                        errorMessage.classList.remove('show');
                    } else {
                        showError(data.message || 'Data tidak ditemukan');
                        step1.classList.add('show');
                    }
                })
                .catch(error => {
                    loadingSpinner.classList.remove('show');
                    console.error('Error:', error);
                    showError('Terjadi kesalahan saat mencari data');
                    step1.classList.add('show');
                })
                .finally(() => {
                    searchInProgress = false;
                });
        }

        function populateForm(data) {
            document.getElementById('formId').value = data.data.id;
            document.getElementById('tipeDisplay').textContent = data.data.tipe || data.type || '-';
            document.getElementById('kodeTokoDisplay').textContent = data.data.kode_toko;
            document.getElementById('namaTokoDisplay').value = data.data.nama_toko;
            document.getElementById('picDisplay').value = data.data.pic;
            document.getElementById('nomorPicDisplay').value = data.data.nomor_pic;
            document.getElementById('kotaDisplay').value = data.data.kota;
            document.getElementById('alamatDisplay').value = data.data.alamat;
            document.getElementById('hadirCheckbox').checked = true;
            document.getElementById('jumlahKehadiran').value = data.data.jumlah_kehadiran || 0;

            const agenInfoBody = document.getElementById('agenInfoBody');
            const agenInfo = Array.isArray(data.data.agen_info) ? data.data.agen_info : [];

            if (agenInfo.length === 0) {
                agenInfoBody.innerHTML = '<tr><td colspan="2" class="text-gray-500">-</td></tr>';
            } else {
                agenInfoBody.innerHTML = agenInfo.map((agen) => {
                    const kodeAgen = agen.kode_agen || '-';
                    const namaAgen = agen.nama_agen || '-';
                    return `<tr><td>${kodeAgen}</td><td>${namaAgen}</td></tr>`;
                }).join('');
            }
            
            updateHadirText();
        }

        function updateHadirText() {
            hadirText.textContent = '✓ Hadir';
        }

        function goBack() {
            step1.classList.add('show');
            step2.classList.remove('show');
            kodeTokoInput.focus();
        }

        function showSuccess(message) {
            document.getElementById('successText').textContent = message;
            successMessage.classList.add('show');
            setTimeout(() => {
                successMessage.classList.remove('show');
            }, 5000);
        }

        function showError(message) {
            document.getElementById('errorText').textContent = message;
            errorMessage.classList.add('show');
            setTimeout(() => {
                errorMessage.classList.remove('show');
            }, 5000);
        }

        function submitForm(e) {
            e.preventDefault();

            const requiredFields = ['namaTokoDisplay', 'picDisplay', 'nomorPicDisplay', 'kotaDisplay', 'alamatDisplay'];
            const hasEmptyRequiredField = requiredFields.some((fieldId) => {
                const field = document.getElementById(fieldId);
                return !field.value || !field.value.trim();
            });

            if (hasEmptyRequiredField) {
                showError('Nama Toko, PIC, Nomor PIC, Kota, dan Alamat wajib diisi.');
                return;
            }

            const formData = new FormData(kehadiranForm);
            const data = {
                id: formData.get('id'),
                nama_toko: formData.get('nama_toko'),
                pic: formData.get('pic'),
                nomor_pic: formData.get('nomor_pic'),
                kota: formData.get('kota'),
                alamat: formData.get('alamat'),
                hadir: 1,
                jumlah_kehadiran: parseInt(formData.get('jumlah_kehadiran')) || 0,
            };

            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').textContent = 'Mengirim...';

            fetch('{{ route('kehadiran.submit') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('submitBtn').textContent = 'Kirim Data Kehadiran';

                if (data.success) {
                    showSuccess('Data kehadiran berhasil disimpan!');
                    setTimeout(() => {
                        // Reset form
                        kodeTokoInput.value = '';
                        lastProcessedCode = '';
                        kehadiranForm.reset();
                        step1.classList.add('show');
                        step2.classList.remove('show');
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        kodeTokoInput.focus();
                    }, 2000);
                } else {
                    showError(data.message || 'Terjadi kesalahan saat menyimpan data');
                }
            })
            .catch(error => {
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('submitBtn').textContent = 'Kirim Data Kehadiran';
                console.error('Error:', error);
                showError('Terjadi kesalahan saat mengirim data');
            });
        }

        // Auto-focus input saat halaman load
        window.addEventListener('load', () => {
            kodeTokoInput.focus();
            startScanner();
        });

        window.addEventListener('beforeunload', () => {
            if (scannerRunning) {
                stopScanner();
            }
        });
    </script>
</x-app-layout>
