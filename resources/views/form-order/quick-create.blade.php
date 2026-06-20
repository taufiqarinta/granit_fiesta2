<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Form Order — Scan</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #fff5f5; /* soft rose background */
            --surface:   #ffffff;
            --panel:     #fff7f7; /* very light rose panel */
            --border:    #fde2e2; /* pale red border */
            --accent:    #ef4444; /* primary red */
            --accent-dk: #dc2626; /* darker red */
            --accent-lt: #fff1f1; /* light red/rose */
            --success:   #16a34a; /* keep success green for positive actions */
            --success-dk:#15803d;
            --text:      #1e293b;
            --muted:     #6b7280; /* neutral muted gray */
            --radius:    12px;
            --shadow-lg: 0 8px 32px rgba(239,68,68,.08);
        }

        body {
            background: var(--bg);
            font-family: 'Sora', sans-serif;
            color: var(--text);
            min-height: 100vh;
        }

        /* ── Page wrapper ── */
        .page { padding: 1.25rem 1rem 3rem; }

        .card {
            background: var(--surface);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            padding: 1.75rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 1.5rem;
        }

        /* ── Two-column grid ── */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 860px) {
            .grid-2 { grid-template-columns: 1fr; }
            .card { padding: 1rem; border-radius: 14px; }
            .page { padding: .75rem .5rem 2rem; }
        }

        /* ── Panel ── */
        .panel {
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            padding: 1.25rem;
        }

        .section-label {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        /* ── Scanner ── */
        .scanner-box {
            position: relative;
            width: 100%;
            background: #0f172a;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: .75rem;
            /* KUNCI: fixed aspect ratio, tidak bergantung pada library */
            aspect-ratio: 4 / 3;
        }

        .scanner-box.hidden {
            display: none;
        }

        .scanner-box video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .scanner-box canvas {
            display: none;
        }

        /* Overlay crosshair */
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

        .scanner-frame::before  { top: 0;    left: 0;  border-width: 3px 0 0 3px; }
        .scanner-frame::after   { top: 0;    right: 0; border-width: 3px 3px 0 0; }
        .scanner-frame > span::before { bottom: 0; left: 0;  border-width: 0 0 3px 3px; }
        .scanner-frame > span::after  { bottom: 0; right: 0; border-width: 0 3px 3px 0; }

        /* Scan line */
        .scan-line {
            position: absolute;
            left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #4ade80, transparent);
            animation: scanMove 2s ease-in-out infinite;
            top: 0;
        }

        @keyframes scanMove {
            0%   { top: 10%; }
            50%  { top: 88%; }
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

        .scanner-status.ok  { color: var(--success); }
        .scanner-status.err { color: #ef4444; }

        /* ── Field ── */
        .field { margin-bottom: .9rem; }

        .label {
            display: block;
            font-size: .74rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: .3rem;
            letter-spacing: .03em;
        }

        .input {
            width: 100%;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: .6rem .85rem;
            font-size: .9rem;
            font-family: 'Sora', sans-serif;
            color: var(--text);
            background: #fff;
            transition: border-color .18s, box-shadow .18s;
            outline: none;
            /* Mencegah zoom di iOS */
            font-size: 16px;
        }

        .input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(67,97,238,.12);
        }

        .input[readonly] {
            background: var(--panel);
            color: var(--muted);
            cursor: default;
        }

        .input-success {
            background: #ecfdf5 !important;
            border-color: #4ade80 !important;
            color: #064e3b !important;
        }

        .input-success:focus {
            border-color: #22c55e !important;
            box-shadow: 0 0 0 3px rgba(52, 211, 153, .15);
        }

        .summary-panel {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
            padding: .75rem 1rem;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid rgba(156, 163, 175, .2);
            align-items: center;
            min-height: 48px;  
        }

        .summary-item {
            font-size: .92rem;
            font-weight: 600;
            color: #0f172a;
        }

        .summary-item strong {
            color: #0f172a;
            margin-right: .35rem;
        }

        .input-row {
            display: flex;
            gap: .5rem;
        }

        .input-row .input { flex: 1; min-width: 0; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            border: none;
            border-radius: 8px;
            padding: .6rem 1rem;
            font-family: 'Sora', sans-serif;
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform .12s, background .15s;
            white-space: nowrap;
            min-height: 44px;
        }

        .btn:active { transform: scale(.96); }

        .btn-primary {
            background: var(--accent);
            color: #fff;
        }

        .btn-primary:hover { background: var(--accent-dk); }

        .btn-success {
            background: var(--success);
            color: #fff;
        }

        .btn-success:hover { background: var(--success-dk); }

        .btn-outline {
            background: #fff;
            color: var(--muted);
            border: 1.5px solid var(--border);
        }

        .btn-outline:hover { border-color: var(--accent); color: var(--accent); }

        .btn-danger {
            background: #fff;
            color: #ef4444;
            border: 1.5px solid #fca5a5;
            font-size: .75rem;
            padding: .3rem .65rem;
            min-height: unset;
        }

        .btn-danger:hover { background: #fef2f2; }

        .ttd-box .btn {
            font-size: .75rem;
            padding: .45rem .75rem;
            min-height: 16px;
        }

        .ttd-box .btn-danger {
            font-size: .68rem;
            padding: .25rem .55rem;
        }

        .swal2-container {
            z-index: 99999 !important;
        }

        .btn-full { width: 100%; }

        /* ── Divider ── */
        .divider { border: none; border-top: 1.5px solid var(--border); margin: 1.25rem 0; }

        /* ── Paket rows ── */
        .paket-list { display: flex; flex-direction: column; gap: .6rem; }

        .paket-row {
            display: flex;
            align-items: center;
            gap: .65rem;
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: .6rem .85rem;
        }

        .paket-name { flex: 1; font-size: .85rem; font-weight: 500; }

        .paket-badge {
            font-size: .7rem;
            font-weight: 600;
            background: var(--accent-lt);
            color: var(--accent);
            border-radius: 20px;
            padding: .15rem .5rem;
            white-space: nowrap;
        }

        .paket-qty {
            width: 5.5rem;
            text-align: center;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            padding: .4rem .5rem;
            font-size: 16px;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 500;
            color: var(--text);
            background: #fff;
            outline: none;
            -moz-appearance: textfield;
        }

        .paket-qty::-webkit-outer-spin-button,
        .paket-qty::-webkit-inner-spin-button { -webkit-appearance: none; }

        .paket-qty:focus { border-color: var(--accent); }

        @media (max-width: 480px) {
            .paket-row { flex-wrap: wrap; }
            .paket-qty { width: 100%; }
        }

        /* ── Signature ── */
        .ttd-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .85rem;
            margin-top: 1rem;
        }

        @media (max-width: 640px) {
            .ttd-grid { grid-template-columns: 1fr; }
        }

        .ttd-box {
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: .85rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .5rem;
        }

        .ttd-box.invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,.12);
        }

        .ttd-label {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--accent);
        }

        .ttd-canvas-wrap {
            position: relative;
            width: 100%;
            border: 1.5px dashed var(--border);
            border-radius: 8px;
            background: #fff;
            overflow: hidden;
            touch-action: none;
        }

        .ttd-canvas-wrap canvas {
            display: block;
            width: 100%;
            height: 240px;
            cursor: crosshair;
            touch-action: none;
        }

        .ttd-preview {
            width: 100%;
            min-height: 160px;
            border: 1.5px dashed var(--border);
            border-radius: 8px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: .85rem;
            text-align: center;
            color: #c8d3f0;
            font-size: .85rem;
            font-weight: 500;
            overflow: hidden;
        }

        .ttd-preview.empty { color: #c8d3f0; }

        .ttd-preview-img {
            width: 100%;
            max-height: 140px;
            display: block;
            object-fit: contain;
        }

        .ttd-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            color: #c8d3f0;
            font-size: .75rem;
            font-weight: 500;
            transition: opacity .2s;
        }

        .ttd-canvas-wrap.has-sig .ttd-placeholder { opacity: 0; }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .65);
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 9999;
        }

        .modal.show { display: flex; }

        .modal-dialog {
            width: min(100%, 720px);
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 16px 48px rgba(15, 23, 42, .18);
            overflow: hidden;
            animation: modalFade .18s ease-out;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.1rem 1.25rem .75rem;
            border-bottom: 1px solid rgba(15, 23, 42, .08);
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
        }

        .modal-body {
            padding: 0 1.25rem 1rem;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: .75rem;
            padding: 0 1.25rem 1.25rem;
        }

        .modal-close {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 10px;
            padding: 0;
            font-size: 1.2rem;
            line-height: 1;
        }

        @keyframes modalFade {
            from { transform: translateY(20px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }

        /* ── Footer ── */
        .form-footer {
            display: flex;
            justify-content: flex-end;
            gap: .75rem;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1.5px solid var(--border);
            flex-wrap: wrap;
        }

        @media (max-width: 480px) {
            .form-footer { flex-direction: column; }
            .form-footer .btn { width: 100%; }
        }

        /* ── Toast Alert ── */
        .toast {
            display: none;
            position: fixed;
            top: 1.25rem;
            right: 1.25rem;
            background: var(--surface);
            border-left: 4px solid var(--success);
            border-radius: var(--radius);
            padding: 1.25rem 1rem;
            max-width: 400px;
            width: calc(100% - 2.5rem);
            box-shadow: var(--shadow-lg);
            z-index: 9999;
            animation: slideIn .3s ease-out;
        }

        .toast.show { display: block; }

        @keyframes slideIn {
            from { transform: translateX(420px); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }

        .toast-title { font-size: .9rem; font-weight: 700; color: var(--success); margin-bottom: .5rem; }
        .toast-body  { font-size: .82rem; color: var(--text); line-height: 1.5; }

        .toast-detail {
            background: var(--accent-lt);
            border-radius: 8px;
            padding: .65rem;
            margin-top: .65rem;
            font-size: .78rem;
        }

        .toast-detail strong { display: block; color: var(--accent); font-weight: 700; margin-bottom: .25rem; }

        .toast-close {
            position: absolute;
            top: .6rem; right: .6rem;
            background: none; border: none;
            cursor: pointer; color: var(--muted);
            font-size: 1.1rem; line-height: 1;
        }

        .toast-close:hover { color: var(--text); }

        /* ── Utility ── */
        .mt-sm { margin-top: .6rem; }
    </style>
</head>
<body>

<!-- Toast -->
<div id="toast" class="toast">
    <button class="toast-close" onclick="closeToast()">&times;</button>
    <p class="toast-title">✔ Berhasil!</p>
    <p class="toast-body" id="toastMsg"></p>
    <div class="toast-detail">
        <strong id="toastKodeLabel">Kode Unik:</strong>
        <div id="toastKode"></div>
        <div style="margin-top:.4rem;">
            <strong style="display:inline;font-weight:600;">Jumlah Voucher:</strong>
            <span id="toastVoucher" style="margin-left:.3rem;"></span> voucher
        </div>
    </div>
</div>

<div class="page">
    <div class="card">
        <h2 class="card-title">📦 Form Order — Scan</h2>

        <div class="grid-2">

            <!-- ════════ KIRI: Scanner & Info Toko ════════ -->
            <div class="panel">
                <p class="section-label">📷 Scan Toko</p>

                <!-- Scanner murni HTML — tidak pakai library apapun -->
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

                <button type="button" id="btnScanUlang" class="btn btn-outline btn-full mt-sm">
                    ⟳ Scan Ulang
                </button>

                <div style="margin-top:1rem;">
                    <div class="field">
                        <label class="label">Kode Toko</label>
                        <div class="input-row">
                            <input type="text" id="kode_toko_input" class="input" placeholder="Scan atau masukkan kode toko" autocomplete="off">
                            <button type="button" id="btnLookupToko" class="btn btn-primary">🔍 Cari</button>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Lokasi Event</label>
                        <input type="text" id="lokasi_event" readonly class="input" value="{{ $defaultLokasi->nama_lokasi ?? '' }}">
                    </div>

                    <div class="field">
                        <label class="label">PIC</label>
                        <input type="text" id="pic" readonly class="input">
                    </div>

                    <div class="field">
                        <label class="label">No. HP</label>
                        <input type="text" id="no_hp" readonly class="input">
                    </div>

                    <div class="field">
                        <label class="label">Kota / Kabupaten</label>
                        <input type="text" id="kota" readonly class="input">
                    </div>
                </div>
            </div>

            <!-- ════════ KANAN: Form Order ════════ -->
            <div class="panel">
                <p class="section-label">🗒️ Detail Order</p>

                <form id="scanForm" action="{{ route('form-order.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="source" value="quick-scan">
                    <div class="field">
                        <label class="label">Kode Agen</label>
                        <div class="input-row">
                            <input type="text" id="kode_agen_manual_input" class="input" placeholder="Masukkan kode agen lalu klik Cari" autocomplete="off">
                            <button type="button" id="btnLookupAgen" class="btn btn-primary">🔍 Cari</button>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Kode Agen Terpilih</label>
                        <input type="text" id="kode_agen_input" name="kode_agen_input" readonly class="input" placeholder="Belum ada agen dipilih">
                    </div>

                    <div class="field">
                        <label class="label">Nama Agen</label>
                        <input type="text" id="nama_agen" readonly class="input">
                    </div>

                    <div class="field">
                        <label class="label">Brand</label>
                        <input type="text" id="brand" name="brand" readonly class="input">
                    </div>

                    <div class="field">
                        <label class="label">Nama Sales</label>
                        <input type="text" id="nama_sales" name="nama_sales" class="input">
                    </div>

                    <hr class="divider">
                    <p class="section-label">📦 Pilih Paket</p>

                    <div class="paket-list">
                        @foreach($masterTargets as $target)
                        <div class="paket-row">
                            <span class="paket-name">{{ $target->target }}</span>
                            <span class="paket-badge">{{ $target->point }} pt</span>
                            <input
                                type="number"
                                min="0"
                                name="targets[{{ $loop->index }}][jumlah_pengambilan]"
                                id="jumlah_{{ $target->id }}"
                                data-point="{{ $target->point }}"
                                data-kupon="{{ $target->kupon ?? 0 }}"
                                value="0"
                                class="paket-qty"
                            >
                            <input type="hidden" name="targets[{{ $loop->index }}][master_target_id]" value="{{ $target->id }}">
                        </div>
                        @endforeach
                    </div>

                    <div class="summary-panel">
                        <div class="summary-item"><strong>Total Point:</strong> <span id="total_points_display">0</span></div>
                        <div class="summary-item"><strong>Total Kupon:</strong> <span id="total_kupon_display">0</span></div>
                    </div>

                    <!-- Tanda Tangan -->
                    <hr class="divider">
                    <p class="section-label">✍️ Tanda Tangan</p>

                    <div class="field">
                        <label class="label">Nama Terang <span style="color:#ef4444">*</span></label>
                        <input type="text" id="nama_terang_input" name="nama_terang" class="input" 
                            placeholder="Masukkan nama terang" autocomplete="off" required>
                    </div>

                    <div class="ttd-grid" style="grid-template-columns: 1fr;">
                        <div class="ttd-box" data-sign-key="nama_terang">
                            <span class="ttd-label">Tanda Tangan</span>
                            <div class="ttd-preview empty" id="preview-nama_terang">
                                <span>TTD belum diisi</span>
                            </div>
                            <button type="button" class="btn btn-primary btn-full" 
                                    data-sign-button="nama_terang" 
                                    onclick="openSignatureModal('nama_terang')">
                                ✍️ Isi Tanda Tangan
                            </button>
                            <button type="button" class="btn btn-danger" 
                                    onclick="clearTTD('nama_terang')">
                                ✕ Hapus
                            </button>
                            <input type="hidden" name="ttd_nama_terang" id="ttd_nama_terang_hidden">
                        </div>
                    </div>

                    <!-- Modal Tanda Tangan -->
                    <div id="signatureModal" class="modal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-header">
                                <h3 id="modalTitle">Tanda Tangan</h3>
                                <button type="button" class="modal-close btn btn-outline" onclick="closeSignatureModal()" aria-label="Tutup">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="ttd-canvas-wrap" id="wrap-modal">
                                    <canvas id="modal-canvas" width="800" height="240"></canvas>
                                    <span class="ttd-placeholder">Goreskan tanda tangan di sini</span>
                                </div>
                            </div>
                            <div class="modal-actions">
                                <button type="button" class="btn btn-outline" id="btnModalClear">Bersihkan</button>
                                <button type="button" class="btn btn-success" id="btnModalSave">Simpan</button>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" name="nama_toko"    id="nama_toko_hidden">
                    <input type="hidden" name="kode_toko"    id="kode_toko_hidden">
                    <input type="hidden" name="pic"          id="pic_hidden">
                    <input type="hidden" name="no_hp"        id="no_hp_hidden">
                    <input type="hidden" name="kota"         id="kota_hidden">
                    <input type="hidden" name="lokasi_event" id="lokasi_event_hidden">
                    <input type="hidden" name="nama_agen"    id="nama_agen_id_hidden">
                    <input type="hidden" name="nama_agen_id" id="nama_agen_id_hidden_alt">
                    <input type="hidden" name="pic_old"      id="pic_old_hidden">
                    <input type="hidden" name="nomor_pic_old" id="nomor_pic_old_hidden">

                    <div class="form-footer">
                        <a href="{{ route('form-order.index') }}" class="btn btn-outline">✕ Batal</a>
                        <button type="submit" id="formSubmitButton" class="btn btn-success">✔ Simpan Order</button>
                    </div>
                </form>
            </div>

        </div><!-- end grid-2 -->
    </div><!-- end card -->
</div><!-- end page -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/* ═══════════════════════════════════════════
   SCANNER — native getUserMedia + jsQR
   Tidak ada library yang inject DOM / CSS
═══════════════════════════════════════════ */
const videoEl    = document.getElementById('scanVideo');
const canvasEl   = document.getElementById('scanCanvas');
const canvasCtx  = canvasEl.getContext('2d');
const statusEl   = document.getElementById('scanStatus');

let scanRunning  = false;
let scanStream   = null;
let scanRAF      = null;
let scanLock     = false;
let lastCode     = '';

function setStatus(msg, type) {
    statusEl.textContent  = msg;
    statusEl.className    = 'scanner-status' + (type ? ' ' + type : '');
}

async function startScanner() {
    if (scanRunning) return;
    try {
        // Tampilkan scanner box
        document.querySelector('.scanner-box').classList.remove('hidden');
        scanStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 } }
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
                lastCode = code;
                document.getElementById('kode_toko_input').value = code.toUpperCase();
                setStatus('QR terbaca. Mencari data…');
                doLookupToko(code).always(function() {
                    setTimeout(function() { scanLock = false; }, 1500);
                });
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
    // Sembunyikan scanner box saat terkunci
    document.querySelector('.scanner-box').classList.add('hidden');
    setStatus('Data toko ditemukan. Klik "Scan Ulang" untuk scan toko lain.');
}

/* Scan ulang → reset toko data saja dan restart scanner tanpa reload */
document.getElementById('btnScanUlang').addEventListener('click', function() {
    resetTokoData();
    startScanner();
});

$('#btnLookupToko').on('click', function() {
    const kode = $('#kode_toko_input').val().trim();
    if (!kode) { alertErr('Masukkan kode toko terlebih dahulu'); return; }
    // Reset data toko lama sebelum lookup baru
    resetTokoDataOnly();
    doLookupToko(kode);
});

$('#kode_toko_input').on('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); $('#btnLookupToko').click(); }
});

$('#btnLookupAgen').on('click', function() {
    const kode = $('#kode_agen_manual_input').val().trim();
    if (!kode) { alertErr('Masukkan kode agen terlebih dahulu'); return; }
    doLookupAgen(kode);
});

$('#kode_agen_manual_input').on('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); $('#btnLookupAgen').click(); }
});

