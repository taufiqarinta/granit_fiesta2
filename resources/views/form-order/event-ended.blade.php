<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Event Telah Berakhir</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #fff5f5;
            --surface:   #ffffff;
            --border:    #fde2e2;
            --accent:    #ef4444;
            --accent-dk: #dc2626;
            --accent-lt: #fff1f1;
            --text:      #1e293b;
            --muted:     #6b7280;
        }

        body {
            background: var(--bg);
            font-family: 'Sora', sans-serif;
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
        }

        .card {
            background: var(--surface);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(239,68,68,.08);
            padding: 2.5rem 2rem;
            max-width: 440px;
            width: 100%;
            text-align: center;
        }

        .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--accent-dk);
            margin-bottom: .6rem;
        }

        .desc {
            font-size: .9rem;
            color: var(--muted);
            line-height: 1.6;
            margin-bottom: 1.25rem;
        }

        .info-box {
            background: var(--accent-lt);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: .9rem 1rem;
            font-size: .85rem;
            color: var(--text);
        }

        .info-box strong {
            display: block;
            color: var(--accent);
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: .3rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⏰</div>
        <p class="title">Event Telah Berakhir</p>
        <p class="desc">
            Mohon maaf, waktu input order untuk event ini sudah ditutup.
            Silakan hubungi panitia jika ada kendala.
        </p>
    </div>
</body>
</html>