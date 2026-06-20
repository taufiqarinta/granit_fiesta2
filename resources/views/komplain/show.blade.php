<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Komplain Pelanggan') }}
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
                <!-- Form Laporan Komplain Pelanggan -->
                <div class="w-full bg-white rounded-lg shadow-lg">
                    <div class="px-6 py-4 bg-gray-50 border-b rounded-t-lg">
                        <h3 class="text-lg font-semibold text-gray-800">Laporan Komplain Pelanggan - update</h3>
                    </div>
                    
                    <div class="p-6">
                        <form method="POST" action="{{ route('komplain.update', $form->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Nomor dan Tanggal -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-4">
                                <div>
                                    <label for="nomor" class="block text-sm font-medium text-gray-700 mb-2">Nomor</label>
                                    <input type="text" name="nomor" id="nomor" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-100" 
                                           value="{{ $form->nomor }}" readonly>
                                </div>
                                <div>
                                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                           value="{{ $form->tanggal }}" required readonly>
                                </div>
                            </div>

                            <!-- Data Pembelian Section -->
                            <div class="p-4 bg-gray-50 rounded-lg border">
                                <h4 class="flex items-center mb-4">
                                    <span class="px-3 py-1 text-sm font-medium text-gray-700 bg-white rounded-full shadow-sm">Data Pembelian</span>
                                </h4>
                                
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama / Company</label>
                                        <input type="text" name="nama" id="nama" value="{{ $form->nama }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Nama / Company" required>
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input type="email" name="email" id="email" value="{{ $form->email }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                required>
                                    </div>
                                    <div>
                                        <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
                                        <input type="text" name="provinsi" id="provinsi" value="{{ $namaProvinsi }}" readonly
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                            required>
                                    </div>

                                    <div>
                                        <label for="kabupaten" class="block text-sm font-medium text-gray-700 mb-2">Kabupaten/Kota</label>
                                        <input type="text" name="kabupaten" id="kabupaten" value="{{ $namaKabupaten }}" readonly
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                            required>
                                    </div>
                                    <div>
                                        <label for="via_agen" class="block text-sm font-medium text-gray-700 mb-2">Via Agen</label>
                                        <input type="text" name="via_agen" id="via_agen" value="{{ $form->via_agen }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Nama Agen" required>
                                    </div>
                                    <div>
                                        <label for="no_sj" class="block text-sm font-medium text-gray-700 mb-2">No. SJ</label>
                                        <input type="text" name="no_sj" id="no_sj" value="{{ $form->no_sj }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi No. SJ" required maxlength="10">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran SJ</label>
                                        @if($form->lampiran_sj)
                                            <a href="{{ asset($form->lampiran_sj) }}" 
                                            target="_blank" 
                                            class="text-blue-600 hover:underline">
                                                Lihat Lampiran SJ
                                            </a>
                                        @else
                                            <p class="text-gray-500">Tidak ada lampiran</p>
                                        @endif
                                    </div>

                                    <div>
                                        <label for="tanggal_pembelian" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pembelian</label>
                                        <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" value="{{ $form->tanggal_pembelian }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    </div>
                                    <div>
                                        <label for="tanggal_komplain" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Komplain</label>
                                        <input type="date" name="tanggal_komplain" id="tanggal_komplain" value="{{ $form->tanggal_komplain }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    </div>
                                    <div>
                                        <label for="sales" class="block text-sm font-medium text-gray-700 mb-2">Salesman/Salesgirl</label>
                                        <input type="text" name="sales" id="sales" value="{{ $form->sales }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Salesman/Salesgirl" required maxlength="10">
                                    </div>
                                </div>
                            </div>

                            <!-- Data Barang Section -->
                            <div class="p-4 bg-gray-50 rounded-lg border">
                                <h4 class="flex items-center mb-4">
                                    <span class="px-3 py-1 text-sm font-medium text-gray-700 bg-white rounded-full shadow-sm">Data Barang</span>
                                </h4>
                                
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="id_merks" class="block text-sm font-medium text-gray-700 mb-2">Merek</label>
                                        <input type="id_merks" name="id_merks" id="id_merks" value="{{ $form->merks->name }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                required>
                                    </div>
                                    <div>
                                        <label for="id_ukurans" class="block text-sm font-medium text-gray-700 mb-2">Ukuran</label>
                                        <input type="id_ukurans" name="id_ukurans" id="id_ukurans" value="{{ $form->ukurans->name }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                required>
                                    </div>
                                    <div>
                                        <label for="id_motifs" class="block text-sm font-medium text-gray-700 mb-2">Motif</label>
                                        <input type="id_motifs" name="id_motifs" id="id_motifs" value="{{ $form->motifs->name }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                required>
                                    </div>
                                    <div>
                                        <label for="kw" class="block text-sm font-medium text-gray-700 mb-2">KW</label>
                                        <input type="kw" name="kw" id="kw" value="{{ $form->kw }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                required>
                                    </div>
                                    <div>
                                        <label for="tonality" class="block text-sm font-medium text-gray-700 mb-2">Tonality</label>
                                        <input type="number" name="tonality" id="tonality" value="{{ $form->tonality }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Tonality" required>
                                    </div>
                                    <div>
                                        <label for="kaliber" class="block text-sm font-medium text-gray-700 mb-2">Kaliber</label>
                                        <input type="kaliber" name="kaliber" id="kaliber" value="{{ $form->kaliber }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                required>
                                    </div>
                                    <div>
                                        <label for="batch" class="block text-sm font-medium text-gray-700 mb-2">Batch Produksi</label>
                                        <input type="text" name="batch" id="batch" value="{{ $form->batch }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Batch Produksi" required maxlength="10">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran Batch Produksi</label>
                                        @if($form->lampiran_batch)
                                            <a href="{{ asset($form->lampiran_batch) }}" 
                                            target="_blank" 
                                            class="text-blue-600 hover:underline">
                                                Lihat Lampiran Batch
                                            </a>
                                        @else
                                            <p class="text-gray-500">Tidak ada lampiran</p>
                                        @endif
                                    </div>
                                    <div>
                                        <label for="jumlah_order" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Order</label>
                                        <input type="number" name="jumlah_order" id="jumlah_order" value="{{ $form->jumlah_order }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Jumlah Order" required>
                                    </div>
                                    <div>
                                        <label for="jumlah_kirim" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Kirim</label>
                                        <input type="number" name="jumlah_kirim" id="jumlah_kirim" value="{{ $form->jumlah_kirim }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Jumlah Kirim" required>
                                    </div>
                                    <div>
                                        <label for="jumlah_komplain" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Komplain</label>
                                        <input type="number" name="jumlah_komplain" id="jumlah_komplain" value="{{ $form->jumlah_komplain }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Jumlah Komplain" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Komplain Section -->
                            <div class="p-4 bg-gray-50 rounded-lg border">
                                <h4 class="flex items-center mb-4">
                                    <span class="px-3 py-1 text-sm font-medium text-gray-700 bg-white rounded-full shadow-sm">Komplain</span>
                                </h4>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="jenis_komplain" class="block text-sm font-medium text-gray-700 mb-2">Jenis Komplain</label>
                                        <input type="text" name="jenis_komplain" id="jenis_komplain" value="{{ $form->jenis_komplain }}" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Jenis Komplain" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran Bukti Komplain</label>

                                        @if(isset($lampiranBukti) && count($lampiranBukti) > 0)
                                            <ul class="list-disc pl-5 space-y-1">
                                                @foreach($lampiranBukti as $file)
                                                    <li>
                                                        <a href="{{ asset('lampiran_bukti/' . $file) }}" target="_blank" class="text-blue-600 hover:underline">
                                                            Lihat Lampiran {{ $loop->iteration }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-gray-500">Tidak ada lampiran</p>
                                        @endif
                                    </div>


                                    <div>
                                        <label for="penyelesaian" class="block text-sm font-medium text-gray-700 mb-2">Penyelesaian yang dikehendaki</label>
                                        <textarea name="penyelesaian" id="penyelesaian" rows="3" readonly
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                required>{{ $form->penyelesaian }}</textarea>
                                    </div>
                                    <div>
                                        <label for="analisa" class="block text-sm font-medium text-gray-700 mb-2">Hasil Analisa Produk</label>
                                        <textarea name="analisa" id="analisa" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        {{ !(auth()->user()->name == 'QMS' && $form->approval2_status == 0) ? 'readonly' : '' }}
                                        required>{{ $form->analisa }}</textarea>

                                    </div>
                                    <div>
                                        <label for="lampiran_analisa" class="block text-sm font-medium text-gray-700 mb-2">
                                            Lampiran Hasil Analisa Produk (Optional)
                                        </label>

                                        @if($form->approval2_status == 0 && auth()->user()->name == 'QMS')
                                            <!-- Kotak "input palsu" -->
                                            <label for="lampiran_analisa"
                                                class="w-full flex items-center justify-between border border-gray-300 rounded-md px-3 py-2 bg-white cursor-pointer">
                                                <span id="file_name_analisa" class="text-gray-500 text-sm">
                                                    {{ $form->lampiran_analisa ? basename($form->lampiran_analisa) : 'Upload file' }}
                                                </span>
                                                <span class="inline-block px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                                    Browse
                                                </span>
                                                <input type="file" name="lampiran_analisa" id="lampiran_analisa" class="hidden">
                                            </label>
                                        @else
                                            <input type="file" disabled
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-500">
                                        @endif

                                        @if($form->lampiran_analisa)
                                            <p class="mt-2 text-sm text-green-600">
                                                Lampiran sudah diunggah: 
                                                <a href="{{ asset($form->lampiran_analisa) }}" target="_blank" class="underline text-blue-600 hover:text-blue-800">
                                                    Lihat Lampiran
                                                </a>
                                            </p>
                                        @endif
                                    </div>

                                    <div>
                                        <label for="keputusan" class="block text-sm font-medium text-gray-700 mb-2">Keputusan Penyelesaian</label>
                                        <select
                                            class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            name="keputusan"
                                            id="keputusan"
                                            {{ !(auth()->user()->name == 'SLS' && $form->approval4_status == 0) ? 'disabled' : '' }}>
                                            <option value=""></option>
                                            <option value="Penggantian Barang" {{ $form->keputusan == 'Penggantian Barang' ? 'selected' : '' }}>Penggantian Barang</option>
                                            <option value="Penggantian Non Barang" {{ $form->keputusan == 'Penggantian Non Barang' ? 'selected' : '' }}>Penggantian Non Barang</option>
                                            <option value="Tidak Dilanjutkan" {{ $form->keputusan == 'Tidak Dilanjutkan' ? 'selected' : '' }}>Tidak Dilanjutkan</option>
                                        </select>

                                    </div>


                                </div>
                            </div>

                            <!-- Status Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 border-b">#</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 border-b">Username</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 border-b">Date</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 border-b">Time</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 border-b">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <tr class="border-b">
                                            <td class="px-4 py-2 text-sm text-gray-700">Creator</td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="created_by" value="{{ $form->user->name }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="created_date" value="{{ $form->created_date }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="created_time" value="{{ $form->created_time }} " 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-1 text-xs font-medium text-black ">Submitted</span>
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-2 text-sm text-gray-700">Approval 1</td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval1_by" value="{{ $form->approval1_by }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval1_date" value="{{ $form->approval1_date }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval1_time" value="{{ $form->approval1_time }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                @if($form->flag == 0 && $form->status >= 0 && auth()->user()->name == 'QMS')
                                                    <button type="submit"
                                                            class="px-2 py-1 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded">
                                                        Simpan
                                                    </button>
                                                @elseif($form->approval1_status > 0)
                                                    <span class="px-2 py-1 text-xs font-medium text-blue-600">Updated by QMS</span>
                                                    @if(auth()->user()->name == 'QMS' && $form->approval2_status == 0 )
                                                    <button type="submit"
                                                            class="px-2 py-1 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded">
                                                        Update
                                                    </button>
                                                    @endif
                                                @else
                                                    <span class="px-2 py-1 text-xs font-medium text-black">Opened</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-2 text-sm text-gray-700">Approval 2</td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval2_by" value="{{ $form->approval2_by }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval2_date" value="{{ $form->approval2_date }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval2_time" value="{{ $form->approval2_time }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                @if(auth()->user()->name == 'GM' && $form->flag == 1)
                                                    <button type="submit" name="approval2_status" value="1"
                                                            class="px-2 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded">
                                                        Valid
                                                    </button>
                                                    <button type="submit" name="approval2_status" value="-1"
                                                            class="px-2 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded ml-2">
                                                        Not Valid
                                                    </button>
                                                @else
                                                    @if($form->approval2_status == 1)
                                                        <span class="px-2 py-1 text-xs font-medium text-green-600">Valid</span>
                                                    @elseif($form->approval2_status == -1)
                                                        <span class="px-2 py-1 text-xs font-medium text-red-600">Not Valid</span>
                                                    @else
                                                        <span class="px-2 py-1 text-xs font-medium text-black">Opened</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-2 text-sm text-gray-700">Approval 3</td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval3_by" value="{{ $form->approval3_by }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval3_date" value="{{ $form->approval3_date }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval3_time" value="{{ $form->approval3_time }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                @if($form->flag == 2 && $form->approval3_status == 0 && auth()->user()->name == 'SLS')
                                                    <button type="submit"
                                                            class="px-2 py-1 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded">
                                                        Simpan
                                                    </button>
                                                @elseif($form->approval3_status == 1)
                                                    <span class="px-2 py-1 text-xs font-medium text-blue-600">Updated by SLS</span>
                                                    @if(auth()->user()->name == 'SLS' && $form->approval4_status == 0 )
                                                    <button type="submit"
                                                            class="px-2 py-1 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded">
                                                        Update
                                                    </button>
                                                    @endif
                                                @else
                                                    <span class="px-2 py-1 text-xs font-medium text-black">Opened</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-2 text-sm text-gray-700">Approval 4</td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval4_by" value="{{ $form->approval4_by }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval4_date" value="{{ $form->approval4_date }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval4_time" value="{{ $form->approval4_time }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                @if(auth()->user()->name == 'DIR' && $form->flag == 3 && $form->approval3_status == 1)
                                                    <button type="submit" name="approval4_status" value="1"
                                                            class="px-2 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded">
                                                        Approve
                                                    </button>
                                                    <button type="submit" name="approval4_status" value="-1"
                                                            class="px-2 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded">
                                                        Not Approve
                                                    </button>
                                                @elseif($form->approval4_status == 1)
                                                    <span class="px-2 py-1 text-xs font-medium text-green-600">Approved</span>
                                                @elseif($form->approval4_status == -1)
                                                    <span class="px-2 py-1 text-xs font-medium text-red-600">Not Approved</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-medium text-black">Opened</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 text-sm font-medium text-gray-700" colspan="4">Status Final</td>
                                            <td class="px-4 py-2">
                                                @php
                                                    $flag = (int) $form->flag;

                                                    $statusText = match ($flag) {
                                                        0 => 'Submitted',
                                                        1 => 'Updated by QMS',
                                                        2 => 'Valid',
                                                        3 => 'Updated by SLS',
                                                        4 => 'Approved',
                                                        -1 => 'Not Valid',
                                                        -2 => 'Not Approved',
                                                        default => 'Unknown'
                                                    };

                                                    $txtColor = match ($flag) {
                                                        4,2 => 'text-green-700',
                                                        3, 1 => 'text-blue-600',
                                                        -1, -2 => 'text-red-500',
                                                        default => 'text-black'
                                                    };
                                                @endphp

                                                <span class="px-2 py-1 text-xs font-medium {{ $txtColor }}">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                        <!-- Kembali Buttons -->
                        <div class="flex justify-end">
                            <a href="{{ route('komplain.index') }}"
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-600 border border-transparent rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('lampiran_analisa')?.addEventListener('change', function () {
            const fileName = this.files.length > 0 ? this.files[0].name : "Upload file";
            document.getElementById('file_name_analisa').textContent = fileName;
        });
    </script>

    <script>
        document.getElementById('id_merks').addEventListener('change', function () {
            let merkId = this.value;
            let ukuranSelect = document.getElementById('id_ukurans');
            let motifSelect = document.getElementById('id_motifs');
            console.log(merkId);

            ukuranSelect.innerHTML = '<option value="">- Pilih Ukuran -</option>';
            motifSelect.innerHTML = '<option value="">- Pilih Motif -</option>';
            ukuranSelect.disabled = true;
            motifSelect.disabled = true;

            if (merkId) {
                fetch(`/get-ukurans/${merkId}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(item => {
                            let opt = document.createElement('option');
                            opt.value = item.id;
                            opt.textContent = item.name;
                            ukuranSelect.appendChild(opt);
                        });
                        ukuranSelect.disabled = false;
                    });
            }
        });

        document.getElementById('id_ukurans').addEventListener('change', function () {
            let ukuranId = this.value;
            let motifSelect = document.getElementById('id_motifs');

            motifSelect.innerHTML = '<option value="">- Pilih Motif -</option>';
            motifSelect.disabled = true;

            if (ukuranId) {
                fetch(`/get-motifs/${ukuranId}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(item => {
                            let opt = document.createElement('option');
                            opt.value = item.id;
                            opt.textContent = item.name;
                            motifSelect.appendChild(opt);
                        });
                        motifSelect.disabled = false;
                    });
            }
        });
    </script>
    
    <script>
        document.getElementById('provinsi').addEventListener('change', function () {
            console.log("masuk");
            const provinsiKode = this.value;
            const kabupatenSelect = document.getElementById('kabupaten');

            kabupatenSelect.innerHTML = '<option value="">Memuat...</option>';

            if (provinsiKode) {
                fetch(`/kabupaten?kode_provinsi=${provinsiKode}`)
                    .then(response => response.json())
                    .then(data => {
                        kabupatenSelect.innerHTML = '<option value="">- Pilih Kabupaten/Kota -</option>';
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.kode;
                            option.textContent = item.nama;
                            kabupatenSelect.appendChild(option);
                        });
                    });
            } else {
                kabupatenSelect.innerHTML = '<option value="">- Pilih Kabupaten/Kota -</option>';
            }
        });
    </script>

</x-app-layout>