$('#kode_agen_manual_input').on('input', function() {
    // Jika input dikosongkan, reset semua status
    if ($(this).val().trim() === '') {
        resetOrderForm();
        isEditingOrder = false;
        currentOrderId = null;
        $('#formSubmitButton').html('✔ Simpan Order');
        $('#formSubmitButton').removeClass('btn-warning').addClass('btn-success');
        $('#kode_agen_input, #nama_agen, #brand').val('');
        $('#nama_agen_id_hidden, #nama_agen_id_hidden_alt').val('');
    }
});
/* ═══════════════════════════════════════════
   TANDA TANGAN
═══════════════════════════════════════════ */
const signatureTitleMap = {
    nama_terang: 'Tanda Tangan'
};

const signatureHiddenMap = {
    nama_terang: '#ttd_nama_terang_hidden'
};

const signaturePreviewMap = {
    nama_terang: 'preview-nama_terang'
};

const signatureButtonMap = {
    nama_terang: 'button[data-sign-button="nama_terang"]'
};

let currentSignatureKey = null;
const modalCanvas = document.getElementById('modal-canvas');
const modalWrap = document.getElementById('wrap-modal');
const modalCtx = modalCanvas.getContext('2d');
let drawing = false, lx = 0, ly = 0;

function pos(e) {
    const r = modalCanvas.getBoundingClientRect();
    const sx = modalCanvas.width / r.width;
    const sy = modalCanvas.height / r.height;
    const src = e.touches ? e.touches[0] : e;
    return { x: (src.clientX - r.left) * sx, y: (src.clientY - r.top) * sy };
}

