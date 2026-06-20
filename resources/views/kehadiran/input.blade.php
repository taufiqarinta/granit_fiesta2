<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Input Kehadiran Event</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #fff5f5;
            --surface:   #ffffff;
            --panel:     #fff7f7;
            --border:    #fde2e2;
            --accent:    #ef4444;
            --accent-dk: #dc2626;
            --accent-lt: #fff1f1;
            --success:   #16a34a;
            --success-dk:#15803d;
            --success-lt:#f0fdf4;
            --danger:    #ef4444;
            --danger-lt: #fef2f2;
            --text:      #1e293b;
            --muted:     #6b7280;
            --radius:    12px;
            --shadow-lg: 0 8px 32px rgba(239,68,68,.08);
        }

        body {
            background: var(--bg);
            font-family: 'Sora', sans-serif;
            color: var(--text);
            min-height: 100vh;
        }

        /* Watermark corner */
        body::after {
            content: "";
            position: fixed;
            right: 20px; bottom: 20px;
            width: 240px; height: 240px;
            background-image: url('{{ asset('corner.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: .07;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Page ── */
        .page {
            padding: 1.5rem 1rem 3rem;
            position: relative;
            z-index: 1;
        }

        .card {
            background: var(--surface);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            padding: 2rem;
            max-width: 860px;
            margin: 0 auto;
        }

        @media (max-width: 600px) {
            .page { padding: .75rem .5rem 2rem; }
            .card { padding: 1rem; border-radius: 14px; }
        }

        /* ── Alert ── */
        .alert {
            display: none;
            align-items: center;
            gap: .6rem;
            border-radius: 10px;
            padding: .85rem 1rem;
            margin-bottom: 1.25rem;
            font-size: .875rem;
            font-weight: 500;
        }
        .alert.show { display: flex; }
        .alert svg { flex-shrink: 0; width: 18px; height: 18px; }
        .alert-success { background: var(--success-lt); border: 1.5px solid #86efac; color: #166534; }
        .alert-error   { background: var(--danger-lt);  border: 1.5px solid #fca5a5; color: #991b1b; }

        /* ── Spinner ── */
        .spinner-wrap { display: none; text-align: center; padding: 2.5rem 1rem; }
        .spinner-wrap.show { display: block; }
        .spinner-ring {
            border: 4px solid var(--border);
            border-top-color: var(--accent);
            border-radius: 50%;
            width: 42px; height: 42px;
            animation: spin .9s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner-wrap p { margin-top: .75rem; font-size: .85rem; color: var(--muted); }

        /* ── Steps ── */
        .step { display: none; }
        .step.show { display: block; }

        /* ── Section label ── */
        .section-label {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 1.25rem;
        }

        /* ── Step 2 header ── */
        .step2-header {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1.75rem;
            padding-bottom: 1.25rem;
            border-bottom: 1.5px solid var(--border);
        }
        .step-badge {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--accent);
            color: #fff;
            font-size: .85rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .step2-header h3 { margin: 0; font-size: 1rem; font-weight: 700; }

        /* ── Scanner ── */
        .scanner-wrap {
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            padding: 1rem;
            margin-bottom: 1.25rem;
        }

        /* KUNCI: aspect-ratio bukan min-height, agar tidak loncat saat kamera load */
        .scanner-box {
            position: relative;
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
            border-radius: 10px;
            overflow: hidden;
            background: #0f172a;
            aspect-ratio: 4 / 3;
        }

        .scanner-box video {
            position: absolute;
            inset: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
        }

        .scanner-box canvas { display: none; }

        /* Overlay + frame corners */
        .scanner-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .scan-frame {
            width: 52%;
            aspect-ratio: 1;
            position: relative;
        }

        .scan-frame::before, .scan-frame::after,
        .scan-frame > span::before, .scan-frame > span::after {
            content: '';
            position: absolute;
            width: 20px; height: 20px;
            border-color: #4ade80;
            border-style: solid;
        }
        .scan-frame::before  { top: 0;    left: 0;  border-width: 3px 0 0 3px; }
        .scan-frame::after   { top: 0;    right: 0; border-width: 3px 3px 0 0; }
        .scan-frame > span::before { bottom: 0; left: 0;  border-width: 0 0 3px 3px; }
        .scan-frame > span::after  { bottom: 0; right: 0; border-width: 0 3px 3px 0; }

        .scan-line {
            position: absolute;
            left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #4ade80, transparent);
            animation: scanMove 2s ease-in-out infinite;
        }
        @keyframes scanMove { 0%,100% { top: 10%; } 50% { top: 88%; } }

        /* Tombol flip kamera — pojok kanan atas scanner */
        .btn-flip {
            position: absolute;
            top: .5rem; right: .5rem;
            background: rgba(255,255,255,.18);
            border: 1.5px solid rgba(255,255,255,.35);
            border-radius: 8px;
            color: #fff;
            font-size: .75rem;
            font-weight: 600;
            padding: .3rem .6rem;
            cursor: pointer;
            backdrop-filter: blur(4px);
            z-index: 10;
            font-family: 'Sora', sans-serif;
            transition: background .15s;
        }
        .btn-flip:hover { background: rgba(255,255,255,.28); }

        .scanner-status {
            text-align: center;
            font-size: .8rem;
            font-weight: 500;
            color: var(--muted);
            margin-top: .6rem;
            min-height: 1.3em;
            padding: 0 .25rem;
        }
        .scanner-status.ok  { color: var(--success); }
        .scanner-status.err { color: var(--danger); }

        /* ── Fields ── */
        .field { margin-bottom: 1rem; }

        .label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: .35rem;
            letter-spacing: .03em;
        }
        .label .req { color: var(--danger); margin-left: 2px; }

        .input {
            width: 100%;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: .6rem .85rem;
            font-size: 16px; /* cegah zoom iOS */
            font-family: 'Sora', sans-serif;
            color: var(--text);
            background: #fff;
            transition: border-color .18s, box-shadow .18s;
            outline: none;
        }
        .input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(67,97,238,.12);
        }
        textarea.input { resize: vertical; min-height: 80px; }

        .readonly {
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: .6rem .85rem;
            font-size: .9rem;
            color: var(--muted);
            min-height: 2.4rem;
        }

        .input-icon-wrap { position: relative; }
        .input-icon-wrap .input { padding-right: 2.4rem; }
        .input-icon-wrap .icon {
            position: absolute;
            right: .75rem; top: 50%;
            transform: translateY(-50%);
            font-size: 1.1rem;
            pointer-events: none;
        }

        /* ── Section block ── */
        .section-block {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1.5px solid var(--border);
        }
        .section-block:last-of-type { border-bottom: none; }
        .section-heading {
            font-size: .8rem;
            font-weight: 700;
            color: var(--text);
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: 1rem;
        }

        /* ── Grid ── */
        .grid-auto {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
        }
        @media (max-width: 580px) { .grid-auto { grid-template-columns: 1fr; } }

        /* ── Agen table ── */
        .agen-table {
            border: 1.5px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            margin-top: .25rem;
        }
        .agen-table table { width: 100%; border-collapse: collapse; }
        .agen-table th,
        .agen-table td {
            border-bottom: 1px solid var(--border);
            padding: .5rem .85rem;
            text-align: left;
            font-size: .82rem;
        }
        .agen-table th {
            background: var(--panel);
            color: var(--muted);
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            font-size: .7rem;
        }
        .agen-table tbody tr:last-child td { border-bottom: none; }

        /* ── Toggle ── */
        .toggle-wrap { display: flex; align-items: center; gap: .85rem; margin-top: .4rem; }
        .toggle {
            position: relative;
            display: inline-block;
            width: 50px; height: 26px;
            flex-shrink: 0;
        }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle .slider {
            position: absolute; inset: 0;
            background: #d1d5db;
            border-radius: 26px;
            transition: .3s;
            cursor: pointer;
        }
        .toggle .slider::before {
            content: "";
            position: absolute;
            width: 20px; height: 20px;
            left: 3px; bottom: 3px;
            background: #fff;
            border-radius: 50%;
            transition: .3s;
            box-shadow: 0 1px 4px rgba(0,0,0,.2);
        }
        .toggle input:checked + .slider { background: var(--success); }
        .toggle input:checked + .slider::before { transform: translateX(24px); }
        #hadirText { font-size: .88rem; font-weight: 700; color: var(--success-dk); }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            border: none;
            border-radius: 8px;
            padding: .65rem 1.2rem;
            font-family: 'Sora', sans-serif;
            font-size: .88rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform .12s, background .15s;
            white-space: nowrap;
            min-height: 44px;
            text-decoration: none;
        }
        .btn:active { transform: scale(.96); }
        .btn-full { width: 100%; }

        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-dk); }

        .btn-success { background: var(--success); color: #fff; }
        .btn-success:hover { background: var(--success-dk); }
        .btn-success:disabled { background: #86efac; cursor: not-allowed; }

        .btn-gray { background: #e5e7eb; color: #4b5563; }
        .btn-gray:hover { background: #d1d5db; }

        /* ── Form footer ── */
        .form-footer {
            display: flex;
            gap: .75rem;
            padding-top: 1.25rem;
            border-top: 1.5px solid var(--border);
            margin-top: 1.5rem;
        }
        .form-footer .btn { flex: 1; }
        @media (max-width: 480px) {
            .form-footer { flex-direction: column; }
        }
    </style>
</head>
<body>

<div class="page">
    <div class="card">

        <!-- Alert Success -->
        <div id="alertSuccess" class="alert alert-success">
            <svg fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span id="alertSuccessText">Data kehadiran berhasil disimpan!</span>
        </div>

        <!-- Alert Error -->
        <div id="alertError" class="alert alert-error">
            <svg fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span id="alertErrorText">Terjadi kesalahan</span>
        </div>

        <!-- Spinner -->
        <div id="spinner" class="spinner-wrap">
            <div class="spinner-ring"></div>
            <p>Mencari data…</p>
        </div>

        <!-- ══════════ STEP 1 ══════════ -->
        <div id="step1" class="step show">
            <p class="section-label">📷 Langkah 1 — Scan atau Input Kode</p>

            <div class="scanner-wrap">
                <!-- Scanner: murni HTML video+canvas, tidak ada library inject DOM -->
                <div class="scanner-box" id="scannerBox">
                    <video id="scanVideo" playsinline autoplay muted></video>
                    <canvas id="scanCanvas"></canvas>
                    <div class="scanner-overlay">
                        <div class="scan-frame">
                            <span></span>
                            <div class="scan-line"></div>
                        </div>
                    </div>
                    <button type="button" class="btn-flip" id="btnFlip" title="Balik kamera">🔄 Balik</button>
                </div>
                <p id="scanStatus" class="scanner-status">Mengaktifkan kamera…</p>
            </div>

            <div class="field">
                <label class="label" for="kodeToko">
                    Kode Toko / Kode Agen <span class="req">*</span>
                </label>
                <div class="input-icon-wrap">
                    <input type="text" id="kodeToko" class="input"
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

        <!-- ══════════ STEP 2 ══════════ -->
        <div id="step2" class="step">
            <div class="step2-header">
                <span class="step-badge">✓</span>
                <h3>Langkah 2 — Informasi Kehadiran</h3>
            </div>

            <form id="kehadiranForm">
                @csrf
                <input type="hidden" id="formId" name="id">

                <!-- Info Toko -->
                <div class="section-block">
                    <p class="section-heading">🏪 Informasi Toko / Agen</p>

                    <div class="grid-auto">
                        <div class="field">
                            <label class="label">Tipe</label>
                            <div class="readonly" id="tipeDisplay">-</div>
                        </div>
                        <div class="field">
                            <label class="label">Kode Toko / Agen</label>
                            <div class="readonly" id="kodeTokoDisplay">-</div>
                        </div>
                        <div class="field">
                            <label class="label">Lokasi Event</label>
                            <div class="readonly" id="lokasiEventDisplay">-</div>
                        </div>
                        <div class="field">
                            <label class="label">Nama Toko / Agen <span class="req">*</span></label>
                            <input type="text" id="namaToko" name="nama_toko" required class="input">
                        </div>
                    </div>

                    <div class="field" style="margin-top:.5rem;">
                        <label class="label">Kode Agen & Nama Agen</label>
                        <div class="agen-table">
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

                    <div class="grid-auto" style="margin-top:.5rem;">
                        <div class="field">
                            <label class="label">PIC <span class="req">*</span></label>
                            <input type="text" id="pic" name="pic" required class="input">
                        </div>
                        <div class="field">
                            <label class="label">Nomor PIC <span class="req">*</span></label>
                            <input type="text" id="nomorPic" name="nomor_pic" required class="input">
                        </div>
                        <div class="field">
                            <label class="label">Kota <span class="req">*</span></label>
                            <input type="text" id="kota" name="kota" required class="input">
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Alamat <span class="req">*</span></label>
                        <textarea id="alamat" name="alamat" required rows="3" class="input"></textarea>
                    </div>
                </div>

                <!-- Status Kehadiran -->
                <div class="section-block">
                    <p class="section-heading">📋 Status Kehadiran</p>
                    <div class="grid-auto">
                        <div class="field">
                            <label class="label">Status Hadir</label>
                            <div class="toggle-wrap">
                                <label class="toggle">
                                    <input type="checkbox" id="hadirCheckbox" name="hadir" checked disabled>
                                    <span class="slider"></span>
                                </label>
                                <span id="hadirText">✓ Hadir</span>
                            </div>
                            <input type="hidden" name="hadir" value="1">
                        </div>
                        <div class="field">
                            <label class="label">Jumlah Orang Hadir <span class="req">*</span></label>
                            <input type="number" id="jumlahKehadiran" name="jumlah_kehadiran"
                                min="0" value="0" class="input">
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button type="button" id="backBtn" class="btn btn-gray">← Kembali</button>
                    <button type="submit" id="submitBtn" class="btn btn-success">✔ Kirim Data Kehadiran</button>
                </div>
            </form>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
/* ═══════════════════════════════════════════
   SCANNER — native getUserMedia + jsQR
   Tidak ada library yang inject DOM / CSS
═══════════════════════════════════════════ */
const videoEl   = document.getElementById('scanVideo');
const canvasEl  = document.getElementById('scanCanvas');
const canvasCtx = canvasEl.getContext('2d');
const statusEl  = document.getElementById('scanStatus');

let scanRunning  = false;
let scanStream   = null;
let scanRAF      = null;
let scanLock     = false;
let lastCode     = '';
let currentFacing = 'environment'; // default: kamera belakang
let searchInProgress = false;

function setStatus(msg, type) {
    statusEl.textContent = msg;
    statusEl.className   = 'scanner-status' + (type ? ' ' + type : '');
}

async function startScanner() {
    if (scanRunning) return;
    try {
        scanStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: currentFacing }, width: { ideal: 1280 } }
        });
        videoEl.srcObject = scanStream;
        await videoEl.play();
        scanRunning = true;
        setStatus('Kamera aktif. Arahkan QR code ke frame.');
        requestAnimationFrame(tick);
    } catch (err) {
        setStatus('Gagal aktifkan kamera: ' + (err.message || err), 'err');
    }
}

