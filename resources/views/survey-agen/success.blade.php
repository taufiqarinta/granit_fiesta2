<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Hasil Form Survey</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #fff5f5;
            --surface:   #ffffff;
            --panel:     #fff7f7;
            --border:    #fde2e2;
            --accent:    #ef4444;
            --success:   #16a34a;
            --success-dk:#15803d;
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
            max-width: 700px;
            margin: 0 auto;
        }

        @media (max-width: 600px) {
            .page { padding: .75rem .5rem 2rem; }
            .card { padding: 1rem; border-radius: 14px; }
        }

        .success-banner {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dk) 100%);
            border-radius: 16px;
            padding: 2rem 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
            color: #fff;
        }

        .success-banner .icon {
            font-size: 3rem;
            margin-bottom: .75rem;
        }

        .success-banner .title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: .5rem;
        }

        .success-banner .subtitle {
            font-size: .95rem;
            opacity: .95;
        }

        .qr-section {
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .qr-label {
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .qr-code-img {
            max-width: 240px;
            margin: 0 auto;
            border-radius: 8px;
            border: 1.5px solid var(--border);
            padding: .5rem;
            background: #fff;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 580px) { .info-grid { grid-template-columns: 1fr; } }

        .info-card {
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
        }

        .info-label {
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: .5rem;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--accent);
            word-break: break-all;
            word-wrap: break-word;
        }

        .survey-details {
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .details-title {
            font-size: .9rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 1rem;
            padding-bottom: .75rem;
            border-bottom: 1.5px solid var(--border);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: .6rem 0;
            border-bottom: 1px solid var(--border);
            font-size: .9rem;
        }

        .detail-row:last-child { border-bottom: none; }

        .detail-label { color: var(--muted); font-weight: 500; }

        .detail-value { color: var(--text); font-weight: 600; }

        .form-footer {
            display: flex;
            gap: .75rem;
            padding-top: 1.25rem;
            border-top: 1.5px solid var(--border);
            flex-wrap: wrap;
        }

        @media (max-width: 480px) {
            .form-footer { flex-direction: column; }
            .form-footer button { width: 100%; }
        }

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
            text-decoration: none;
            min-height: 44px;
        }

        .btn:active { transform: scale(.96); }

        .btn-primary {
            background: var(--accent);
            color: #fff;
            flex: 1;
        }

        .btn-primary:hover { background: #dc2626; }

        .btn-secondary {
            background: #e5e7eb;
            color: #4b5563;
        }

        .btn-secondary:hover { background: #d1d5db; }

        .divider {
            border: none;
            border-top: 1.5px solid var(--border);
            margin: 1.5rem 0;
        }

        .sales-list {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .sales-item {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .85rem;
            font-size: .85rem;
        }

        .sales-name { font-weight: 700; color: var(--accent); }

        .sales-info {
            display: flex;
            justify-content: space-between;
            margin-top: .5rem;
            padding-top: .5rem;
            border-top: 1px solid var(--border);
            font-size: .8rem;
            color: var(--muted);
        }

        .auto-download-note {
            text-align: center;
            font-size: .82rem;
            color: var(--muted);
            margin-top: .75rem;
        }
    </style>
</head>
<body>

<div class="page">
    <div class="card">
        <!-- Success Banner -->
        <div class="success-banner">
            <div class="icon">✔</div>
            <div class="title">Data Form Survey Berhasil Disimpan!</div>
            <div class="subtitle">Terima kasih telah mengisi Form Survey</div>
        </div>

        <!-- QR Code Section -->
        <div class="qr-section">
            <p class="qr-label">📱 QR Code Survey</p>
            {!! $qrCodeBase64 ? '<img src="' . $qrCodeBase64 . '" alt="QR Code" class="qr-code-img">' : '<p style="color:var(--muted);">QR Code tidak tersedia</p>' !!}
        </div>

        <!-- Info Cards -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Nomor Survey</div>
                <div class="info-value">{{ $survey->kode_survey }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">Sales</div>
                    <div class="info-value">{{ optional($survey->details->first())->nama_sales ?? '-' }}</div>
            </div>
        </div>

        <!-- Survey Details -->
        <div class="survey-details">
            <div class="details-title">📋 Detail Survey</div>

            <div class="detail-row">
                <span class="detail-label">Nama Agen</span>
                <span class="detail-value">{{ $survey->nama_agen }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Kode Agen</span>
                <span class="detail-value">{{ $survey->kode_agen }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Tanggal Survey</span>
                <span class="detail-value">{{ $survey->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <!-- Sales Data -->
        <div class="survey-details">
            <div class="details-title">💼 Data Sales</div>

            <div class="sales-list">
                @foreach($survey->details as $detail)
                <div class="sales-item">
                    <div class="sales-name">{{ $detail->nama_sales }}</div>
                    <div class="sales-info">
                        <span>Area: {{ $detail->area ?? '-' }}</span>
                        <span>Brands: {{ $detail->brands ?? '-' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Footer -->
        <div class="form-footer">
            <a href="/form-survey" class="btn btn-secondary">← Buat Survey Baru</a>
        </div>

        <div class="auto-download-note">
            File gambar akan diunduh otomatis setelah halaman dibuka.
        </div>
    </div>
</div>

<script>
function downloadImage() {
    const kodeSurvey = @json($survey->kode_survey);
    const namaAgen = @json($survey->nama_agen);
    const namaSales = @json(optional($survey->details->first())->nama_sales);
    const createdAt = @json($survey->created_at->format('d/m/Y H:i'));
    const qrCodeBase64 = @json($qrCodeBase64);

    const canvas = document.createElement('canvas');
    canvas.width = 800;
    canvas.height = 1000;
    const ctx = canvas.getContext('2d');

    const qrImage = new Image();
    qrImage.onload = function() {
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        ctx.fillStyle = '#4361ee';
        ctx.fillRect(0, 0, canvas.width, 10);

        ctx.fillStyle = '#1e293b';
        ctx.font = '700 34px Arial';
        ctx.fillText('FORM SURVEY', 50, 70);

        ctx.font = '22px Arial';
        ctx.fillText('Agen: ' + namaAgen.slice(0, 60), 50, 120);

        const qrSize = 300;
        const qrX = Math.round((canvas.width - qrSize) / 2);
        ctx.drawImage(qrImage, qrX, 200, qrSize, qrSize);

        ctx.font = '700 28px Arial';
        ctx.fillStyle = '#1e293b';
        ctx.fillText('NOMOR SURVEY:', 50, 620);

        ctx.font = '700 34px Arial';
        ctx.fillStyle = '#4361ee';
        ctx.fillText(kodeSurvey, 50, 670);

        ctx.font = '22px Arial';
        ctx.fillStyle = '#64748b';
        ctx.fillText('Sales: ' + (namaSales || '-'), 50, 730);
        ctx.fillText('Tanggal: ' + createdAt, 50, 900);

        const link = document.createElement('a');
        link.download = `form-survey-${kodeSurvey}.jpg`;
        link.href = canvas.toDataURL('image/jpeg', 0.92);
        document.body.appendChild(link);
        link.click();
        link.remove();
    };

    qrImage.onerror = function() {
        console.error('Gagal memuat QR code untuk unduhan JPEG');
    };

    qrImage.src = qrCodeBase64;
}

window.addEventListener('load', function() {
    setTimeout(downloadImage, 500);
});
</script>

</body>
</html>