function startSignature(e) { e.preventDefault(); drawing = true; const p = pos(e); lx = p.x; ly = p.y; }
function moveSignature(e) {
    e.preventDefault(); if (!drawing) return;
    const p = pos(e);
    modalCtx.beginPath();
    modalCtx.moveTo(lx, ly);
    modalCtx.lineTo(p.x, p.y);
    modalCtx.strokeStyle = '#1e293b';
    modalCtx.lineWidth = 2.2;
    modalCtx.lineCap = 'round';
    modalCtx.lineJoin = 'round';
    modalCtx.stroke();
    lx = p.x; ly = p.y;
    modalWrap.classList.add('has-sig');
}
function endSignature(e) { e.preventDefault(); drawing = false; }

function initSignaturePad() {
    if (!modalCanvas) return;
    modalCanvas.addEventListener('mousedown', startSignature);
    modalCanvas.addEventListener('mousemove', moveSignature);
    modalCanvas.addEventListener('mouseup', endSignature);
    modalCanvas.addEventListener('mouseleave', endSignature);
    modalCanvas.addEventListener('touchstart', startSignature, { passive: false });
    modalCanvas.addEventListener('touchmove', moveSignature, { passive: false });
    modalCanvas.addEventListener('touchend', endSignature, { passive: false });
}