function tick() {
    if (!scanRunning) return;
    if (videoEl.readyState === videoEl.HAVE_ENOUGH_DATA) {
        canvasEl.width  = videoEl.videoWidth;
        canvasEl.height = videoEl.videoHeight;
        canvasCtx.drawImage(videoEl, 0, 0);
        const imgData = canvasCtx.getImageData(0, 0, canvasEl.width, canvasEl.height);
        const result  = jsQR(imgData.data, imgData.width, imgData.height, {
            inversionAttempts: 'dontInvert'
        });
        if (result && result.data) {
            const code = result.data.trim();
            if (code && !scanLock && code !== lastCode) {
                scanLock = true;
                document.getElementById('kodeToko').value = code;
                setStatus('QR terbaca. Mencari data…');
                searchData(code);
                setTimeout(function() { scanLock = false; }, 1500);
            }
        }
    }
    scanRAF = requestAnimationFrame(tick);
}

async function stopScanner() {
    scanRunning = false;
    if (scanRAF)    { cancelAnimationFrame(scanRAF); scanRAF = null; }
    if (scanStream) { scanStream.getTracks().forEach(t => t.stop()); scanStream = null; }
    videoEl.srcObject = null;
}

/* Tombol flip kamera */
document.getElementById('btnFlip').addEventListener('click', async function() {
    currentFacing = currentFacing === 'environment' ? 'user' : 'environment';
    setStatus('Mengganti kamera…');
    await stopScanner();
    await startScanner();
});

