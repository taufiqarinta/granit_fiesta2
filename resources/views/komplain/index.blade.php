<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Komplain') }}
        </h2>
    </x-slot>

    <div class="px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-2 mb-4 text-sm text-white bg-green-500 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-2 mb-4 text-sm text-white bg-red-500 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col mb-4 space-y-2 md:flex-row md:items-center md:justify-between md:space-y-0">
                <!-- Kiri: Tombol Tambah & Export PDF -->
                <div class="flex space-x-2">
                    <a href="{{ route('komplain.create') }}"
                        class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                        Tambah Komplain
                    </a>

                    <!-- <a href="{{ route('komplain.export.pdf', ['tanggal_awal' => request('tanggal_awal'), 'tanggal_akhir' => request('tanggal_akhir')]) }}"
                        class="px-4 py-2 bg-red-500 text-white rounded">
                        Export PDF
                    </a> -->

                    <a href="{{ route('komplain.export.excel', request()->query()) }}"
                        class="px-4 py-2 bg-green-600 text-white rounded">
                        Export Excel
                    </a>


                </div>

                <!-- Kanan: Form Filter -->
                <form method="GET" action="{{ route('komplain.index') }}" class="w-full md:w-auto">
                    <div class="flex flex-col space-y-2 md:flex-row md:items-end md:space-x-2 md:space-y-0">
                        <div class="w-full md:w-auto">
                            <label class="block mb-1 text-sm">Tanggal Awal</label>
                            <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}"
                                class="w-full p-2 border rounded md:w-auto">
                        </div>
                        <div class="w-full md:w-auto">
                            <label class="block mb-1 text-sm">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                                class="w-full p-2 border rounded md:w-auto">
                        </div>
                        <div class="flex space-x-2">
                            <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded">Filter</button>
                            <a href="{{ route('komplain.index') }}" class="px-4 py-2 bg-gray-300 rounded">Reset</a>
                        </div>
                    </div>
                </form>
            </div>


            <div style="width: 100%; overflow-x: scroll; border: 1px solid #e5e7eb;">
                <table style="width: 2000px; min-width: 2000px;">
                    <thead class="bg-gray-50">
                        <tr class="text-center">
                            <th class="w-6 p-2">No</th>
                            <th class="w-6 p-2">Nomor</th>
                            <th class="p-2">Tanggal</th>
                            <th class="p-2">Nama</th>
                            <th class="p-2">Lokasi Pasang</th>
                            <th class="p-2">No. SJ</th>
                            <th class="p-2">Tanggal Pembelian</th>
                            <th class="p-2">Tanggal Komplain</th>
                            <th class="p-2">Merek</th>
                            <th class="p-2">Ukuran</th>
                            <th class="p-2">Motif</th>
                            <th class="p-2">KW</th>
                            <th class="p-2">Tonality</th>
                            <th class="p-2">Kaliber</th>
                            <th class="p-2">Batch Produksi</th>
                            <th class="p-2">Jumlah Order</th>
                            <th class="p-2">Jumlah Kirim</th>
                            <th class="p-2">Jumlah Komplain</th>
                            <th class="p-2">Jenis Komplain</th>
                            <th class="p-2">Keputusan Penyelesaian</th>
                            <th class="p-2">Status</th>
                            <th class="p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($formLkps as $formLkp)
                            @php
                                $provinsi_text = $wilayahList[$formLkp->provinsi] ?? '-';
                                $kabupaten_text = $wilayahList[$formLkp->kabupaten] ?? '-';

                                $nama_merek = $merks[$formLkp->id_merks] ?? '-';
                                $nama_ukuran = $ukurans[$formLkp->id_ukurans] ?? '-';
                                $nama_motif = $motifs[$formLkp->id_motifs] ?? '-';

                                $dir = asset('lampiran/lkp');
                                $lampiran_sj_text = $formLkp->lampiran_sj ? "<a href='{$dir}/{$formLkp->lampiran_sj}' target='_blank'>{$formLkp->lampiran_sj}</a>" : '-';
                                $lampiran_batch_text = $formLkp->lampiran_batch ? "<a href='{$dir}/{$formLkp->lampiran_batch}' target='_blank'>{$formLkp->lampiran_batch}</a>" : '-';

                                // Status badge
                                if ($formLkp->status == -1) {
                                    $status_text = "<span class='px-2 py-1 text-red-500 text-xs'>Not Valid</span>";
                                } elseif ($formLkp->status == -2) {
                                    $status_text = "<span class='px-2 py-1 text-red-500 text-xs'>Not Approved</span>";
                                } elseif ($formLkp->status == 0 || $formLkp->status == 1) {
                                    switch ($formLkp->flag) {
                                        case 0:
                                            $status_text = "<span class='px-2 py-1 text-black-700 text-xs'>Submitted</span>";
                                            break;
                                        case 1:
                                            $status_text = "<span style='color: #3B82F6; font-size: 12px; padding: 2px 4px;'>Updated by QMS</span>";
                                            break;
                                        case 2:
                                            $status_text = "<span class='px-2 py-1 text-green-700 text-xs'>Valid</span>";
                                            break;
                                        case 3:
                                           $status_text = "<span style='color: #3B82F6; font-size: 12px; padding: 2px 4px;'>Updated by SLS</span>";
                                            break;
                                        case 4:
                                            $status_text = "<span class='px-2 py-1 text-green-700 text-xs'>Approved</span>";
                                            break;
                                        default:
                                            $status_text = "<span class='px-2 py-1 text-black-700 text-xs'>Unknown</span>";
                                            break;
                                    }
                                } else {
                                    $status_text = "<span class='px-2 py-1 text-black-700 text-xs'>Unknown</span>";
                                }

                            @endphp
                            <tr>
                                <td class="p-2">{{ $loop->iteration }}</td>
                                <td class="p-2">{{ $formLkp->nomor }}</td>
                                <td class="p-2">{{ $formLkp->tanggal }}</td>
                                <td class="p-2">{{ $formLkp->nama }}</td>
                                <td class="p-2">{{ $provinsi_text }}, {{ $kabupaten_text }}</td>
                                <td class="p-2">
                                    <a href="{{ asset($formLkp->lampiran_sj) }}" 
                                    target="_blank" 
                                    class="text-blue-600 hover:text-blue-800">
                                        {{ $formLkp->no_sj }}
                                    </a>
                                </td>
                                <td class="p-2">{{ $formLkp->tanggal_pembelian }}</td>
                                <td class="p-2">{{ $formLkp->tanggal_komplain }}</td>
                                <td class="p-2">{{ $nama_merek }}</td>
                                <td class="p-2">{{ $nama_ukuran }}</td>
                                <td class="p-2">{{ $nama_motif }}</td>
                                <td class="p-2">{{ $formLkp->kw }}</td>
                                <td class="p-2">{{ $formLkp->tonality }}</td>
                                <td class="p-2">{{ $formLkp->kaliber }}</td>
                                <td class="p-2">
                                    <a href="{{ asset($formLkp->lampiran_batch) }}" 
                                    target="_blank" 
                                    class="text-blue-600 hover:text-blue-800">
                                        {{ $formLkp->batch }}
                                    </a>
                                </td>
                                <td class="p-2">{{ $formLkp->jumlah_order }}</td>
                                <td class="p-2">{{ $formLkp->jumlah_kirim }}</td>
                                <td class="p-2">{{ $formLkp->jumlah_komplain }}</td>
                                <td class="p-2">{{ $formLkp->jenis_komplain }}</td>
                                <td class="p-2">{{ $formLkp->keputusan }}</td>
                                <td class="p-2">{!! $status_text !!}</td>
                                <td class="p-2">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('komplain.show', $formLkp->id) }}" class="text-blue-600 hover:text-blue-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zm0 12.5a5 5 0 110-10 5 5 0 010 10z"/>
                                                <circle cx="12" cy="12" r="2.5"/>
                                            </svg>
                                        </a>
                                        <button onclick="sendWAKomplain({{ $formLkp->id }})"
                                            class="text-green-600 hover:text-green-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                viewBox="0 0 32 32" fill="currentColor">
                                                <!-- Icon WhatsApp simple -->
                                                <path
                                                    d="M16 2C8.3 2 2 8.3 2 16c0 2.8.8 5.5 2.3 7.9L2 30l6.2-2.2C10.5 29.2 13.2 30 16 30c7.7 0 14-6.3 14-14S23.7 2 16 2zm0 26c-2.4 0-4.7-.7-6.7-1.9l-.5-.3-3.7 1.3 1.2-3.6-.4-.6C4.7 20.9 4 18.5 4 16c0-6.6 5.4-12 12-12s12 5.4 12 12-5.4 12-12 12zm6.5-8c-.4-.2-2.4-1.2-2.8-1.4-.4-.2-.7-.2-1 .2-.3.4-1.1 1.4-1.3 1.7-.2.3-.5.4-.9.2-.4-.2-1.7-.6-3.2-2-1.2-1.1-2-2.4-2.3-2.8-.2-.4 0-.7.2-.9.2-.2.4-.5.6-.8.2-.2.3-.4.5-.7.2-.2.1-.5 0-.7-.2-.2-1-2.5-1.4-3.4-.3-.7-.7-.6-1-.6h-.9c-.3 0-.7.1-1 .4s-1.3 1.2-1.3 3c0 1.8 1.3 3.5 1.5 3.7.2.2 2.6 4 6.3 5.6.9.4 1.6.6 2.2.8.9.3 1.7.3 2.3.2.7-.1 2.4-1 2.7-1.9.3-.8.3-1.5.2-1.9-.1-.3-.4-.4-.8-.6z" />
                                            </svg>
                                        </button>

                                        @php
                                            $adminEmail = 'admsales.kobin@gmail.com';
                                            $userName = auth()->user()->name;
                                            $userEmail = auth()->user()->email;
                                            $subject = 'Laporan Komplain Pelanggan';

                                            // Ambil nama provinsi (kode 2 digit)
                                            $kodeProvinsi = substr($formLkp->provinsi, 0, 2);
                                            $namaProvinsi = DB::table('wilayah')
                                                ->whereRaw('CHAR_LENGTH(kode) = 2')
                                                ->where('kode', $kodeProvinsi)
                                                ->value('nama');

                                            // Ambil nama kabupaten (kode 5 digit)
                                            $kodeKabupaten = substr($formLkp->kabupaten, 0, 5);
                                            $namaKabupaten = DB::table('wilayah')
                                                ->whereRaw('CHAR_LENGTH(kode) = 5')
                                                ->where('kode', $kodeKabupaten)
                                                ->value('nama');

                                            $lokasiPasangText = "Lokasi Pasang : {$namaProvinsi}, {$namaKabupaten}\n";


                                            $body = "Nomor : {$formLkp->nomor}\n"
                                                . "Tanggal : {$formLkp->tanggal}\n"
                                                . "Nama / Company : {$formLkp->nama}\n"
                                                . "Lokasi Pasang : {$lokasiPasangText}"
                                                . "Via Agen : {$formLkp->via_agen}\n"
                                                . "No. SJ : {$formLkp->no_sj}\n"
                                                . "Tanggal Pembelian : {$formLkp->tanggal_pembelian}\n"
                                                . "Tanggal Komplain : {$formLkp->tanggal_komplain}\n"
                                                . "Salesman/Salesgirl : {$formLkp->sales}\n"
                                                . "Merek : {$formLkp->merks->name}\n"
                                                . "Ukuran : {$formLkp->ukurans->name}\n"
                                                . "Motif : {$formLkp->motifs->name}\n"
                                                . "KW : {$formLkp->kw}\n"
                                                . "Tonality : {$formLkp->tonality}\n"
                                                . "Kaliber : {$formLkp->kaliber}\n"
                                                . "Batch Produksi : {$formLkp->batch}\n"
                                                . "Jumlah Order : {$formLkp->jumlah_order}\n"
                                                . "Jumlah Kirim : {$formLkp->jumlah_kirim}\n"
                                                . "Jumlah Komplain : {$formLkp->jumlah_komplain}\n"
                                                . "Jenis Komplain : {$formLkp->jenis_komplain}\n"
                                                . "Penyelesaian yang dikehendaki : {$formLkp->penyelesaian}\n\n"
                                                . "Mohon untuk tidak membalas email ini, karena ini adalah email otomatis.";

                                            $gmailUrl = "https://mail.google.com/mail/?view=cm&fs=1&to=$adminEmail&su=" . urlencode($subject) . '&body=' . urlencode($body);
                                        @endphp


                                        <a href="{{ $gmailUrl }}" target="_blank"
                                            class="text-red-600 hover:text-red-800 inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M2.25 4.5A2.25 2.25 0 0 1 4.5 2.25h15a2.25 2.25 0 0 1 2.25 2.25v15a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 2.25 19.5v-15ZM4.5 5.318v13.364h15V5.318l-7.5 4.909L4.5 5.318Zm1.36-.818h12.28L12 9.023 5.86 4.5Z" />
                                            </svg>
                                        </a>


                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                </table>
            </div>
            <div class="mt-4">
                {{ $formLkps->links() }}
            </div>
        </div>
    </div>


    <script>
        function sendWAKomplain(id) {
            fetch(`/send-wa-komplain/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.url) {
                    window.open(data.url, '_blank');
                } else {
                    alert('Gagal generate WA link.');
                }
            }).catch(err => {
                alert('Terjadi kesalahan.');
                console.error(err);
            });     
        }
    </script>


</x-app-layout>