function getSignatureButton(key) {
    const selector = signatureButtonMap[key];
    return selector ? document.querySelector(selector) : null;
}

function setSignatureButtonLabel(key, hasSignature) {
    const button = getSignatureButton(key);
    if (!button) return;
    button.textContent = hasSignature ? '✍️ Ubah Tanda Tangan' : '✍️ Isi Tanda Tangan';
}

function initSignatureButtonLabels() {
    Object.keys(signatureButtonMap).forEach(function(key) {
        const value = document.querySelector(signatureHiddenMap[key])?.value || '';
        setSignatureButtonLabel(key, !!value);
    });
}

function openSignatureModal(key) {
    currentSignatureKey = key;
    const title = signatureTitleMap[key] || 'Tanda Tangan';
    document.getElementById('modalTitle').textContent = title;

    // Reset tanda tangan lama jika user ingin mengubah
    if (document.querySelector(signatureHiddenMap[key])?.value) {
        clearTTD(key);
    }

    // Tampilkan modal DULU
    document.getElementById('signatureModal').classList.add('show');

    // Setelah modal visible, baru resize canvas sesuai ukuran wrap aktual
    requestAnimationFrame(function() {
        const wrap = document.getElementById('wrap-modal');
        const rect = wrap.getBoundingClientRect();
        
        // Set canvas sesuai CSS size, tanpa DPR scaling
        const w = Math.floor(rect.width)  || 800;
        const h = Math.floor(rect.height) || 240;
        
        // Assign width/height = otomatis clear canvas + reset transform
        modalCanvas.width  = w;
        modalCanvas.height = h;

        // Tidak perlu ctx.scale sama sekali
        loadSignatureIntoModal(key);
    });
}