/* ═══════════════════════════════════════════
   SEARCH DATA
═══════════════════════════════════════════ */
function searchData(kodeOverride) {
    const kode = (kodeOverride || document.getElementById('kodeToko').value || '').trim();
    if (!kode) { showError('Mohon masukkan kode toko atau kode agen'); return; }
    if (searchInProgress) return;

    searchInProgress = true;
    document.getElementById('spinner').classList.add('show');
    document.getElementById('step1').classList.remove('show');

    // Safety timeout 15 detik
    const safetyTimer = setTimeout(function() {
        if (!searchInProgress) return;
        searchInProgress = false;
        document.getElementById('spinner').classList.remove('show');
        document.getElementById('step1').classList.add('show');
        showError('Koneksi timeout. Silakan coba lagi.');
    }, 15000);

    const controller = new AbortController();
    const fetchTimer = setTimeout(function() { controller.abort(); }, 10000);

    fetch('{{ url('/api/kehadiran/get-toko') }}/' + encodeURIComponent(kode), {
        signal: controller.signal
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        clearTimeout(fetchTimer);
        clearTimeout(safetyTimer);
        document.getElementById('spinner').classList.remove('show');
        searchInProgress = false;

        if (data.success) {
            lastCode = kode;
            populateForm(data);
            document.getElementById('step1').classList.remove('show');
            document.getElementById('step2').classList.add('show');
            document.getElementById('alertSuccess').classList.remove('show');
            document.getElementById('alertError').classList.remove('show');
        } else {
            showError(data.message || 'Data tidak ditemukan');
            document.getElementById('step1').classList.add('show');
        }
    })
    .catch(function(err) {
        clearTimeout(fetchTimer);
        clearTimeout(safetyTimer);
        document.getElementById('spinner').classList.remove('show');
        searchInProgress = false;
        document.getElementById('step1').classList.add('show');
        if (err.name === 'AbortError') {
            showError('Koneksi timeout. Silakan coba lagi.');
        } else {
            showError('Terjadi kesalahan saat mencari data');
        }
    });
}

