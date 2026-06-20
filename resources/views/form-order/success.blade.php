<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Berhasil</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Sora', sans-serif;
            color: #0f172a;
            background:
                radial-gradient(circle at top left, rgba(149, 0, 0, .18), transparent 32%),
                radial-gradient(circle at bottom right, rgba(37, 99, 235, .14), transparent 28%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
            min-height: 100vh;
        }
        .page {
            min-height: 100vh;
            padding: 28px 16px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            width: min(980px, 100%);
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, .9);
            border-radius: 28px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .12);
            overflow: hidden;
        }
        .hero {
            padding: 28px 28px 20px;
            background: linear-gradient(135deg, #950000 0%, #fa0000 100%);
            color: #fff;
        }
        .hero h1 {
            margin: 0;
            font-size: clamp(1.5rem, 3vw, 2.2rem);
            font-weight: 800;
            letter-spacing: .01em;
        }
        .hero p { margin: 10px 0 0; opacity: .92; line-height: 1.55; }
        .content { padding: 28px; }
        .grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }
        @media (max-width: 880px) {
            .content, .hero { padding-left: 18px; padding-right: 18px; }
        }
        .voucher-frame {
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            background: #fff;
            box-shadow: 0 16px 40px rgba(15, 23, 42, .08);
        }
        .voucher-frame img {
            display: block;
            width: 100%;
            height: auto;
        }
        .meta {
            display: grid;
            gap: 14px;
        }
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: .82rem;
            font-weight: 700;
        }
        .stat {
            padding: 18px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
        }
        .stat label {
            display: block;
            font-size: .78rem;
            color: #64748b;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .stat .value {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: .08em;
            word-break: break-word;
        }
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 22px;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 18px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 800;
            border: none;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            color: #fff;
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
        }
        .btn-success {
            background: linear-gradient(135deg, #15803d 0%, #22c55e 100%);
            color: #fff;
            box-shadow: 0 12px 28px rgba(34, 197, 94, .22);
        }
        .btn-outline {
            background: #fff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
        }
        @media (max-width: 880px) {
            .actions {
                justify-content: center;
            }
            .actions .btn {
                width: 100%;
                max-width: 320px;
            }
        }
        .note {
            margin-top: 16px;
            font-size: .9rem;
            line-height: 1.6;
            color: #475569;
        }
        .small {
            margin-top: 8px;
            font-size: .82rem;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <div class="hero">
                <h1>Form Order Berhasil Disimpan</h1>
                <p>Simpan kode voucher ini dengan baik. Voucher ini akan digunakan untuk pengundian doorprize.</p>
            </div>

            <div class="content">
                <div class="grid">
                    <div class="meta">
                        <div class="actions" style="margin-top: 0;">
                            <a href="{{ $downloadUrl }}" class="btn btn-primary" download>Download Voucher</a>
                        </div>

                        <div class="stat">
                            <label>Kode Voucher</label>
                            <div class="value">{{ $kodeUnik }}</div>
                        </div>

                        <div class="stat">
                            <label>Jumlah Voucher</label>
                            <div class="value" id="jumlahVoucherValue">{{ $jumlahVoucher }}</div>
                        </div>

                        <div>
                            <div class="chip">Voucher Preview</div>
                            <div class="voucher-frame" style="margin-top: 14px;">
                                <img id="voucherPreviewImage" src="{{ $previewUrl }}" alt="Voucher Preview">
                            </div>
                        </div>

                        <div class="note">
                            Setelah menyimpan atau mengunduh voucher, kamu bisa lanjut membuat form order baru.
                        </div>

                        <div class="small">
                            Jika belum menyimpan voucher, silakan screenshot atau download gambar voucher terlebih dahulu sebelum lanjut.
                        </div>

                        <div class="actions" style="margin-top: 0;">
                            <button type="button" id="btnFormBaru" class="btn btn-success">+ Form Order Baru</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const params = new URLSearchParams(window.location.search);
            const queryJumlah = params.get('jumlah_voucher') || params.get('jumlah') || '';
            const queryKode = params.get('kode_unik') || '';

            if (queryJumlah && document.getElementById('jumlahVoucherValue')) {
                document.getElementById('jumlahVoucherValue').textContent = queryJumlah;
            }

            if (queryJumlah || queryKode) {
                const effectiveJumlah = queryJumlah || '{{ $jumlahVoucher }}';
                const effectiveKode = queryKode || '{{ $kodeUnik }}';
                const previewUrl = '{{ route('download.voucher.image') }}' + '?kode_unik=' + encodeURIComponent(effectiveKode) + '&jumlah=' + encodeURIComponent(effectiveJumlah) + '&preview=1&ts=' + Date.now();
                const downloadUrl = '{{ route('download.voucher.image') }}' + '?kode_unik=' + encodeURIComponent(effectiveKode) + '&jumlah=' + encodeURIComponent(effectiveJumlah) + '&ts=' + Date.now();

                const previewImage = document.getElementById('voucherPreviewImage');
                if (previewImage) previewImage.src = previewUrl;

                const downloadButton = document.querySelector('a.btn.btn-primary[download]');
                if (downloadButton) downloadButton.href = downloadUrl;

                const autoDownloadFrame = document.createElement('iframe');
                autoDownloadFrame.style.display = 'none';
                autoDownloadFrame.src = downloadUrl;
                document.body.appendChild(autoDownloadFrame);
            } else {
                const autoDownloadFrame = document.createElement('iframe');
                autoDownloadFrame.style.display = 'none';
                autoDownloadFrame.src = '{{ $downloadUrl }}';
                document.body.appendChild(autoDownloadFrame);
            }
        });

        document.getElementById('btnFormBaru').addEventListener('click', function () {
            Swal.fire({
                title: 'Simpan Voucher Terlebih Dahulu',
                text: 'Apakah Anda sudah menyimpan kode voucher Anda dengan baik sebelum ingin menambahkan form order baru? Jika sudah, klik Lanjutkan. Jika belum, Anda bisa screenshot terlebih dahulu.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#ef4444'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route('form-order.scan') }}';
                }
            });
        });
    </script>
</body>
</html>