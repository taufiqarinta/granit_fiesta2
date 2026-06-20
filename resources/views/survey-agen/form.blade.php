<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Survey</title>
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
            --danger:    #ef4444;
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

        .page { padding: 1.5rem 1rem 3rem; }

        .card {
            background: var(--surface);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            padding: 2rem;
            max-width: 900px;
            margin: 0 auto;
        }

        @media (max-width: 600px) {
            .page { padding: .75rem .5rem 2rem; }
            .card { padding: 1rem; border-radius: 14px; }
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--text);
        }

        .section-label {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 1.25rem;
        }

        .field { margin-bottom: 1rem; }

        .label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: .35rem;
            letter-spacing: .03em;
        }

        .input {
            width: 100%;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: .6rem .85rem;
            font-size: 16px;
            font-family: 'Sora', sans-serif;
            color: var(--text);
            background: #fff;
            transition: border-color .18s, box-shadow .18s;
            outline: none;
        }

        /* Visual uppercase so typed text appears capitalized immediately */
        input[type="text"].input,
        textarea.input {
            text-transform: uppercase;
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

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

        @media (max-width: 620px) { .grid-2 { grid-template-columns: 1fr; } }

        /* Sales Card */
        .sales-card {
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            position: relative;
        }

        .summary-box {
            background: var(--accent-lt);
            border: 1.5px solid rgba(67,97,238,.15);
            border-radius: 12px;
            padding: 1rem 1.1rem;
            margin-bottom: 1.25rem;
        }

        .summary-label {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: .4rem;
        }

        .summary-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text);
        }

        .sales-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: .75rem;
            border-bottom: 1.5px solid var(--border);
        }

        .sales-card-title {
            font-size: .9rem;
            font-weight: 700;
            color: var(--text);
        }

        .btn-remove {
            background: #fff;
            border: 1.5px solid #fca5a5;
            color: var(--danger);
            border-radius: 6px;
            padding: .3rem .6rem;
            font-size: .75rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }

        .btn-remove:hover { background: #fef2f2; }

        /* Buttons */
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

        .btn-secondary {
            background: #e5e7eb;
            color: #4b5563;
        }

        .btn-secondary:hover { background: #d1d5db; }

        .btn-full { width: 100%; }

        .form-footer {
            display: flex;
            gap: .75rem;
            padding-top: 1.25rem;
            margin-top: 1.5rem;
            border-top: 1.5px solid var(--border);
            flex-wrap: wrap;
        }

        @media (max-width: 480px) {
            .form-footer { flex-direction: column; }
            .form-footer .btn { width: 100%; }
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: .9rem;
        }

        .alert-error {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            color: #991b1b;
        }

        .sales-container { margin-bottom: 1.5rem; }

        .add-sales-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
        }

        .submit-button {
            min-width: 160px;
        }

        .spinner-holder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 0;
            overflow: hidden;
            transition: width .15s ease;
        }

        .spinner-holder.show {
            width: 18px;
            margin-right: .45rem;
        }

        .divider {
            border: none;
            border-top: 1.5px solid var(--border);
            margin: 1.5rem 0;
        }

        .spinner {
            display: none;
            text-align: center;
            padding: 0;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,.45);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .9s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="page">
    <div class="card">
        <h1 class="card-title">📋 Form Survey</h1>

        <form id="surveyForm" action="{{ route('form-survey.store') }}" method="POST">
            @csrf

            <p class="section-label">📝 Informasi Agen</p>

            <div class="field">
                <label class="label">Cek Kode Agen (kode agen dapat dilihat di undangan) <span style="color:var(--danger);">*</span></label>
                <div class="grid-2">
                    <input type="text" id="kodeAgenSearch" class="input" placeholder="Masukkan kode agen" autocomplete="off">
                    <button type="button" class="btn btn-primary" id="btnCariAgen">🔍 Cek Data Agen</button>
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label class="label">Kode Agen</label>
                    <input type="text" name="kode_agen" id="kodeAgen" class="input" readonly placeholder="Kode Agen">
                </div>

                <div class="field">
                    <label class="label">Nama Agen</label>
                    <input type="text" name="nama_agen" id="namaAgen" class="input" readonly placeholder="Nama Agen">
                </div>
            </div>

            <!-- Jumlah sales dihapus: form diisi per sales secara mandiri -->

            <hr class="divider">
            <p class="section-label">💼 Data Sales</p>

                <div class="sales-container">
                    <div class="sales-card">
                        <div class="sales-card-header">
                            <span class="sales-card-title">Data Sales</span>
                        </div>

                        <div class="field">
                            <label class="label">Nama Sales <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="nama_sales" class="input uppercase-field" placeholder="Nama sales" required autocomplete="off">
                        </div>

                        <div class="field">
                            <label class="label">No HP <span style="color:var(--danger);">*</span></label>
                            <input type="number" name="no_hp" class="input" placeholder="08xxxxxxxxxx" required>
                        </div>

                        <div class="field">
                            <label class="label">Area/Distribusi Wilayah <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="area" class="input uppercase-field" placeholder="Contoh : Timur (Surabaya)" required>
                        </div>

                        <div class="field">
                            <label class="label">Top 10 Toko Pareto yang Di-handle <span style="color:var(--danger);">*</span></label>
                            <textarea name="top_10_pareto" class="input" rows="3" placeholder="Sebutkan top 10 pareto toko yang di-handle" required></textarea>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label class="label">Target Penjualan Perbulan (Box) <span style="color:var(--danger);">*</span></label>
                                <input type="number" name="target_penjualan" class="input" placeholder="Contoh : 10000" min="0" required>
                            </div>
                            <div class="field">
                                <label class="label">Brand yang dipegang (bisa sebutkan yang di luar Kobin) <span style="color:var(--danger);">*</span></label>
                                <input type="text" name="brands" class="input" placeholder="Contoh : Brand X, Brand Y" required>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">Keliling luar kota kemana aja? Estimasi kunjungannya <span style="color:var(--danger);">*</span></label>
                            <textarea name="keliling_luar_kota" class="input" rows="2" placeholder="Contoh : Surabaya (10x/bln), Malang (20x/bln)" required></textarea>
                        </div>

                        <div class="field">
                            <label class="label">Toko mana saja yg perlu support marketing <span style="color:var(--danger);">*</span></label>
                            <textarea name="toko_butuh_support" class="input" rows="2" placeholder="Sebutkan toko yg perlu support marketing" required></textarea>
                        </div>

                        <div class="field">
                            <label class="label">Saran untuk Kobin Tiles <span style="color:var(--danger);">*</span></label>
                            <textarea name="saran_kobin" class="input" rows="3" placeholder="Masukkan saran untuk Kobin Tiles" required></textarea>
                        </div>
                    </div>
                </div>

            <!-- <hr class="divider"> -->
            <div class="form-footer">
                <a href="/" class="btn btn-secondary">✕ Batal</a>
                <button type="submit" class="btn btn-success add-sales-btn submit-button">
                    <span id="submitSpinnerHolder" class="spinner-holder"><span id="submitSpinner" class="spinner"></span></span>
                    ✔ Kirim Survey
                </button>
            </div>
        </form>

        <div id="errorAlert" class="alert alert-error" style="display:none;"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const form = document.getElementById('surveyForm');

