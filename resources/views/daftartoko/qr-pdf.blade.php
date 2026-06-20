<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code Toko - {{ $lokasiEventName }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 20px;
            color: #333;
        }

        .header p {
            font-size: 11px;
            color: #666;
            margin-top: 4px;
        }

        /* Pakai table bukan flexbox - DomPDF tidak support flex */
        table.qr-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            table-layout: fixed;
        }

        table.qr-grid td {
            width: 33.333%;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px 6px;
            text-align: center;
            vertical-align: top;
        }

        /* Baris kosong (filler) tidak diberi border */
        table.qr-grid td.empty {
            border: none;
            background: transparent;
        }

        .qr-code img {
            width: 130px;
            height: 130px;
            display: block;
            margin: 0 auto;
        }

        .kode-toko {
            font-size: 13px;
            font-weight: bold;
            color: #2563eb;
            margin: 8px 0 4px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }

        .nama-toko {
            font-size: 11px;
            font-weight: bold;
            color: #333;
            margin: 4px 0;
        }

        .alamat {
            font-size: 9px;
            color: #666;
            margin-top: 6px;
            line-height: 1.4;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #999;
        }

        /* Potong halaman setiap 4 baris (12 item) */
        tr.page-break-after {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>QR Code Toko - {{ $lokasiEventName }}</h1>
        <p>Dicetak pada {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @php
        $tokos = $tokos->values(); // pastikan index reset dari 0
        $perPage = 12;
        $perRow  = 3;
        $chunks  = $tokos->chunk($perPage);
    @endphp

    @foreach($chunks as $chunkIndex => $chunk)

        <table class="qr-grid">
            @php
                $rows = $chunk->values()->chunk($perRow);
            @endphp

            @foreach($rows as $rowIndex => $row)
                {{-- Tambah page-break setelah baris ke-4 (baris terakhir tiap halaman),
                     kecuali halaman/chunk terakhir --}}
                <tr @if($rowIndex === 3 && !$loop->parent->last) class="page-break-after" @endif>

                    @foreach($row as $toko)
                    <td>
                        <div class="qr-code">
                            <img src="{{ $toko->qr_code_base64 }}" alt="QR {{ $toko->kode_toko }}">
                        </div>
                        <div class="kode-toko">{{ $toko->kode_toko }}</div>
                        <div class="nama-toko">{{ $toko->nama_toko }}</div>
                        <div class="alamat">
                            {{ Str::limit($toko->alamat, 60) }}<br>
                            {{ $toko->kota }}
                        </div>
                    </td>
                    @endforeach

                    {{-- Isi sel kosong kalau baris tidak penuh --}}
                    @for($i = $row->count(); $i < $perRow; $i++)
                    <td class="empty"></td>
                    @endfor

                </tr>
            @endforeach
        </table>

    @endforeach

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem | {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

</body>
</html>