function loadSignatureIntoModal(key) {
    const value = document.querySelector(signatureHiddenMap[key])?.value || '';
    if (!value) {
        clearModalCanvas();
        return;
    }
    const img = new Image();
    img.onload = function() {
        modalCtx.clearRect(0, 0, modalCanvas.width, modalCanvas.height);
        // Gambar sesuai ukuran canvas penuh, tanpa dibagi DPR
        modalCtx.drawImage(img, 0, 0, modalCanvas.width, modalCanvas.height);
        modalWrap.classList.add('has-sig');
    };
    img.src = value;
}

function clearModalCanvas() {
    modalCtx.clearRect(0, 0, modalCanvas.width, modalCanvas.height);
    modalWrap.classList.remove('has-sig');
}

function closeSignatureModal() {
    document.getElementById('signatureModal').classList.remove('show');
    currentSignatureKey = null;
}

function isCanvasBlank() {
    const data = modalCtx.getImageData(0, 0, modalCanvas.width, modalCanvas.height).data;
    for (let i = 0; i < data.length; i += 4) {
        if (data[i + 3] !== 0 && (data[i] !== 255 || data[i + 1] !== 255 || data[i + 2] !== 255)) {
            return false;
        }
    }
    return true;
}

function updateSignaturePreview(key, dataUrl) {
    const preview = document.getElementById(signaturePreviewMap[key]);
    const box = document.querySelector(`.ttd-box[data-sign-key="${key}"]`);
    if (!preview) return;
    preview.innerHTML = '';
    preview.classList.remove('empty');

    const img = document.createElement('img');
    img.src = dataUrl;
    img.alt = 'Preview tanda tangan ' + (signatureTitleMap[key] || key);
    img.className = 'ttd-preview-img';
    preview.appendChild(img);

    if (box) box.classList.add('has-sig');
    setSignatureButtonLabel(key, true);
}

function setTTDError(key, hasError) {
    const box = document.querySelector(`.ttd-box[data-sign-key="${key}"]`);
    if (!box) return;
    box.classList.toggle('invalid', !!hasError);
}

function validateRequiredTTD() {
    const requiredKeys = ['nama_terang'];
    let firstInvalidKey = null;

    requiredKeys.forEach(function(key) {
        const value = document.querySelector(signatureHiddenMap[key])?.value || '';
        const hasSignature = !!value;
        setTTDError(key, !hasSignature);
        if (!hasSignature && !firstInvalidKey) firstInvalidKey = key;
    });

    if (firstInvalidKey) {
        alertErr('Tanda tangan wajib diisi.');
        const target = document.querySelector(`.ttd-box[data-sign-key="${firstInvalidKey}"]`);
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }

    return true;
}

function clearTTD(key) {
    const hidden = document.querySelector(signatureHiddenMap[key]);
    if (hidden) hidden.value = '';
    const preview = document.getElementById(signaturePreviewMap[key]);
    if (preview) {
        preview.innerHTML = '<span>TTD belum diisi</span>';
        preview.classList.add('empty');
    }
    const box = document.querySelector(`.ttd-box[data-sign-key="${key}"]`);
    if (box) box.classList.remove('has-sig');
    setTTDError(key, false);
    setSignatureButtonLabel(key, false);
    if (currentSignatureKey === key) {
        clearModalCanvas();
    }
}

function getBoundingBox(canvas) {
    const ctx = canvas.getContext('2d');
    const data = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
    let minX = canvas.width, maxX = 0, minY = canvas.height, maxY = 0;
    for (let y = 0; y < canvas.height; y++) {
        for (let x = 0; x < canvas.width; x++) {
            const alpha = data[(y * canvas.width + x) * 4 + 3];
            if (alpha > 10) {
                if (x < minX) minX = x;
                if (x > maxX) maxX = x;
                if (y < minY) minY = y;
                if (y > maxY) maxY = y;
            }
        }
    }
    if (minX > maxX) return null;
    return { x: minX, y: minY, w: maxX - minX, h: maxY - minY };
}

function saveModalSignature() {
    if (!currentSignatureKey) return;
    if (isCanvasBlank()) {
        alertErr('Silakan isi tanda tangan terlebih dahulu');
        return;
    }

    // Crop ke bounding box tanda tangan + padding 10px
    const pad = 10;
    const bb = getBoundingBox(modalCanvas);
    let dataUrl;

    if (bb) {
        const cropX = Math.max(0, bb.x - pad);
        const cropY = Math.max(0, bb.y - pad);
        const cropW = Math.min(modalCanvas.width - cropX, bb.w + pad * 2);
        const cropH = Math.min(modalCanvas.height - cropY, bb.h + pad * 2);

        const tmpCanvas = document.createElement('canvas');
        tmpCanvas.width  = cropW;
        tmpCanvas.height = cropH;
        const tmpCtx = tmpCanvas.getContext('2d');
        tmpCtx.drawImage(modalCanvas, cropX, cropY, cropW, cropH, 0, 0, cropW, cropH);
        dataUrl = tmpCanvas.toDataURL('image/png');
    } else {
        dataUrl = modalCanvas.toDataURL('image/png');
    }

    const hidden = document.querySelector(signatureHiddenMap[currentSignatureKey]);
    if (hidden) hidden.value = dataUrl;
    updateSignaturePreview(currentSignatureKey, dataUrl);
    closeSignatureModal();
}

