<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 40px;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .info-container {
            margin-bottom: 20px;
        }

        .info-row {
            margin-bottom: 5px;
            white-space: nowrap;
        }

        .info-label {
            display: inline-block;
            width: 150px;
            /* Biar lurus */
            font-weight: bold;
        }

        .info-value {
            display: inline-block;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .total-row td {
            font-weight: bold;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .header h3 {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('Logo-Kobin-Group-Vertikal.png') }}" alt="Logo" style="height: 100px; width: auto;">
        <h3>Detail Order: {{ $order->kode }}</h3>
    </div>

    <div class="title">Report Forecast</div>

    <div class="info-container">
        <div class="info-row">
            <span class="info-label">Nama Customer &nbsp;&nbsp;&nbsp;: {{ $order->user->name }}</span>
            {{-- <span class="info-value"> </span> --}}
        </div>
        <div class="info-row">
            <span class="info-label">Permintaan Bulan : {{ $forecastBulan }}</span>
            {{-- <span class="info-value">Jan 25</span> --}}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Merk</th>
                <th>Motif</th>
                <th>Ukuran</th>
                <th>Prioritas</th>
                <th>Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->permintaans as $item)
                <tr>
                    <td>{{ $item->merk->name ?? '-' }}</td>
                    <td>{{ $item->motif ?? '-' }}</td>
                    <td>{{ $item->ukuran->name ?? '-' }}</td>
                    <td>{{ $item->prioritas ?? 0 }}</td>
                    <td>{{ $item->estimasi ?? 0 }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4">Total</td>
                <td>{{ $totalQty }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 40px; text-align: right; font-size: 10px;">
        Printed by Kobin Forecast Order System {{ now()->format('d/m/Y') }}
    </div>
</body>


</html>