// Uppercase helper: transform ke uppercase saat mengetik untuk semua input teks dan textarea (kecuali kotak pencarian)
document.addEventListener('input', function(e) {
    const el = e.target;
    if (!el) return;
    const tag = (el.tagName || '').toLowerCase();
    const type = (el.getAttribute && el.getAttribute('type')) || '';

    // only process text inputs and textareas; skip the kodeAgenSearch field
    if ((tag === 'input' && type === 'text') || tag === 'textarea') {
        if (el.id === 'kodeAgenSearch') return;
        const start = el.selectionStart;
        const end = el.selectionEnd;
        el.value = (el.value || '').toUpperCase();
        try { el.setSelectionRange(start, end); } catch (_) {}
    }
});

// Form submit
form.addEventListener('submit', function(e) {
    e.preventDefault();

    // Uppercase all text inputs and textareas (including search box)
    form.querySelectorAll('input[type="text"], textarea').forEach(el => {
        el.value = (el.value || '').toUpperCase();
    });

    if (!document.getElementById('kodeAgen').value.trim() || !document.getElementById('namaAgen').value.trim()) {
        showError('Silakan cari dan pilih kode agen terlebih dahulu');
        return;
    }

    const formData = new FormData(this);

    // Show spinner
    document.getElementById('submitSpinnerHolder').classList.add('show');
    document.getElementById('submitSpinner').style.display = 'inline-block';

    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('submitSpinnerHolder').classList.remove('show');
        document.getElementById('submitSpinner').style.display = 'none';

        if (data.success) {
            // Redirect ke halaman success
            window.location.href = `/form-survey/${data.kode_survey}`;
        } else {
            showError(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(err => {
        document.getElementById('submitSpinnerHolder').classList.remove('show');
        document.getElementById('submitSpinner').style.display = 'none';
        showError('Gagal mengirim data');
        console.error(err);
    });
});

document.getElementById('btnCariAgen').addEventListener('click', function() {
    const kode = document.getElementById('kodeAgenSearch').value.trim();

    if (!kode) {
        showError('Masukkan kode agen terlebih dahulu');
        return;
    }

    fetch(`{{ route('form-survey.lookup-agen') }}?kode_agen=${encodeURIComponent(kode)}`)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                document.getElementById('kodeAgen').value = data.data.kode_agen || '';
                document.getElementById('namaAgen').value = data.data.nama_agen || '';
                document.getElementById('errorAlert').style.display = 'none';
                document.getElementById('errorAlert').textContent = '';
                
                // Apply success styling
                document.getElementById('kodeAgenSearch').classList.add('input-success');
                document.getElementById('kodeAgen').classList.add('input-success');
                document.getElementById('namaAgen').classList.add('input-success');
            } else {
                document.getElementById('kodeAgen').value = '';
                document.getElementById('namaAgen').value = '';
                document.getElementById('kodeAgenSearch').classList.remove('input-success');
                document.getElementById('kodeAgen').classList.remove('input-success');
                document.getElementById('namaAgen').classList.remove('input-success');
                Swal.fire({
                    icon: 'warning',
                    title: 'Agen Tidak Ditemukan',
                    text: data.message || 'Kode agen tidak ditemukan',
                });
            }
        })
        .catch(function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal mencari agen. Silakan coba lagi.',
            });
        });
});

document.getElementById('kodeAgenSearch').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('btnCariAgen').click();
    }
});

// Reset success styling saat input dikosongkan
document.getElementById('kodeAgenSearch').addEventListener('input', function() {
    if (this.value.trim() === '') {
        document.getElementById('kodeAgenSearch').classList.remove('input-success');
        document.getElementById('kodeAgen').classList.remove('input-success');
        document.getElementById('namaAgen').classList.remove('input-success');
        document.getElementById('kodeAgen').value = '';
        document.getElementById('namaAgen').value = '';
    }
});

function showError(msg) {
    const alert = document.getElementById('errorAlert');
    if (!msg) {
        alert.style.display = 'none';
        alert.textContent = '';
        return;
    }
    alert.textContent = msg;
    alert.style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
    setTimeout(() => { alert.style.display = 'none'; }, 5000);
}
</script>

</body>
</html>