/* ═══════════════════════════════════════════
   POPULATE FORM
═══════════════════════════════════════════ */
function populateForm(data) {
    document.getElementById('formId').value         = data.data.id;
    document.getElementById('tipeDisplay').textContent = data.data.tipe || data.type || '-';
    document.getElementById('kodeTokoDisplay').textContent = data.data.kode_toko;
    document.getElementById('namaToko').value       = data.data.nama_toko;
    document.getElementById('pic').value            = data.data.pic;
    document.getElementById('nomorPic').value       = data.data.nomor_pic;
    document.getElementById('kota').value           = data.data.kota;
    document.getElementById('alamat').value         = data.data.alamat;
    document.getElementById('lokasiEventDisplay').textContent = data.data.lokasi_event || '-'; 
    document.getElementById('hadirCheckbox').checked = true;
    document.getElementById('jumlahKehadiran').value = data.data.jumlah_kehadiran || 0;

    const agenInfo = Array.isArray(data.data.agen_info) ? data.data.agen_info : [];
    document.getElementById('agenInfoBody').innerHTML = agenInfo.length === 0
        ? '<tr><td colspan="2" style="color:var(--muted)">-</td></tr>'
        : agenInfo.map(function(a) {
            return '<tr><td>' + (a.kode_agen || '-') + '</td><td>' + (a.nama_agen || '-') + '</td></tr>';
          }).join('');
}

