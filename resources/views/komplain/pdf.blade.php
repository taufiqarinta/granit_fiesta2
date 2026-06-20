<!DOCTYPE html>
<html>
<head>
    <title>Data Komplain</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <h3>Data Komplain</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Dibuat Oleh</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($formLkps as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->nomor }}</td>
                <td>{{ $item->created_date }}</td>
                <td>{{ $item->created_by }}</td>
                <td>{{ $item->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
