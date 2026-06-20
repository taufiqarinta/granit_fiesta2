<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Scan QR Code Survey') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <style>
                        *, *::before, *::after { box-sizing: border-box; }

                        :root {
            --bg: #fff5f5;
            --surface: #ffffff;
            --panel: #fff7f7;
            --border: #fde2e2;
            --accent: #ef4444;
            --accent-dk: #dc2626;
            --success: #16a34a;
            --text: #1e293b;
            --muted: #6b7280;
            --shadow-lg: 0 8px 32px rgba(239,68,68,.08);

                        .scan-shell {
                            background: linear-gradient(180deg, #fff 0%, #f8faff 100%);
                            border: 1px solid var(--border);
                            border-radius: 20px;
                            padding: 1.5rem;
                            box-shadow: var(--shadow-lg);
                        }

                        .scan-grid {
                            display: grid;
                            grid-template-columns: 1.2fr .8fr;
                            gap: 1.25rem;
                        }

                        @media (max-width: 900px) {
                            .scan-grid { grid-template-columns: 1fr; }
                        }

                        .panel {
                            background: var(--panel);
                            border: 1.5px solid var(--border);
                            border-radius: 14px;
                            padding: 1.1rem;
                        }

                        .section-label {
                            font-size: .68rem;
                            font-weight: 700;
                            letter-spacing: .12em;
                            text-transform: uppercase;
                            color: var(--accent);
                            margin-bottom: .9rem;
                        }

                        .scanner-box {
                            position: relative;
                            width: 100%;
                            background: #0f172a;
                            border-radius: 12px;
                            overflow: hidden;
                            margin-bottom: .75rem;
                            aspect-ratio: 4 / 3;
                        }

                        .scanner-box video {
                            position: absolute;
                            inset: 0;
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            display: block;
                        }

                        .scanner-box canvas { display: none; }

                        .scanner-overlay {
                            position: absolute;
                            inset: 0;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            pointer-events: none;
                        }

                        .scanner-frame {
                            width: 55%;
                            aspect-ratio: 1;
                            position: relative;
                        }

                        .scanner-frame::before,
                        .scanner-frame::after,
                        .scanner-frame > span::before,
                        .scanner-frame > span::after {
                            content: '';
                            position: absolute;
                            width: 22px;
                            height: 22px;
                            border-color: #4ade80;
                            border-style: solid;
                        }

                        .scanner-frame::before { top: 0; left: 0; border-width: 3px 0 0 3px; }
                        .scanner-frame::after { top: 0; right: 0; border-width: 3px 3px 0 0; }
                        .scanner-frame > span::before { bottom: 0; left: 0; border-width: 0 0 3px 3px; }
                        .scanner-frame > span::after { bottom: 0; right: 0; border-width: 0 3px 3px 0; }

                        .scan-line {
                            position: absolute;
                            left: 0;
                            right: 0;
                            height: 2px;
                            background: linear-gradient(90deg, transparent, #4ade80, transparent);
                            animation: scanMove 2s ease-in-out infinite;
                            top: 0;
                        }

                        @keyframes scanMove {
                            0% { top: 10%; }
                            50% { top: 88%; }
                            100% { top: 10%; }
                        }

                        .scanner-status {
                            text-align: center;
                            font-size: .82rem;
                            font-weight: 500;
                            color: var(--muted);
                            margin-top: .5rem;
                            min-height: 1.4em;
                            padding: 0 .5rem;
                        }

                        .scanner-status.ok { color: var(--success); }
                        .scanner-status.err { color: #ef4444; }

                        .input {
                            width: 100%;
                            border: 1.5px solid var(--border);
                            border-radius: 8px;
                            padding: .6rem .85rem;
                            font-size: 16px;
                            color: var(--text);
                            background: #fff;
                            outline: none;
                        }

                        .input:focus {
                            border-color: var(--accent);
                            box-shadow: 0 0 0 3px rgba(67,97,238,.12);
                        }

                        .btn {
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            gap: .35rem;
                            border: none;
                            border-radius: 8px;
                            padding: .65rem 1rem;
                            font-size: .88rem;
                            font-weight: 600;
                            cursor: pointer;
                            transition: transform .12s, background .15s;
                            white-space: nowrap;
                            min-height: 44px;
                            text-decoration: none;
                        }

                        .btn:active { transform: scale(.96); }
                        .btn-primary { background: var(--accent); color: #fff; }
                        .btn-primary:hover { background: var(--accent-dk); }
                        .btn-outline { background: #fff; color: var(--muted); border: 1.5px solid var(--border); }
                        .btn-outline:hover { border-color: var(--accent); color: var(--accent); }

                        .field { margin-bottom: .9rem; }

                        .label {
                            display: block;
                            font-size: .74rem;
                            font-weight: 600;
                            color: var(--muted);
                            margin-bottom: .3rem;
                            letter-spacing: .03em;
                        }

                        .input-row { display: flex; gap: .5rem; }
                        .input-row .input { flex: 1; min-width: 0; }

                        .toast {
                            display: none;
                            position: fixed;
                            top: 1.25rem;
                            right: 1.25rem;
                            background: var(--surface);
                            border-left: 4px solid var(--success);
                            border-radius: 12px;
                            padding: 1rem 1rem 1rem 1.15rem;
                            max-width: 400px;
                            width: calc(100% - 2.5rem);
                            box-shadow: var(--shadow-lg);
                            z-index: 9999;
                        }

                        .toast.show { display: block; }

                        .toast-title { font-size: .9rem; font-weight: 700; color: var(--success); margin-bottom: .35rem; }
                        .toast-body { font-size: .82rem; color: var(--text); line-height: 1.5; }
                    </style>

                    <div class="scan-shell">
                        <div class="scan-grid">
                            <div class="panel">
                                <p class="section-label">📷 Scan QR Survey</p>

                                <div class="scanner-box">
                                    <video id="scanVideo" playsinline autoplay muted></video>
                                    <canvas id="scanCanvas"></canvas>
                                    <div class="scanner-overlay">
                                        <div class="scanner-frame">
                                            <span></span>
                                            <div class="scan-line"></div>
                                        </div>
                                    </div>
                                </div>

                                <div style="display:flex; gap:.5rem; align-items:center; margin-bottom:.5rem;">
                                    <p id="scanStatus" class="scanner-status" style="flex:1; margin:0;">Mengaktifkan kamera…</p>
                                    <button type="button" id="btnFlip" class="btn btn-outline" style="padding:.4rem .7rem; min-height:unset; font-size:.8rem;">
                                        🔄 Balik
                                    </button>
                                </div>

                                <button type="button" id="btnScanUlang" class="btn btn-outline" style="width:100%; margin-top:.6rem;">
                                    ⟳ Scan Ulang
                                </button>
                            </div>

                            <div class="panel">
                                <p class="section-label">⌨️ Input Manual</p>

                                <div class="field">
                                    <label class="label" for="kodeSurveyInput">Masukkan Kode Survey</label>
                                    <div class="input-row">
                                        <input
                                            type="text"
                                            id="kodeSurveyInput"
                                            class="input"
                                            placeholder="Contoh: SURVEY-ABC123"
                                            autocomplete="off"
                                        >
                                        <button type="button" id="btnCariSurvey" class="btn btn-primary">Cari</button>
                                    </div>
                                    <p class="scanner-status" id="searchMessage" style="text-align:left; margin-top:.5rem;"></p>
                                </div>

                                <div class="panel" style="background:#fff; margin-top:1rem; border-style:dashed;">
                                    <p style="font-size:.85rem; color:var(--muted); line-height:1.6;">
                                        Setelah QR terbaca atau kode survey dimasukkan, sistem akan langsung membuka halaman detail survey.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="toast" class="toast">
        <p class="toast-title">✔ Berhasil</p>
        <p class="toast-body" id="toastMsg"></p>
    </div>
</x-app-layout>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

<script>
    const surveyDetailBaseUrl = @json(url('/form-survey'));
const videoEl = document.getElementById('scanVideo');
const canvasEl = document.getElementById('scanCanvas');
const canvasCtx = canvasEl.getContext('2d');
const statusEl = document.getElementById('scanStatus');
const searchMessageEl = document.getElementById('searchMessage');

let scanRunning = false;
let scanStream = null;
let scanRAF = null;
let scanLock = false;
let lastCode = '';
let useRearCamera = true;

function setStatus(msg, type) {
    statusEl.textContent = msg;
    statusEl.className = 'scanner-status' + (type ? ' ' + type : '');
}

function setSearchMessage(msg, type) {
    searchMessageEl.textContent = msg || '';
    searchMessageEl.className = 'scanner-status' + (type ? ' ' + type : '');
}

function showToast(message) {
    document.getElementById('toastMsg').textContent = message;
    const toast = document.getElementById('toast');
    toast.classList.add('show');
    setTimeout(function () {
        toast.classList.remove('show');
    }, 3000);
}

async function startScanner() {
    if (scanRunning) return;

    try {
        scanStream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: { ideal: useRearCamera ? 'environment' : 'user' },
                width: { ideal: 1280 }
            }
        });

        videoEl.srcObject = scanStream;
        await videoEl.play();
        scanRunning = true;
        setStatus('Kamera aktif. Arahkan QR survey ke frame.', 'ok');
        requestAnimationFrame(tick);
    } catch (err) {
        setStatus('Gagal aktifkan kamera: ' + (err.message || err), 'err');
    }
}

function tick() {
    if (!scanRunning) return;

    if (videoEl.readyState === videoEl.HAVE_ENOUGH_DATA) {
        canvasEl.width = videoEl.videoWidth;
        canvasEl.height = videoEl.videoHeight;
        canvasCtx.drawImage(videoEl, 0, 0);

        const imgData = canvasCtx.getImageData(0, 0, canvasEl.width, canvasEl.height);
        const result = jsQR(imgData.data, imgData.width, imgData.height, {
            inversionAttempts: 'dontInvert'
        });

        if (result && result.data) {
            const code = result.data.trim();
            if (code && !scanLock && code !== lastCode) {
                scanLock = true;
                lastCode = code;
                setStatus('QR terbaca. Membuka detail survey...', 'ok');
                openSurveyDetail(code);
            }
        }
    }

    scanRAF = requestAnimationFrame(tick);
}

async function stopScanner() {
    scanRunning = false;
    if (scanRAF) {
        cancelAnimationFrame(scanRAF);
        scanRAF = null;
    }
    if (scanStream) {
        scanStream.getTracks().forEach(function (track) { track.stop(); });
        scanStream = null;
    }
    videoEl.srcObject = null;
    setStatus('Kamera dihentikan.');
}

function openSurveyDetail(kodeSurvey) {
    kodeSurvey = (kodeSurvey || '').trim();
    if (!kodeSurvey) {
        scanLock = false;
        setStatus('Kode survey kosong.', 'err');
        return;
    }

    $.get('{{ route('form-survey.search') }}', { kode_survey: kodeSurvey })
        .done(function (res) {
            if (res && res.success) {
                window.location.href = `${surveyDetailBaseUrl}/${encodeURIComponent(res.data.kode_survey)}/detail`;
                return;
            }

            scanLock = false;
            setStatus(res.message || 'Kode survey tidak ditemukan.', 'err');
            showToast(res.message || 'Kode survey tidak ditemukan.');
        })
        .fail(function (xhr) {
            scanLock = false;
            const message = xhr.responseJSON?.message || 'Kode survey tidak ditemukan.';
            setStatus(message, 'err');
            showToast(message);
        });
}

function manualSearch() {
    const kodeSurvey = document.getElementById('kodeSurveyInput').value.trim();

    if (!kodeSurvey) {
        setSearchMessage('Masukkan kode survey terlebih dahulu', 'err');
        return;
    }

    setSearchMessage('Mencari survey...', 'ok');
    openSurveyDetail(kodeSurvey);
}

document.getElementById('btnCariSurvey').addEventListener('click', manualSearch);

document.getElementById('kodeSurveyInput').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        manualSearch();
    }
});

document.getElementById('btnScanUlang').addEventListener('click', function () {
    stopScanner().then(function () {
        scanLock = false;
        lastCode = '';
        setSearchMessage('');
        startScanner();
    });
});

document.getElementById('btnFlip').addEventListener('click', function () {
    useRearCamera = !useRearCamera;
    stopScanner().then(function () {
        scanLock = false;
        lastCode = '';
        startScanner();
    });
});

window.addEventListener('load', startScanner);
window.addEventListener('beforeunload', function () {
    stopScanner();
});
</script>