/* ═══════════════════════════════════════════
   BACK
═══════════════════════════════════════════ */
document.getElementById('backBtn').addEventListener('click', function() {
    scanLock = false;
    searchInProgress = false;
    document.getElementById('kodeToko').value = '';
    document.getElementById('step2').classList.remove('show');
    document.getElementById('spinner').classList.remove('show');
    document.getElementById('step1').classList.add('show');
    setStatus('Kamera aktif. Arahkan QR code ke frame.');
    document.getElementById('kodeToko').focus();
});

/* ═══════════════════════════════════════════
   AUTO UPPERCASE PIC, NOMOR PIC, KOTA, ALAMAT
═══════════════════════════════════════════ */
['pic', 'nomorPic', 'kota', 'alamat'].forEach(function(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
});

/* ═══════════════════════════════════════════
   SUBMIT
═══════════════════════════════════════════ */
document.getElementById('kehadiranForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const required = ['namaToko', 'pic', 'nomorPic', 'kota', 'alamat'];
    for (const id of required) {
        if (!document.getElementById(id).value.trim()) {
            showError('Nama Toko, PIC, Nomor PIC, Kota, dan Alamat wajib diisi.');
            return;
        }
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Mengirim…';

    const payload = {
        id:               document.getElementById('formId').value,
        nama_toko:        document.getElementById('namaToko').value,
        pic:              document.getElementById('pic').value.toUpperCase(),
        nomor_pic:        document.getElementById('nomorPic').value.toUpperCase(),
        kota:             document.getElementById('kota').value.toUpperCase(),
        alamat:           document.getElementById('alamat').value.toUpperCase(),
        hadir:            1,
        jumlah_kehadiran: parseInt(document.getElementById('jumlahKehadiran').value) || 0,
    };

    fetch('{{ route('kehadiran.submit') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        submitBtn.disabled = false;
        submitBtn.textContent = '✔ Kirim Data Kehadiran';

        if (data.success) {
            showSuccess('Data kehadiran berhasil disimpan!');
            setTimeout(function() {
                document.getElementById('kodeToko').value = '';
                lastCode = '';
                document.getElementById('kehadiranForm').reset();
                document.getElementById('step2').classList.remove('show');
                document.getElementById('step1').classList.add('show');
                window.scrollTo({ top: 0, behavior: 'smooth' });
                document.getElementById('kodeToko').focus();
            }, 2000);
        } else {
            showError(data.message || 'Terjadi kesalahan saat menyimpan data');
        }
    })
    .catch(function(err) {
        submitBtn.disabled = false;
        submitBtn.textContent = '✔ Kirim Data Kehadiran';
        console.error(err);
        showError('Terjadi kesalahan saat mengirim data');
    });
});

/* ═══════════════════════════════════════════
   SEARCH button & Enter key
═══════════════════════════════════════════ */
document.getElementById('searchBtn').addEventListener('click', function() {
    searchData();
});
document.getElementById('kodeToko').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); searchData(); }
});

/* ═══════════════════════════════════════════
   HELPERS
═══════════════════════════════════════════ */
function showSuccess(msg) {
    document.getElementById('alertSuccessText').textContent = msg;
    document.getElementById('alertSuccess').classList.add('show');
    setTimeout(function() { document.getElementById('alertSuccess').classList.remove('show'); }, 5000);
}

function showError(msg) {
    document.getElementById('alertErrorText').textContent = msg;
    document.getElementById('alertError').classList.add('show');
    setTimeout(function() { document.getElementById('alertError').classList.remove('show'); }, 5000);
}

/* ═══════════════════════════════════════════
   INIT
═══════════════════════════════════════════ */
window.addEventListener('load', function() {
    document.getElementById('kodeToko').focus();
    startScanner();
});
window.addEventListener('beforeunload', function() { stopScanner(); });
</script>
</body>
</html>