<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            font-size: 10.5pt;
            line-height: 1.35;
        }

        /* ── PRINT ─────────────────────────────────────── */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            html, body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
            }

            .action-buttons { display: none !important; }

            .page {
                width: 210mm;
                height: 297mm;
                page-break-after: always;
                position: relative;
                background: white;
                padding: 0;
                margin: 0;
            }

            .page:last-child { page-break-after: auto; }

            .page::after {
                content: '';
                position: absolute;
                bottom: 0;
                right: 0;
                width: 350px;
                height: 350px;
                background-image: url('https://fos01.kobin.co.id/images/bg/footer-new2.png');
                background-size: contain;
                background-repeat: no-repeat;
                background-position: bottom right;
                opacity: 0.15;
                z-index: 1;
                display: block;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .page-content {
                position: relative;
                z-index: 10;
                padding: 10mm 12mm 12mm 12mm;
                height: 297mm;
                display: flex;
                flex-direction: column;
            }
        }

        /* ── SCREEN ─────────────────────────────────────── */
        @media screen {
            body {
                background-color: #f3f4f6;
                padding: 20px;
            }

            .page {
                max-width: 210mm;
                width: 210mm;
                min-height: 297mm;
                margin: 20px auto;
                background: white;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                border-radius: 8px;
                position: relative;
                overflow: hidden;
            }

            .page::after {
                content: '';
                position: absolute;
                bottom: 0;
                right: 0;
                width: 350px;
                height: 350px;
                background-image: url('https://fos01.kobin.co.id/images/bg/footer-new2.png');
                background-size: contain;
                background-repeat: no-repeat;
                background-position: bottom right;
                opacity: 0.15;
                z-index: 1;
                display: block;
            }

            .page-content {
                position: relative;
                z-index: 10;
                padding: 10mm 12mm 12mm 12mm;
                min-height: 297mm;
                display: flex;
                flex-direction: column;
            }

            .action-buttons {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 1000;
                display: flex;
                gap: 10px;
                background: white;
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }

            .btn {
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-weight: 500;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-block;
                font-size: 14px;
            }

            .btn-print { background-color: #28a745; color: white; }
            .btn-print:hover { background-color: #218838; }
            .btn-back  { background-color: #6c757d; color: white; }
            .btn-back:hover { background-color: #5a6268; }
        }

        /* ── KOMPONEN DOKUMEN (common) ──────────────────── */

        .content-body { flex: 1; }
        .signature-section { flex-shrink: 0; margin-top: 4mm; }

        .topbar {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .topbar-left, .topbar-right {
            display: table-cell;
            vertical-align: top;
        }

        .topbar-left  { width: 45%; }
        .topbar-right { width: 55%; text-align: right; }

        .brand-logo { height: 20mm; object-fit: contain; }

        .doc-title    { font-size: 14pt; font-weight: 700; letter-spacing: 0.5px; }
        .doc-subtitle { font-size: 12pt; font-weight: 700; margin-top: 2px; letter-spacing: 0.5px; }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .meta-table td  { padding: 3px 4px; vertical-align: top; font-size: 9.5pt; }
        .meta-label     { width: 24mm; white-space: nowrap; font-weight: 700; text-transform: uppercase; }
        .meta-sep       { width: 4px; }

        .intro { margin: 8px 0 6px; text-align: justify; font-size: 10pt; }
        .intro .bold { font-weight: 700; }

        .detail-table,
        .mekanisme-table,
        .terms-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table th, .detail-table td,
        .mekanisme-table th, .mekanisme-table td,
        .terms-table  th, .terms-table  td {
            border: 1px solid #111827;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .detail-table th, .mekanisme-table th, .terms-table th {
            background: #f3f4f6;
            text-align: center;
            font-size: 9pt;
            font-weight: 700;
        }

        .detail-table td { font-size: 9.5pt; }

        .center { text-align: center; }
        .right  { text-align: right; }
        .bold   { font-weight: 700; }

        .section-title  { margin: 8px 0 3px; font-size: 9.5pt; font-weight: 700; text-transform: uppercase; }

        .mekanisme-table { font-size: 8.5pt; margin-bottom: 4px; }
        .mekanisme-table th, .mekanisme-table td { padding: 3px 6px; }

        ol.terms         { margin: 6px 0 0 14px; font-size: 8pt; line-height: 1.35; }
        ol.terms li      { margin-bottom: 2px; }

        .date-line        { margin-top: 10mm; font-size: 9pt; }
        .date-line span   { display: inline-block; border-bottom: 1px solid #111827; min-width: 55mm; }

        .signature-labels     { width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6mm; }
        .signature-labels td  { padding: 0 6px; text-align: center; font-size: 9pt; }
        .signature-box        { height: 20mm; }
        .signature-box img    { max-width: 100%; max-height: 24mm; display: block; margin: 0 auto; object-fit: contain; }
        .signature-line       { border-top: 1px solid #111827; width: 70%; margin: 18mm auto 4px; }
        .signature-name       { font-size: 8pt; }
        .signature-role       { font-size: 8pt; font-weight: 700; }
    </style>
</head>
<body>

@php
    $logoPath = file_exists(public_path('images/kobin-logo-formorder.jpg'))
        ? asset('images/kobin-logo-formorder.jpg')
        : (file_exists(public_path('images/kobin-logo.png'))
            ? asset('images/kobin-logo.png')
            : asset('images/kobin.png'));

    $signatureSrc = function ($value) {
        return filled($value) ? $value : null;
    };
@endphp

<!-- Action Buttons (Screen Only) -->
<div class="action-buttons">
    <button onclick="window.print()" class="btn btn-print">🖨️ Cetak / Save as PDF</button>
    <a href="{{ route('form-order.show', $formOrder->id) }}" class="btn btn-back">↩️ Kembali</a>
</div>

<div class="page">
    <div class="page-content">

        <div class="content-body">

            <div class="topbar">
                <div class="topbar-left">
                    <img src="{{ $logoPath }}" alt="Kobin Tiles" class="brand-logo">
                </div>
                <div class="topbar-right">
                    <div class="doc-title">FORM ORDER</div>
                    <div class="doc-subtitle">GRANITE FIESTA VOL 2</div>
                </div>
            </div>

            <table class="meta-table">
                <tr>
                    <td class="meta-label">Nama Toko</td>
                    <td class="meta-sep">:</td>
                    <td>{{ $formOrder->nama_toko }}</td>
                    <td class="meta-label">Nama Agen</td>
                    <td class="meta-sep">:</td>
                    <td>{{ $formOrder->nama_agen }}</td>
                </tr>
                <tr>
                    <td class="meta-label">PIC / No HP</td>
                    <td class="meta-sep">:</td>
                    <td>{{ $formOrder->pic }} / {{ $formOrder->no_hp }}</td>
                    <td class="meta-label">Nama Sales</td>
                    <td class="meta-sep">:</td>
                    <td>{{ $formOrder->nama_sales }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Email</td>
                    <td class="meta-sep">:</td>
                    <td>{{ $formOrder->email }}</td>
                    <td class="meta-label">Kota</td>
                    <td class="meta-sep">:</td>
                    <td>{{ $formOrder->kota }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Brand</td>
                    <td class="meta-sep">:</td>
                    <td colspan="4">{{ $formOrder->brand }}</td>
                </tr>
            </table>

            <div class="intro">
                Dengan ini saya berkomitmen untuk melakukan <span class="bold">Purchase Order</span> sebagai berikut:
            </div>

            <table class="detail-table">
                <thead>
                    <tr>
                        <th style="width:56%">PAKET</th>
                        <th style="width:22%">POINTS/BLN</th>
                        <th style="width:22%">JUMLAH PENGAMBILAN (PAKET)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($masterTargets as $masterTarget)
                        @php
                            $detail = $formOrder->details->firstWhere('master_target_id', $masterTarget->id);
                            $pointPerPaket = $masterTarget->point ?? 0;
                            $jumlahPengambilan = $detail ? ($detail->jumlah_pengambilan ?? 0) : 0;
                        @endphp
                        <tr>
                            <td>{{ $masterTarget->target }}</td>
                            <td class="center">{{ number_format($pointPerPaket, 0, ',', '.') }}</td>
                            <td class="center">{{ $jumlahPengambilan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="center" colspan="3">Tidak ada paket tersedia</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="section-title">Mekanisme Poin:</div>
            <table class="mekanisme-table">
                <tr>
                    <th style="width:35%">KATEGORI</th>
                    <th style="width:15%">POINT</th>
                    <th style="width:35%">KATEGORI</th>
                    <th style="width:15%">POINT</th>
                </tr>
                <tr>
                    <td>60X60 - DOUBLE LOADING, LIGHT, MEDIUM</td>
                    <td class="center">1 POINT</td>
                    <td>120X60 - LIGHT, MEDIUM, DARK</td>
                    <td class="center">3 POINTS</td>
                </tr>
                <tr>
                    <td>60X60 - DARK, BLACK, MATTE</td>
                    <td class="center">3 POINT</td>
                    <td>120X60 - BLACK, MATTE</td>
                    <td class="center">5 POINTS</td>
                </tr>
                <tr>
                    <td>80X80 - LIGHT, MEDIUM, DARK</td>
                    <td class="center">2 POINT</td>
                    <td>135X60 - LIGHT, MEDIUM, DARK</td>
                    <td class="center">5 POINTS</td>
                </tr>
                <tr>
                    <td>80X80 - BLACK, MATTE</td>
                    <td class="center">3 POINT</td>
                    <td>135X60 - BLACK, MATTE</td>
                    <td class="center">7 POINTS</td>
                </tr>
            </table>

            <div class="section-title">Syarat &amp; Ketentuan:</div>
            <ol class="terms">
                <li>Program ini berlaku selama 6 bulan hingga 28 Februari 2027.</li>
                <li>Formulir Keikutsertaan harus ditandatangani oleh Pihak Toko Peserta, Agen, dan Kobin Tiles.</li>
                <li>Paket Promo berlaku individu dan tidak dapat digabungkan dengan paket atau promo lainnya.</li>
                <li>Pembayaran harus diselesaikan sesuai dengan ketentuan yang telah disepakati.</li>
                <li>Peserta di akhir periode wajib memenuhi pengambilan sesuai Kontrak.</li>
                <li>Besaran target tidak diperkenankan untuk di Downgrade selama periode program.</li>
                <li>Program ini hanya berlaku untuk KW 1.</li>
                <li>Hadiah tidak dapat diuangkan.</li>
                <li>Hadiah kendaraan diberikan dalam kondisi off the road.</li>
                <li>Pengurusan paspor untuk hadiah tour menjadi tanggung jawab masing-masing peserta.</li>
                <li>Harga emas maksimal Rp 2.750.000 per gram. Jika pada akhir periode harga emas melebihi, maka nilai hadiah akan disesuaikan secara proporsional.</li>
            </ol>
        </div>

        <div class="signature-section">
            <table class="signature-labels">
                <tr>
                    <td>
                        <!-- <div class="signature-box">
                            @if($signatureSrc($formOrder->ttd_pic))
                                <img src="{{ $formOrder->ttd_pic }}" alt="TTD PIC">
                            @else
                                <div class="signature-line"></div>
                            @endif
                        </div>
                        <div class="signature-name">( {{ $formOrder->pic }} )</div>
                        <div class="signature-role">Nama Toko</div> -->
                    </td>
                    <td>
                        <!-- <div class="signature-box">
                            @if($signatureSrc($formOrder->ttd_agen))
                                <img src="{{ $formOrder->ttd_agen }}" alt="TTD Agen">
                            @else
                                <div class="signature-line"></div>
                            @endif
                        </div>
                        <div class="signature-name">( {{ $formOrder->nama_agen }} )</div>
                        <div class="signature-role">Nama Agen</div> -->
                    </td>
                    <!-- <td>
                        <div class="signature-box">
                            @if($signatureSrc($formOrder->ttd_kobin_tiles))
                                <img src="{{ $formOrder->ttd_kobin_tiles }}" alt="TTD Kobin Tiles">
                            @else
                                <div class="signature-line"></div>
                            @endif
                        </div>
                        <div class="signature-name">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
                        <div class="signature-role">Kobin Tiles</div>
                    </td> -->
                    <td>
                        <div class="signature-box">
                            @if($signatureSrc($formOrder->ttd_nama_terang))
                                <img src="{{ $formOrder->ttd_nama_terang }}" alt="TTD Agen">
                            @else
                                <div style="text-align:center; padding-top:10mm; font-size:14pt; letter-spacing:3px; color:#9ca3af;"></div>
                            @endif
                        </div>
                        <div class="signature-name">( {{ $formOrder->nama_terang ?: '..................................................' }} )</div>
                        <div class="signature-role">Pembuat Form Order</div>
                    </td>
                </tr>
            </table>
        </div>


    </div><!-- /page-content -->
</div><!-- /page -->

</body>
</html>