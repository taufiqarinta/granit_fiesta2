
<x-slot name="header">
    <h2 style="font-family:'Sora',sans-serif; font-weight:700; font-size:1.1rem; color:#1e293b; margin:0;">
        ✅ Input Kehadiran Event
    </h2>
</x-slot>

<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    *, *::before, *::after { box-sizing: border-box; }

    :root {
        --bg:        #f0f4ff;
        --surface:   #ffffff;
        --panel:     #f8faff;
        --border:    #dde4f5;
        --accent:    #4361ee;
        --accent-dk: #3452d0;
        --accent-lt: #eef1ff;
        --success:   #22c55e;
        --success-dk:#16a34a;
        --success-lt:#f0fdf4;
        --danger:    #ef4444;
        --danger-lt: #fef2f2;
        --gray:      #6b7280;
        --gray-dk:   #4b5563;
        --text:      #1e293b;
        --muted:     #64748b;
        --radius:    12px;
        --shadow:    0 2px 16px rgba(67,97,238,.10);
        --shadow-lg: 0 8px 32px rgba(67,97,238,.14);
    }

    body {
        background: var(--bg);
        font-family: 'Sora', sans-serif;
        color: var(--text);
    }

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
        opacity: 0.07;
        pointer-events: none;
        z-index: 5;
    }

    /* ── Page wrapper ── */
    .kh-page { padding: 1.5rem 1rem 3rem; position: relative; z-index: 10; }
    .kh-card {
        background: var(--surface);
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        padding: 2rem;
        max-width: 860px;
        margin: 0 auto;
    }

    /* ── Alerts ── */
    .kh-alert {
        display: none;
        align-items: center;
        gap: .6rem;
        border-radius: 10px;
        padding: .85rem 1rem;
        margin-bottom: 1.5rem;
        font-size: .875rem;
        font-weight: 500;
    }
    .kh-alert.show { display: flex; }
    .kh-alert svg { flex-shrink: 0; width: 18px; height: 18px; }

    .kh-alert-success {
        background: var(--success-lt);
        border: 1.5px solid #86efac;
        color: #166534;
    }
    .kh-alert-error {
        background: var(--danger-lt);
        border: 1.5px solid #fca5a5;
        color: #991b1b;
    }

    /* ── Spinner ── */
    .kh-spinner {
        display: none;
        text-align: center;
        padding: 2.5rem 1rem;
    }
    .kh-spinner.show { display: block; }
    .spinner-ring {
        border: 4px solid var(--border);
        border-top: 4px solid var(--accent);
        border-radius: 50%;
        width: 42px;
        height: 42px;
        animation: spin 0.9s linear infinite;
        margin: 0 auto;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .kh-spinner p { margin-top: .75rem; font-size: .85rem; color: var(--muted); }

    /* ── Steps ── */
    .kh-step { display: none; }
    .kh-step.show { display: block; }

    /* ── Step title ── */
    .kh-step-title {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--accent);
        margin: 0 0 1.25rem;
    }
    .kh-step2-header {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1.75rem;
        padding-bottom: 1.25rem;
        border-bottom: 1.5px solid var(--border);
    }
    .kh-step-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--accent);
        color: #fff;
        font-size: .85rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .kh-step2-header h3 { margin: 0; font-size: 1rem; font-weight: 700; }

    /* ── Scanner ── */
    .scanner-wrap {
        background: var(--panel);
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem;
        margin-bottom: 1.25rem;
        overflow: hidden;
    }
    #qr-reader {
        width: 100%;
        max-width: 420px;
        min-height: 240px;
        border-radius: 8px;
        overflow: hidden;
        background: #0f172a;
        margin: 0 auto;
        display: block;
    }
    .scanner-status {
        text-align: center;
        font-size: .8rem;
        font-weight: 500;
        color: var(--muted);
        margin-top: .6rem;
        min-height: 1.2em;
    }

    /* ── Fields ── */
    .kh-field { margin-bottom: 1rem; }
    .kh-label {
        display: block;
        font-size: .78rem;
        font-weight: 600;
        color: var(--muted);
        margin-bottom: .35rem;
        letter-spacing: .03em;
    }
    .kh-label .req { color: var(--danger); margin-left: 2px; }

    .kh-input {
        width: 100%;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: .55rem .85rem;
        font-size: .9rem;
        font-family: 'Sora', sans-serif;
        color: var(--text);
        background: #fff;
        transition: border-color .18s, box-shadow .18s;
        outline: none;
    }
    .kh-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(67,97,238,.12);
    }
    textarea.kh-input { resize: vertical; min-height: 80px; }

    .kh-readonly {
        background: var(--panel);
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: .55rem .85rem;
        font-size: .9rem;
        color: var(--muted);
        min-height: 2.4rem;
    }

    .kh-input-row { display: flex; gap: .5rem; }
    .kh-input-row .kh-input { flex: 1; }
    .kh-input-icon {
        position: relative;
    }
    .kh-input-icon .kh-input { padding-right: 2.4rem; }
    .kh-input-icon .icon {
        position: absolute;
        right: .7rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.1rem;
        pointer-events: none;
    }

    /* ── Section divider ── */
    .kh-section {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1.5px solid var(--border);
    }
    .kh-section:last-of-type { border-bottom: none; }
    .kh-section-heading {
        font-size: .82rem;
        font-weight: 700;
        color: var(--text);
        text-transform: uppercase;
        letter-spacing: .07em;
        margin: 0 0 1rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }

    /* ── Form grid ── */
    .kh-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1rem;
    }
    @media (max-width: 600px) {
        .kh-grid { grid-template-columns: 1fr; }
    }

    /* ── Agen table ── */
    .agen-table-wrap {
        border: 1.5px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
        margin-top: .25rem;
    }
    .agen-table-wrap table { width: 100%; border-collapse: collapse; }
    .agen-table-wrap th,
    .agen-table-wrap td {
        border-bottom: 1px solid var(--border);
        padding: .5rem .85rem;
        text-align: left;
        font-size: .82rem;
    }
    .agen-table-wrap th {
        background: var(--panel);
        color: var(--muted);
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        font-size: .72rem;
    }
    .agen-table-wrap tbody tr:last-child td { border-bottom: none; }

    /* ── Toggle ── */
    .toggle-wrap {
        display: flex;
        align-items: center;
        gap: .85rem;
    }
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
        flex-shrink: 0;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-switch .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #d1d5db;
        transition: .3s;
        border-radius: 26px;
    }
    .toggle-switch .slider::before {
        content: "";
        position: absolute;
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background: #fff;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 1px 4px rgba(0,0,0,.2);
    }
    .toggle-switch input:checked + .slider { background: var(--success); }
    .toggle-switch input:checked + .slider::before { transform: translateX(24px); }
    #hadirText {
        font-size: .88rem;
        font-weight: 700;
        color: var(--success-dk);
    }

    /* ── Buttons ── */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        border: none;
        border-radius: 8px;
        padding: .6rem 1.2rem;
        font-family: 'Sora', sans-serif;
        font-size: .88rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform .12s, box-shadow .12s, background .15s;
        white-space: nowrap;
        text-decoration: none;
    }
    .btn:active { transform: scale(.96); }

    .btn-full { width: 100%; }

    .btn-primary {
        background: var(--accent);
        color: #fff;
        box-shadow: 0 2px 8px rgba(67,97,238,.25);
    }
    .btn-primary:hover { background: var(--accent-dk); box-shadow: 0 4px 14px rgba(67,97,238,.35); }

    .btn-success {
        background: var(--success);
        color: #fff;
        box-shadow: 0 2px 8px rgba(34,197,94,.22);
    }
    .btn-success:hover { background: var(--success-dk); }
    .btn-success:disabled { background: #86efac; cursor: not-allowed; box-shadow: none; }

    .btn-gray {
        background: #e5e7eb;
        color: var(--gray-dk);
    }
    .btn-gray:hover { background: #d1d5db; }

    /* ── Footer buttons ── */
    .kh-form-footer {
        display: flex;
        gap: .75rem;
        padding-top: 1.25rem;
        border-top: 1.5px solid var(--border);
        margin-top: 1.5rem;
    }
    .kh-form-footer .btn { flex: 1; }
    @media (max-width: 480px) {
        .kh-form-footer { flex-direction: column; }
    }
</style>

<div class="kh-page">
    <div class="kh-card">

        <!-- Success -->
        <div id="successMessage" class="kh-alert kh-alert-success">
            <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span id="successText">Data kehadiran berhasil disimpan!</span>
        </div>

        <!-- Error -->
        <div id="errorMessage" class="kh-alert kh-alert-error">
            <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <span id="errorText">Terjadi kesalahan</span>
        </div>

        <!-- Spinner -->
        <div id="loadingSpinner" class="kh-spinner">
            <div class="spinner-ring"></div>
            <p>Mencari data…</p>
        </div>

        <!-- ══ STEP 1 ══ -->
        <div id="step1" class="kh-step show">
            <p class="kh-step-title">📷 Langkah 1 — Scan atau Input Kode</p>

            <div class="scanner-wrap">
                <div id="qr-reader"></div>
                <p id="scannerStatus" class="scanner-status">Mengaktifkan kamera…</p>
            </div>

            <div class="kh-field">
                <label class="kh-label" for="kodeToko">
                    Kode Toko / Kode Agen <span class="req">*</span>
                </label>
                <div class="kh-input-icon">
                    <input type="text" id="kodeToko" name="kode_toko" class="kh-input"
                        placeholder="Scan QR code atau ketik kode toko/agen…" autocomplete="off">
                    <span class="icon">📱</span>
                </div>
                <p style="margin:.4rem 0 0; font-size:.75rem; color:var(--muted);">
                    Letakkan QR code di depan kamera atau ketik kode secara manual
                </p>
            </div>

            <button type="button" id="searchBtn" class="btn btn-primary btn-full" style="margin-top:.5rem;">
                🔍 Cari Data
            </button>
        </div>

        <!-- ══ STEP 2 ══ -->
        <div id="step2" class="kh-step">
            <div class="kh-step2-header">
                <span class="kh-step-badge">✓</span>
                <h3>Langkah 2 — Informasi Kehadiran</h3>
            </div>

            <form id="kehadiranForm">
                @csrf
                <input type="hidden" id="formId" name="id">

                <!-- Info Toko -->
                <div class="kh-section">
                    <p class="kh-section-heading">🏪 Informasi Toko / Agen</p>

                    <div class="kh-grid">
                        <div class="kh-field">
                            <label class="kh-label">Tipe</label>
                            <div class="kh-readonly" id="tipeDisplay">-</div>
                        </div>
                        <div class="kh-field">
                            <label class="kh-label">Kode Toko / Agen</label>
                            <div class="kh-readonly" id="kodeTokoDisplay">-</div>
                        </div>
                        <div class="kh-field">
                            <label class="kh-label" for="namaTokoDisplay">Nama Toko / Agen <span class="req">*</span></label>
                            <input type="text" id="namaTokoDisplay" name="nama_toko" required class="kh-input">
                        </div>
                    </div>

                    <div class="kh-field" style="margin-top:.5rem;">
                        <label class="kh-label">Kode Agen & Nama Agen</label>
                        <div class="agen-table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width:40%">Kode Agen</th>
                                        <th>Nama Agen</th>
                                    </tr>
                                </thead>
                                <tbody id="agenInfoBody">
                                    <tr><td colspan="2" style="color:var(--muted)">-</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="kh-grid" style="margin-top:.5rem;">
                        <div class="kh-field">
                            <label class="kh-label" for="picDisplay">PIC <span class="req">*</span></label>
                            <input type="text" id="picDisplay" name="pic" required class="kh-input">
                        </div>
                        <div class="kh-field">
                            <label class="kh-label" for="nomorPicDisplay">Nomor PIC <span class="req">*</span></label>
                            <input type="text" id="nomorPicDisplay" name="nomor_pic" required class="kh-input">
                        </div>
                        <div class="kh-field">
                            <label class="kh-label" for="kotaDisplay">Kota <span class="req">*</span></label>
                            <input type="text" id="kotaDisplay" name="kota" required class="kh-input">
                        </div>
                    </div>

                    <div class="kh-field" style="margin-top:.25rem;">
                        <label class="kh-label" for="alamatDisplay">Alamat <span class="req">*</span></label>
                        <textarea id="alamatDisplay" name="alamat" required rows="3" class="kh-input"></textarea>
                    </div>
                </div>

                <!-- Status Kehadiran -->
                <div class="kh-section">
                    <p class="kh-section-heading">📋 Status Kehadiran</p>

                    <div class="kh-grid">
                        <div class="kh-field">
                            <label class="kh-label">Status Hadir</label>
                            <div class="toggle-wrap" style="margin-top:.4rem;">
                                <label class="toggle-switch">
                                    <input type="checkbox" id="hadirCheckbox" name="hadir" checked disabled>
                                    <span class="slider"></span>
                                </label>
                                <span id="hadirText">✓ Hadir</span>
                            </div>
                            <input type="hidden" name="hadir" value="1">
                        </div>

                        <div class="kh-field">
                            <label class="kh-label" for="jumlahKehadiran">Jumlah Orang Hadir <span class="req">*</span></label>
                            <input type="number" id="jumlahKehadiran" name="jumlah_kehadiran"
                                min="0" value="0" class="kh-input">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="kh-form-footer">
                    <button type="button" id="backBtn" class="btn btn-gray">← Kembali</button>
                    <button type="submit" id="submitBtn" class="btn btn-success">✔ Kirim Data Kehadiran</button>
                </div>
            </form>
        </div>

    </div>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const kodeTokoInput   = document.getElementById('kodeToko');
    const searchBtn       = document.getElementById('searchBtn');
    const step1           = document.getElementById('step1');
    const step2           = document.getElementById('step2');
    const loadingSpinner  = document.getElementById('loadingSpinner');
    const successMessage  = document.getElementById('successMessage');
    const errorMessage    = document.getElementById('errorMessage');
    const kehadiranForm   = document.getElementById('kehadiranForm');
    const backBtn         = document.getElementById('backBtn');
    const hadirCheckbox   = document.getElementById('hadirCheckbox');
    const hadirText       = document.getElementById('hadirText');
    const scannerStatus   = document.getElementById('scannerStatus');

    const SCAN_LOCK_MS = 800;
    const FETCH_TIMEOUT_MS = 10000;

    let html5QrCode = null, scannerRunning = false, scanLock = false;
    let scannerBooting = false, searchInProgress = false, lastProcessedCode = '';
    let scanLockTimeout = null;

    kodeTokoInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') searchBtn.click(); });
    searchBtn.addEventListener('click', searchData);
    backBtn.addEventListener('click', goBack);
    kehadiranForm.addEventListener('submit', submitForm);

    async function startScanner() {
        if (scannerRunning || scannerBooting) return;
        if (typeof Html5Qrcode === 'undefined') { showError('Library QR scanner gagal dimuat. Coba refresh halaman.'); return; }
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) { scannerStatus.textContent = 'Browser tidak mendukung akses kamera.'; showError('Browser tidak mendukung kamera. Gunakan Chrome/Edge terbaru.'); return; }
        if (!window.isSecureContext) { scannerStatus.textContent = 'Kamera membutuhkan HTTPS atau localhost.'; showError('Akses kamera butuh HTTPS atau localhost.'); return; }
        try {
            scannerBooting = true;
            html5QrCode = html5QrCode || new Html5Qrcode('qr-reader');
            scannerStatus.textContent = 'Meminta izin kamera…';
            const scanConfig = {
                fps: 20,
                qrbox: function(w, h) {
                    const base = Math.min(w, h);
                    const size = Math.max(180, Math.min(280, Math.floor(base * 0.55)));
                    return { width: size, height: size };
                },
                aspectRatio: 1,
            };

            if (typeof Html5QrcodeSupportedFormats !== 'undefined' && Html5QrcodeSupportedFormats.QR_CODE) {
                // Batasi decode hanya QR agar proses deteksi lebih ringan.
                scanConfig.formatsToSupport = [Html5QrcodeSupportedFormats.QR_CODE];
            }
            let cameraConfig = { facingMode: { ideal: 'environment' } };
            try {
                const cameras = await Html5Qrcode.getCameras();
                if (Array.isArray(cameras) && cameras.length > 0) {
                    const preferred = cameras.find(c => /back|rear|environment/i.test(c.label || '')) || cameras[0];
                    cameraConfig = { deviceId: { exact: preferred.id } };
                }
            } catch(e) {}
            await html5QrCode.start(cameraConfig, scanConfig, onScanSuccess, () => {});
            scannerRunning = true;
            scannerStatus.textContent = 'Kamera aktif. Arahkan QR code ke frame.';
        } catch(err) {
            scannerStatus.textContent = 'Kamera gagal diaktifkan.';
            showError('Tidak bisa mengakses kamera: ' + (err?.message || 'unknown error'));
        } finally {
            scannerBooting = false;
        }
    }

    async function stopScanner() {
        if (!scannerRunning || !html5QrCode) return;
        try { await html5QrCode.stop(); await html5QrCode.clear(); } catch(e) {}
        scannerRunning = false;
        scannerStatus.textContent = 'Kamera dihentikan.';
    }

    function onScanSuccess(decodedText) {
        const code = (decodedText || '').trim();
        if (scanLock || !code || code === lastProcessedCode) return;
        scanLock = true;
        kodeTokoInput.value = code;
        scannerStatus.textContent = 'QR berhasil dibaca. Mencari data…';
        searchData();
        scanLockTimeout = setTimeout(() => { scanLock = false; }, SCAN_LOCK_MS);
    }

    function searchData() {
        const kodeToko = kodeTokoInput.value.trim();
        if (!kodeToko) { showError('Mohon masukkan kode toko atau kode agen'); return; }
        if (searchInProgress) return;
        if (kodeToko === lastProcessedCode && step2.classList.contains('show')) return;
        searchInProgress = true;
        loadingSpinner.classList.add('show');
        step1.classList.remove('show');
        
        // Safety timeout: reset searchInProgress setelah 15 detik jika API tidak respond
        const timeoutId = setTimeout(() => {
            if (searchInProgress) {
                searchInProgress = false;
                loadingSpinner.classList.remove('show');
                step1.classList.add('show');
                showError('Koneksi timeout. Silakan coba lagi.');
            }
        }, 15000);
        
        // Fetch dengan timeout 10 detik (AbortController)
        const controller = new AbortController();
        const fetchTimeout = setTimeout(() => controller.abort(), FETCH_TIMEOUT_MS);
        
        fetch(`{{ url('/api/kehadiran/get-toko') }}/${encodeURIComponent(kodeToko)}`, { signal: controller.signal })
            .then(r => r.json())
            .then(data => {
                clearTimeout(fetchTimeout);
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
            .catch(err => {
                clearTimeout(fetchTimeout);
                loadingSpinner.classList.remove('show');
                console.error(err);
                if (err.name === 'AbortError') {
                    showError('Koneksi timeout. Silakan coba lagi.');
                } else {
                    showError('Terjadi kesalahan saat mencari data');
                }
                step1.classList.add('show');
            })
            .finally(() => { 
                clearTimeout(timeoutId);
                searchInProgress = false; 
            });
    }

    function populateForm(data) {
        document.getElementById('formId').value           = data.data.id;
        document.getElementById('tipeDisplay').textContent= data.data.tipe || data.type || '-';
        document.getElementById('kodeTokoDisplay').textContent = data.data.kode_toko;
        document.getElementById('namaTokoDisplay').value  = data.data.nama_toko;
        document.getElementById('picDisplay').value       = data.data.pic;
        document.getElementById('nomorPicDisplay').value  = data.data.nomor_pic;
        document.getElementById('kotaDisplay').value      = data.data.kota;
        document.getElementById('alamatDisplay').value    = data.data.alamat;
        document.getElementById('hadirCheckbox').checked  = true;
        document.getElementById('jumlahKehadiran').value  = data.data.jumlah_kehadiran || 0;
        const agenInfo = Array.isArray(data.data.agen_info) ? data.data.agen_info : [];
        document.getElementById('agenInfoBody').innerHTML = agenInfo.length === 0
            ? '<tr><td colspan="2" style="color:var(--muted)">-</td></tr>'
            : agenInfo.map(a => `<tr><td>${a.kode_agen||'-'}</td><td>${a.nama_agen||'-'}</td></tr>`).join('');
        updateHadirText();
    }

    function updateHadirText() { hadirText.textContent = '✓ Hadir'; }

    function goBack() {
        // Clear any pending locks and timeouts
        if (scanLockTimeout) clearTimeout(scanLockTimeout);
        scanLock = false;
        searchInProgress = false;
        kodeTokoInput.value = '';
        scannerStatus.textContent = 'Kamera aktif. Arahkan QR code ke frame.';
        loadingSpinner.classList.remove('show');
        
        step1.classList.add('show');
        step2.classList.remove('show');
        kodeTokoInput.focus();
    }

    function showSuccess(message) {
        document.getElementById('successText').textContent = message;
        successMessage.classList.add('show');
        setTimeout(() => { successMessage.classList.remove('show'); }, 5000);
    }

    function showError(message) {
        document.getElementById('errorText').textContent = message;
        errorMessage.classList.add('show');
        setTimeout(() => { errorMessage.classList.remove('show'); }, 5000);
    }

    function submitForm(e) {
        e.preventDefault();
        const requiredFields = ['namaTokoDisplay','picDisplay','nomorPicDisplay','kotaDisplay','alamatDisplay'];
        if (requiredFields.some(id => !document.getElementById(id).value.trim())) {
            showError('Nama Toko, PIC, Nomor PIC, Kota, dan Alamat wajib diisi.'); return;
        }
        const formData = new FormData(kehadiranForm);
        const data = {
            id:               formData.get('id'),
            nama_toko:        formData.get('nama_toko'),
            pic:              formData.get('pic'),
            nomor_pic:        formData.get('nomor_pic'),
            kota:             formData.get('kota'),
            alamat:           formData.get('alamat'),
            hadir:            1,
            jumlah_kehadiran: parseInt(formData.get('jumlah_kehadiran')) || 0,
        };
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengirim…';
        fetch('{{ route('kehadiran.submit') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.textContent = '✔ Kirim Data Kehadiran';
            if (data.success) {
                // Kirim notifikasi ke socket server
                fetch('https://nodejs.kobin.co.id:443/notify', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        id:               data.data.id,   // ← dari response controller
                        hadir:            1,
                        jumlah_kehadiran: document.getElementById('jumlahKehadiran').value,
                        nama_toko:        document.getElementById('namaTokoDisplay').value,
                        pic:              document.getElementById('picDisplay').value,
                        nomor_pic:        document.getElementById('nomorPicDisplay').value,
                        kota:             document.getElementById('kotaDisplay').value,
                        alamat:           document.getElementById('alamatDisplay').value,
                    })
                }).catch(e => console.warn('Socket notify gagal:', e));
                
                showSuccess('Data kehadiran berhasil disimpan!');
                setTimeout(() => {
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
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.textContent = '✔ Kirim Data Kehadiran';
            console.error(err);
            showError('Terjadi kesalahan saat mengirim data');
        });
    }

    window.addEventListener('load', () => { kodeTokoInput.focus(); startScanner(); });
    window.addEventListener('beforeunload', () => { if (scannerRunning) stopScanner(); });
</script>