initSignaturePad();
initSignatureButtonLabels();

$('#btnModalClear').on('click', function() {
    clearModalCanvas();
});

$('#btnModalSave').on('click', function() {
    saveModalSignature();
});

/* ═══════════════════════════════════════════
   UPPERCASE INPUT
═══════════════════════════════════════════ */
document.querySelectorAll('input[type="text"]').forEach(function(el) {
    el.addEventListener('input', function() {
        const s = this.selectionStart, e = this.selectionEnd;
        this.value = (this.value || '').toUpperCase();
        try { this.setSelectionRange(s, e); } catch(_) {}
    });
});

/* ═══════════════════════════════════════════
   PAKET QTY UX
═══════════════════════════════════════════ */
document.querySelectorAll('.paket-qty').forEach(function(el) {
    el.addEventListener('focus', function() { if (this.value === '0') this.value = ''; });
    el.addEventListener('blur',  function() { if (this.value === '')  this.value = '0'; updateTotalSummary(); });
    el.addEventListener('input', updateTotalSummary);
});

/* ═══════════════════════════════════════════
   HELPERS
═══════════════════════════════════════════ */
function alertErr(msg) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Perhatian',
            text: msg,
            confirmButtonText: 'OK',
            customClass: { container: 'swal-on-top' }
        });
    } else { alert(msg); }
}

function closeToast() { $('#toast').removeClass('show'); }

function setLoadedInputs(ids, active) {
    ids.forEach(function(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.toggle('input-success', !!active);
    });
}

function markTokoLoaded(active) {
    setLoadedInputs(['kode_toko_input','lokasi_event','pic','no_hp','kota'], active);
}

function markAgenLoaded(active) {
    setLoadedInputs(['kode_agen_input','nama_agen','brand','kode_agen_manual_input'], active);
}

function formatNumber(value) {
    return new Intl.NumberFormat('id-ID').format(value);
}

function updateTotalSummary() {
    let totalPoint = 0;
    let totalKupon = 0;
    document.querySelectorAll('.paket-qty').forEach(function(el) {
        const qty = parseInt(el.value, 10) || 0;
        const point = parseFloat(el.dataset.point || 0) || 0;
        const kupon = parseFloat(el.dataset.kupon || 0) || 0;
        totalPoint += qty * point;
        totalKupon += qty * kupon;
    });
    document.getElementById('total_points_display').textContent = formatNumber(totalPoint);
    document.getElementById('total_kupon_display').textContent = formatNumber(totalKupon);
}

