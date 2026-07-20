<table>
    <thead>
        <tr>
            <th>Lokasi Event</th>
            <th>Kode Toko</th>
            <th>Nama Toko</th>
            <th>PIC</th>
            <th>Nomor PIC</th>
            <th>Kota</th>
            <th>Kode Agen</th>
            <th>Nama Agen</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($groups as $kodeToko => $agenRows)
            @php $rowCount = $agenRows->count(); @endphp
            @foreach($agenRows as $i => $row)
                <tr>
                    @if($i === 0)
                        <td rowspan="{{ $rowCount }}">{{ $row->lokasi_event }}</td>
                        <td rowspan="{{ $rowCount }}">{{ $row->kode_toko }}</td>
                        <td rowspan="{{ $rowCount }}">{{ $row->nama_toko }}</td>
                        <td rowspan="{{ $rowCount }}">{{ $row->pic }}</td>
                        <td rowspan="{{ $rowCount }}">{{ $row->nomor_pic }}</td>
                        <td rowspan="{{ $rowCount }}">{{ $row->kota }}</td>
                    @endif
                    <td>{{ $row->kode_agen }}</td>
                    <td>{{ $row->nama_agen }}</td>
                    @if($i === 0)
                        <td rowspan="{{ $rowCount }}">{{ (int) $row->konfirmasi_kehadiran === 1 ? 'Sudah RSVP' : 'Belum RSVP' }}</td>
                    @endif
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>