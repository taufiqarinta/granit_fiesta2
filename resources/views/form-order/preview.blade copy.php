<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        /* Sama persis pola referensi report blade yang bisa:
           - @page margin: 0
           - .page height fixed 297mm, position:relative
           - ::after didefinisikan di luar @media (global), lalu di-override di screen/print
           - padding konten di .page-content, bukan di @page
        */

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

            /* Footer background — PRINT */
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

            /* Konten di atas background */
            .page-content {
                position: relative;
                z-index: 10;
                /* 10mm top + 12mm sides + 12mm bottom = sama dengan @page margin lama */
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

            /* Footer background — SCREEN */
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

            /* Konten di atas background */
            .page-content {
                position: relative;
                z-index: 10;
                padding: 10mm 12mm 12mm 12mm;
                min-height: 297mm;
                display: flex;
                flex-direction: column;
            }

            /* Action Buttons */
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
            margin-bottom: 6px;
        }

        .topbar-left, .topbar-right {
            display: table-cell;
            vertical-align: top;
        }

        .topbar-left  { width: 55%; }
        .topbar-right { width: 45%; text-align: right; }

        .brand-logo { height: 20mm; object-fit: contain; }

        .doc-title    { font-size: 12pt; font-weight: 700; }
        .doc-subtitle { font-size: 9pt; margin-top: 2px; }

        .meta-table,
        .detail-table,
        .terms-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td  { padding: 2px 4px; vertical-align: top; }
        .meta-label     { width: 24mm; white-space: nowrap; }
        .meta-sep       { width: 4px; }

        .intro { margin: 6px 0 5px; text-align: justify; font-size: 10pt; }

        .detail-table th, .detail-table td,
        .terms-table  th, .terms-table  td {
            border: 1px solid #111827;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .detail-table th, .terms-table th {
            background: #f3f4f6;
            text-align: center;
            font-size: 9pt;
        }

        .detail-table td { font-size: 9.5pt; }

        .center { text-align: center; }
        .right  { text-align: right; }
        .bold   { font-weight: 700; }

        .mechanism-wrap { margin-top: 4px; max-width: 70mm; }
        .terms-title    { margin: 4px 0 2px; font-size: 8.5pt; font-weight: 700; }

        .terms-table          { font-size: 7.5pt; }
        .terms-table th,
        .terms-table td       { padding: 2px 4px; }

        ol.terms              { margin: 2px 0 0 14px; font-size: 6.8pt; line-height: 1.1; }
        ol.terms li           { margin-bottom: 1px; }

        .signature-labels     { width: 100%; table-layout: fixed; border-collapse: collapse; }
        .signature-labels td  { padding: 0 6px; text-align: center; font-size: 9pt; }
        .signature-box        { height: 20mm; }
        .signature-box img    { max-width: 100%; max-height: 24mm; display: block; margin: 0 auto; object-fit: contain; }
        .signature-line       { border-top: 1px solid #111827; width: 70%; margin: 18mm auto 4px; }
        .signature-name       { font-size: 8pt; }
        .signature-role       { font-size: 7.5pt; }
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
                    <div class="doc-title">Form Order</div>
                    <div class="doc-subtitle">Granite Fiesta</div>
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
                    <td class="meta-label">Kota</td>
                    <td class="meta-sep">:</td>
                    <td>{{ $formOrder->kota }}</td>
                    <td class="meta-label">Brand</td>
                    <td class="meta-sep">:</td>
                    <td>{{ $formOrder->brand }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Lokasi Event</td>
                    <td class="meta-sep">:</td>
                    <td>{{ $formOrder->lokasi_event }}</td>
                    <td class="meta-label">Tanggal</td>
                    <td class="meta-sep">:</td>
                    <td>{{ optional($formOrder->created_at)->format('d/m/Y') }}</td>
                </tr>
            </table>

            <div class="intro">
                Dengan ini saya membuka <span class="bold">Purchase Order</span> untuk:
            </div>

            <table class="detail-table">
                <thead>
                    <tr>
                        <th style="width:10%">No</th>
                        <th style="width:42%">Paket</th>
                        <th style="width:16%">Points/BLN</th>
                        <th style="width:16%">Jumlah Pengambilan</th>
                        <th style="width:16%">Total Point</th>
                    </tr>
                </thead>
                <tbody>
                    @php $indexNo = 1; @endphp
                    @forelse($masterTargets as $masterTarget)
                        @php
                            $detail = $formOrder->details->firstWhere('master_target_id', $masterTarget->id);
                            $pointPerPaket = $masterTarget->point ?? 0;
                            $jumlahPengambilan = $detail ? ($detail->jumlah_pengambilan ?? 0) : 0;
                            $totalPoint = $pointPerPaket * $jumlahPengambilan;
                        @endphp
                        <tr>
                            <td class="center">{{ $indexNo }}</td>
                            <td>{{ $masterTarget->target }}</td>
                            <td class="center">{{ number_format($pointPerPaket, 0, ',', '.') }}</td>
                            <td class="center">{{ $jumlahPengambilan }}</td>
                            <td class="right">{{ number_format($totalPoint, 0, ',', '.') }}</td>
                        </tr>
                        @php $indexNo++; @endphp
                    @empty
                        <tr>
                            <td class="center" colspan="5">Tidak ada paket tersedia</td>
                        </tr>
                    @endforelse
                    <tr>
                        <td colspan="4" class="right bold">TOTAL POINT</td>
                        <td class="right bold">{{ number_format($formOrder->total_point, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="mechanism-wrap">
                <div class="terms-title">Mekanisme Poin:</div>
                <table class="terms-table">
                    <tr>
                        <th style="width:70%">Kategori</th>
                        <th style="width:30%">Point</th>
                    </tr>
                    <tr>
                        <td>Glazed Polished Light</td>
                        <td class="center">1 Point</td>
                    </tr>
                    <tr>
                        <td>Glazed Polished Medium &amp; Dark</td>
                        <td class="center">3 Point</td>
                    </tr>
                </table>
            </div>

            <br>
            
            <ol class="terms">
                <li>Program ini berlaku selama 6 bulan, mulai Juli 2026 - Januari 2027.</li>
                <li>Pembelian paket hanya berlaku untuk KW1 dan tidak termasuk Cavalier Series.</li>
                <li>Harga hanya berlaku untuk toko yang sudah menandatangani Purchase Order dengan jangka waktu sesuai program.</li>
                <li>Paket promo berlaku untuk individu dan tidak dapat digabungkan dengan paket ataupun promo lainnya.</li>
                <li>Selama periode program, tidak diperkenankan untuk pembatalan dan downgrade paket.</li>
                <li>Tempo pembayaran harus diselesaikan sesuai dengan TOP (Term of Payment) yang telah disepakati.</li>
                <li>Program ini tidak bisa dipindahkan ke program lainnya tanpa persetujuan dari Kobin Tiles.</li>
                <li>Hadiah tidak dapat diuangkan.</li>
                <li>Hadiah kendaraan diberikan dalam kondisi off the road.</li>
                <li>Pengurusan paspor untuk hadiah tour menjadi tanggung jawab masing-masing peserta.</li>
                <li>Hadiah Promo Tour berlaku dengan kurs dollar max. Rp 16.500,- bila kurs dollar diatas Rp 16.500,- maka hadiahtour akan dihitung secara proporsional.</li>
                <li>Hadiah emas maksimal Rp. 2.500.000 per gram. Jika pada akhir periode harga emas melebihi Rp. 2.500.000, maka nilai hadiah akan dihitung secara proporsional.</li>
            </ol>

        </div><!-- /content-body -->

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
                                <div class="signature-line"></div>
                            @endif
                        </div>
                        <div class="signature-name">( {{ $formOrder->nama_terang }} )</div>
                        <div class="signature-role">Pembuat Form Order</div>
                    </td>
                </tr>
            </table>
        </div>

    </div><!-- /page-content -->
</div><!-- /page -->

</body>
</html>