function resetForm() {
    document.getElementById('scanForm').reset();
    scanLock = false; lastCode = '';
    ['kode_toko_input','pic','no_hp','kota','nama_agen','brand','nama_sales',
     'kode_agen_manual_input','kode_agen_input','nama_terang_input'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    ['nama_toko_hidden','kode_toko_hidden','pic_hidden','no_hp_hidden','kota_hidden',
     'lokasi_event_hidden','nama_agen_id_hidden','nama_agen_id_hidden_alt'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.querySelectorAll('.paket-qty').forEach(el => el.value = '0');
    clearTTD('nama_terang');
    markTokoLoaded(false);
    markAgenLoaded(false);
    updateTotalSummary();
}

function resetTokoData() {
    scanLock = false; lastCode = '';
    // Reset display fields toko saja, tidak termasuk agen/paket
    ['kode_toko_input','pic','no_hp','kota'].forEach(id => document.getElementById(id).value = '');
    // Reset hidden fields toko saja
    ['nama_toko_hidden','kode_toko_hidden','pic_hidden','no_hp_hidden','kota_hidden','pic_old_hidden','nomor_pic_old_hidden'].forEach(id => document.getElementById(id).value = '');
    // Reset lokasi_event ke default
    const defaultLokasi = document.getElementById('lokasi_event').getAttribute('data-default') || '';
    document.getElementById('lokasi_event').value = defaultLokasi;
    // Remove highlight toko
    markTokoLoaded(false);
    // Clear tanda tangan
    clearTTD('nama_terang');
    // Tampilkan scanner box lagi
    document.querySelector('.scanner-box').classList.remove('hidden');
    // Reset scanner status
    setStatus('Kamera siap. Arahkan QR code ke frame.');
}

function resetTokoDataOnly() {
    scanLock = false; lastCode = '';
    // Reset display fields toko saja, tidak termasuk agen/paket
    ['kode_toko_input','pic','no_hp','kota'].forEach(id => document.getElementById(id).value = '');
    // Reset hidden fields toko saja
    ['nama_toko_hidden','kode_toko_hidden','pic_hidden','no_hp_hidden','kota_hidden','pic_old_hidden','nomor_pic_old_hidden'].forEach(id => document.getElementById(id).value = '');
    // Reset lokasi_event ke default
    const defaultLokasi = document.getElementById('lokasi_event').getAttribute('data-default') || '';
    document.getElementById('lokasi_event').value = defaultLokasi;
    // Remove highlight toko
    markTokoLoaded(false);
    // Clear tanda tangan
    clearTTD('nama_terang');
    // Jangan ubah status scanner
}

/* ═══════════════════════════════════════════
   INIT
═══════════════════════════════════════════ */
window.addEventListener('load', startScanner);
window.addEventListener('beforeunload', function() { stopScanner(); });

// Variabel global untuk menyimpan status edit
let isEditingOrder = false;
let currentOrderId = null;

// Modified doLookupAgen function
function doLookupAgen(kode) {
    kode = (kode || '').trim();
    if (!kode) return Promise.reject('Kode agen kosong');

    isEditingOrder = false;
    currentOrderId = null;
    if ($('#order_id').length) {
        $('#order_id').remove();
    }
    // Reset submit button ke state awal
    $('#formSubmitButton').html('✔ Simpan Order');
    $('#formSubmitButton').removeClass('btn-warning').addClass('btn-success');
    
    return $.get('{{ url('/api/lookup-agen-by-kode') }}', { kode_agen: kode })
        .done(function(res) {
            if (res.success) {
                $('#kode_agen_input').val(res.data.kode_agen || kode);
                $('#nama_agen').val(res.data.nama_agen || '');
                $('#brand').val((res.data.brands || []).join(', '));
                $('#nama_agen_id_hidden').val(res.data.id || '');
                $('#nama_agen_id_hidden_alt').val(res.data.id || '');
                markAgenLoaded(true);
                
                // AFTER loading agen, check for existing order
                checkExistingOrder();
            } else {
                $('#kode_agen_input, #nama_agen, #brand').val('');
                $('#nama_agen_id_hidden, #nama_agen_id_hidden_alt').val('');
                markAgenLoaded(false);
                alertErr(res.message || 'Agen tidak ditemukan');
                resetOrderForm();
                isEditingOrder = false;
                currentOrderId = null;
            }
        })
        .fail(function() { 
            markAgenLoaded(false);
            alertErr('Gagal melakukan lookup agen');
            resetOrderForm();
        });
}

// New function to check existing order
function checkExistingOrder() {
    const kodeAgen = $('#kode_agen_input').val().trim();
    const namaToko = $('#nama_toko_hidden').val().trim();
    const lokasiEvent = $('#lokasi_event').val().trim();
    const kota = $('#kota').val().trim();
    const picOld = $('#pic_old_hidden').val().trim();
    const nomorPicOld = $('#nomor_pic_old_hidden').val().trim();
    
    if (!kodeAgen || !namaToko || !lokasiEvent || !kota) {
        return;
    }
    
    // Show loading indicator
    $('#btnLookupAgen').prop('disabled', true);
    $('#btnLookupAgen').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...');
    
    $.get('{{ url('/api/check-existing-order') }}', {
        kode_agen: kodeAgen,
        nama_toko: namaToko,
        lokasi_event: lokasiEvent,
        kota: kota,
        pic_old: picOld,
        nomor_pic_old: nomorPicOld
    })
    .done(function(res) {
        $('#btnLookupAgen').prop('disabled', false);
        $('#btnLookupAgen').html('🔍 Cari');
        
        if (res.success && res.exists) {
            // Existing order found - load data
            isEditingOrder = true;
            currentOrderId = res.data.id;
            
            // Add order_id to form
            if ($('#order_id').length === 0) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'order_id',
                    name: 'order_id'
                }).appendTo('#scanForm');
            }
            $('#order_id').val(currentOrderId);
            
            // CRITICAL: Set the pic_old and nomor_pic_old from existing order data
            // This ensures the update can find the existing toko records
            if (res.data.pic_old) {
                $('#pic_old_hidden').val(res.data.pic_old);
                $('#nomor_pic_old_hidden').val(res.data.nomor_pic_old);
                // Also update the display fields if needed
                $('#pic').val(res.data.pic);
                $('#no_hp').val(res.data.no_hp);
            }
            
            // Update kode_toko if needed
            if (res.data.kode_toko) {
                $('#kode_toko_hidden').val(res.data.kode_toko);
                $('#kode_toko_input').val(res.data.kode_toko);
            }
            
            // Load existing data
            $('#brand').val(res.data.brand || '');
            $('#nama_sales').val(res.data.nama_sales || '');
            $('#nama_terang_input').val(res.data.nama_terang || '');
            
            // Load paket quantities
            $('.paket-qty').each(function() {
                const $this = $(this);
                const masterTargetId = $this.closest('.paket-row').find('input[name$="[master_target_id]"]').val();
                
                if (res.data.details && res.data.details[masterTargetId] !== undefined && res.data.details[masterTargetId] > 0) {
                    $this.val(res.data.details[masterTargetId]);
                } else {
                    $this.val(0);
                }
            });
            updateTotalSummary();
            
            // Clear all signatures (as requested)
            clearTTD('nama_terang');
            
            // Reset visual state
            $('.ttd-box[data-sign-key]').removeClass('has-sig');
            
            // Format numbers for display
            const formattedTotalPoint = new Intl.NumberFormat('id-ID').format(res.data.total_point || 0);
            const formattedTotalKupon = new Intl.NumberFormat('id-ID').format(res.data.total_kupon || 0);
            
            // Show notification
            Swal.fire({
                icon: 'info',
                title: '📋 Data Order Ditemukan',
                html: `
                    <div style="text-align: left; margin-top: 10px;">
                        <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 10px;">
                            <strong>📊 Informasi Order:</strong><br>
                            <span style="color: #ef4444;">◆</span> <strong>Total Point:</strong> ${formattedTotalPoint}<br>
                            <span style="color: #3b82f6;">◆</span> <strong>Brand:</strong> ${res.data.brand || '-'}<br>
                            <span style="color: #8b5cf6;">◆</span> <strong>Sales:</strong> ${res.data.nama_sales || '-'}
                        </div>
                        <div style="background: #fff3cd; padding: 12px; border-radius: 8px; border-left: 4px solid #ffc107;">
                            <strong>⚠️ Perhatian:</strong><br>
                            Tanda tangan akan direset dan harus diisi ulang untuk validasi.
                        </div>
                    </div>
                `,
                confirmButtonText: '✏️ Edit Order',
                confirmButtonColor: '#ef4444',
                showCancelButton: true,
                cancelButtonText: '❌ Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('html, body').animate({
                        scrollTop: $('#scanForm').offset().top - 100
                    }, 500);
                }
            });
            
            // Update submit button
            $('#formSubmitButton').html('✏️ Update Order');
            $('#formSubmitButton').addClass('btn-warning').removeClass('btn-success');
            
        } else if (res.success && !res.exists) {
            // No existing order - normal flow
            isEditingOrder = false;
            currentOrderId = null;
            if ($('#order_id').length) {
                $('#order_id').remove();
            }
            
            // Reset paket quantities to 0
            $('.paket-qty').val(0);
            updateTotalSummary();
            
            // Clear signatures
            clearTTD('nama_terang');
            $('.ttd-box[data-sign-key]').removeClass('has-sig');
            
            // Reset submit button - PASTIKAN INI ADA
            $('#formSubmitButton').html('✔ Simpan Order');
            $('#formSubmitButton').removeClass('btn-warning').addClass('btn-success');
            
            // Swal.fire({
            //     icon: 'success',
            //     title: '✨ Data Baru',
            //     text: res.message || 'Data order tidak ditemukan. Silakan buat order baru.',
            //     timer: 2500,
            //     showConfirmButton: false
            // });
        }
    })
    .fail(function(err) {
        $('#btnLookupAgen').prop('disabled', false);
        $('#btnLookupAgen').html('🔍 Cari');
        console.error('Error checking existing order:', err);
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal mengecek data order. Silakan coba lagi.',
            confirmButtonText: 'OK'
        });
    });
}

// Update doLookupToko to pass additional data for checking
function doLookupToko(kode) {
    return $.get('{{ url('/api/lookup-toko-by-kode') }}', { kode: kode })
        .done(function(res) {
            if (res.success) {
                const d = res.data;
                $('#pic').val(d.pic || '');
                $('#no_hp').val(d.no_hp || '');
                $('#kota').val(d.kota || '');
                $('#lokasi_event').val(d.lokasi_event || $('#lokasi_event').val());
                $('#nama_sales').val(d.nama_sales || '');
                markTokoLoaded(true);
                // hidden
                $('#nama_toko_hidden').val(d.nama_toko || ''); // Store nama_toko for checking
                $('#kode_toko_hidden').val(d.kode_toko || '');
                $('#pic_hidden').val(d.pic || '');
                $('#no_hp_hidden').val(d.no_hp || '');
                $('#kota_hidden').val(d.kota || '');
                $('#lokasi_event_hidden').val(d.lokasi_event || $('#lokasi_event').val());
                $('#pic_old_hidden').val(d.pic || '');
                $('#nomor_pic_old_hidden').val(d.no_hp || '');
                setStatus('✔ Toko: ' + (d.nama_toko || ''), 'ok');
                // KUNCI SCANNER setelah berhasil load toko
                stopScanner();
                
                // AFTER loading toko, check if agen is already loaded
                if ($('#kode_agen_input').val().trim()) {
                    setTimeout(function() {
                        checkExistingOrder();
                    }, 500); // Small delay to ensure all data is set
                }
                updateTotalSummary();
            } else {
                setStatus('Toko tidak ditemukan.', 'err');
                markTokoLoaded(false);
                alertErr(res.message || 'Toko tidak ditemukan');
                resetOrderForm();
            }
        })
        .fail(function() {
            setStatus('Lookup gagal.', 'err');
            markTokoLoaded(false);
            alertErr('Gagal melakukan lookup toko');
            resetOrderForm();
        });
}

// Add CSS for btn-warning class if not exists
const style = document.createElement('style');
style.textContent = `
    .btn-warning {
        background: #f59e0b;
        color: #fff;
    }
    .btn-warning:hover {
        background: #d97706;
    }
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.2em;
    }
`;
document.head.appendChild(style);

// Reset order form function
function resetOrderForm() {
    isEditingOrder = false;
    currentOrderId = null;
    if ($('#order_id').length) {
        $('#order_id').remove();
    }
    $('.paket-qty').val(0);
    clearTTD('nama_terang');
    $('#formSubmitButton').html('✔ Simpan Order');
}

// Update submit handler to show appropriate message
$('#scanForm').on('submit', function(e) {
    e.preventDefault();
    
    // Pastikan source terkirim
    if ($('#source').length === 0) {
        $('<input>').attr({
            type: 'hidden',
            name: 'source',
            value: 'quick-scan'
        }).appendTo('#scanForm');
    }

    // Uppercase semua text
    $(this).find('input[type="text"], textarea').each(function() {
        if (this.value) this.value = this.value.toUpperCase();
    });

    // Validasi frontend-only untuk tanda tangan wajib
    if (!validateRequiredTTD()) return;

    // Validasi
    if (!$('#nama_toko_hidden').val())        { alertErr('Scan Toko terlebih dahulu'); return; }
    if (!$('#kode_agen_input').val().trim())  { alertErr('Pilih Kode Agen via tombol Cari'); return; }
    if (!$('#nama_agen_id_hidden_alt').val()) { alertErr('Lookup Agen terlebih dahulu'); return; }
    if (!$('#brand').val())                   { alertErr('Brand harus ada'); return; }

    const fd = new FormData(this);
    const submitAction = isEditingOrder ? 'mengupdate' : 'menyimpan';
    
    // Show loading indicator
    Swal.fire({
        title: 'Memproses...',
        text: `Sedang ${submitAction} data order`,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            Swal.close();
            if (res.success) {
                const successMsg = res.is_update ? 'Order berhasil diupdate!' : 'Order berhasil disimpan!';
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: successMsg + ' ' + (res.message || ''),
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = res.redirect_url;
                });
            } else {
                alertErr(res.message || 'Submission gagal');
            }
        },
        error: function(xhr) {
            Swal.close();
            let msg = 'Terjadi kesalahan saat submit.';
            if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
            else if (xhr.status === 422 && xhr.responseJSON?.errors)
                msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
            alertErr(msg);
        }
    });
});

// Add event listener for lokasi_event change (if it's editable)
$('#lokasi_event').on('change', function() {
    if ($('#kode_agen_input').val().trim() && $('#kode_toko_hidden').val().trim()) {
        checkExistingOrder();
    }
});
</script>
</body>
</html>