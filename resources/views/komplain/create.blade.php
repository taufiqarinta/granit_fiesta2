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
                        <h3 class="text-lg font-semibold text-gray-800">Laporan Komplain Pelanggan - New</h3>
                    </div>
                    
                    <div class="p-6">
                        <form method="POST" action="{{ route('komplain.store') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            
                            <!-- Nomor dan Tanggal -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label for="nomor" class="block text-sm font-medium text-gray-700 mb-2">Nomor</label>
                                    <input type="text" name="nomor" id="nomor" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-100" 
                                           value="{{ $nomor }}" readonly>
                                </div>
                                <div>
                                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                           value="{{ date('Y-m-d') }}" required readonly>
                                </div>
                            </div>

                            <!-- Data Pembelian Section -->
                            <div class="p-4 bg-gray-50 rounded-lg border">
                                <h4 class="flex items-center mb-4">
                                    <span class="px-3 py-1 text-sm font-medium text-gray-700 bg-white rounded-full shadow-sm">Data Pembelian</span>
                                </h4>
                                
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama / Company <span class="text-red-500">*</span></label>
                                        <input type="text" name="nama" id="nama" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               value="{{ auth()->user()->name }}" required>
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                        <input type="email" name="email" id="email" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Email Untuk Feedback" required>
                                    </div>
                                    <div>
                                        <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-2">Provinsi <span class="text-red-500">*</span></label>
                                        <select name="provinsi" id="provinsi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <option value="">- Pilih Provinsi -</option>
                                            @foreach ($provinsis as $prov)
                                                <option value="{{ $prov->kode }}">{{ $prov->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="kabupaten" class="block text-sm font-medium text-gray-700 mb-2">Kabupaten/Kota <span class="text-red-500">*</span></label>
                                        <select name="kabupaten" id="kabupaten" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <option value="">- Pilih Kabupaten/Kota -</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="via_agen" class="block text-sm font-medium text-gray-700 mb-2">Via Agen <span class="text-red-500">*</span></label>
                                        <input type="text" name="via_agen" id="via_agen" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               value="{{ auth()->user()->name }}" required>
                                    </div>
                                    <div>
                                        <label for="no_sj" class="block text-sm font-medium text-gray-700 mb-2">No. SJ <span class="text-red-500">*</span></label>
                                        <input type="text" name="no_sj" id="no_sj" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi No. SJ" required maxlength="10">
                                    </div>
                                    <div>
                                        <label for="lampiran_sj" class="block text-sm font-medium text-gray-700 mb-2">
                                            Lampiran SJ <span class="text-red-500">*</span>
                                        </label>

                                        <label for="lampiran_sj"
                                                class="w-full flex items-center justify-between border border-gray-300 rounded-md px-3 py-2 bg-white cursor-pointer">
                                            <span id="file_name_sj" class="text-gray-500 text-sm">Upload file</span>
                                            <span class="inline-block px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                            Browse
                                            </span>
                                            <input type="file" name="lampiran_sj" id="lampiran_sj" class="hidden" required>
                                        </label>
                                    </div>
                                    <div>
                                        <label for="tanggal_pembelian" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pembelian <span class="text-red-500">*</span></label>
                                        <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    </div>
                                    <div>
                                        <label for="tanggal_komplain" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Komplain <span class="text-red-500">*</span></label>
                                        <input type="date" name="tanggal_komplain" id="tanggal_komplain" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    </div>
                                    <div>
                                        <label for="sales" class="block text-sm font-medium text-gray-700 mb-2">Salesman/Salesgirl <span class="text-red-500">*</span></label>
                                        <input type="text" name="sales" id="sales" 
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
                                        <label for="id_merks" class="block text-sm font-medium text-gray-700 mb-2">Merek <span class="text-red-500">*</span></label>
                                        <select name="id_merks" id="id_merks" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <option value="">- Pilih Merek -</option>
                                            @foreach($merks as $merk)
                                                <option value="{{ $merk->id }}">{{ $merk->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="id_ukurans" class="block text-sm font-medium text-gray-700 mb-2">Ukuran <span class="text-red-500">*</span></label>
                                        <select name="id_ukurans" id="id_ukurans" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <option value="">- Pilih Ukuran -</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="id_motifs" class="block text-sm font-medium text-gray-700 mb-2">Motif <span class="text-red-500">*</span></label>
                                        <select name="id_motifs" id="id_motifs" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <option value="">- Pilih Motif -</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="kw" class="block text-sm font-medium text-gray-700 mb-2">KW <span class="text-red-500">*</span></label>
                                        <select name="kw" id="kw" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <option value="KW1">KW1</option>
                                            <option value="KW2">KW2</option>
                                            <option value="KW3">KW3</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="tonality" class="block text-sm font-medium text-gray-700 mb-2">Tonality <span class="text-red-500">*</span></label>
                                        <input type="number" name="tonality" id="tonality" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Tonality" required>
                                    </div>
                                    <div>
                                        <label for="kaliber" class="block text-sm font-medium text-gray-700 mb-2">Kaliber <span class="text-red-500">*</span></label>
                                        <select name="kaliber" id="kaliber" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <option value="-">-</option>
                                            <option value="SS">SS</option>
                                            <option value="S">S</option>
                                            <option value="N">N</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                            <option value="XXL">XXL</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="batch" class="block text-sm font-medium text-gray-700 mb-2">Batch Produksi <span class="text-red-500">*</span></label>
                                        <input type="text" name="batch" id="batch" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Batch Produksi" required maxlength="10">
                                    </div>
                                    <div>
                                        <label for="lampiran_batch" class="block text-sm font-medium text-gray-700 mb-2">
                                            Lampiran Batch Produksi <span class="text-red-500">*</span>
                                        </label>

                                        <!-- Kotak "input palsu" -->
                                        <label for="lampiran_batch"
                                                class="w-full flex items-center justify-between border border-gray-300 rounded-md px-3 py-2 bg-white cursor-pointer">
                                            <span id="file_name_batch" class="text-gray-500 text-sm">Upload file</span>
                                            <span class="inline-block px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                            Browse
                                            </span>
                                            <input type="file" name="lampiran_batch" id="lampiran_batch" class="hidden" required>
                                        </label>
                                        </div>
                                    <div>
                                        <label for="jumlah_order" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Order <span class="text-red-500">*</span></label>
                                        <input type="number" name="jumlah_order" id="jumlah_order" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Jumlah Order" required>
                                    </div>
                                    <div>
                                        <label for="jumlah_kirim" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Kirim <span class="text-red-500">*</span></label>
                                        <input type="number" name="jumlah_kirim" id="jumlah_kirim" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Jumlah Kirim" required>
                                    </div>
                                    <div>
                                        <label for="jumlah_komplain" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Komplain <span class="text-red-500">*</span></label>
                                        <input type="number" name="jumlah_komplain" id="jumlah_komplain" 
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
                                        <label for="jenis_komplain" class="block text-sm font-medium text-gray-700 mb-2">Jenis Komplain <span class="text-red-500">*</span></label>
                                        <input type="text" name="jenis_komplain" id="jenis_komplain" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                               placeholder="Isi Jenis Komplain" required>
                                    </div>
                                    <div>
                                        <label for="lampiran_bukti" class="block text-sm font-medium text-gray-700 mb-2">
                                            Lampiran Bukti Komplain <span class="text-red-500">*</span>
                                        </label>
                                        <label for="lampiran_bukti"
                                                class="w-full flex items-center justify-between border border-gray-300 rounded-md px-3 py-2 bg-white cursor-pointer">
                                            <span id="file_name_bukti" class="text-gray-500 text-sm">Upload file</span>
                                            <span class="inline-block px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                            Browse
                                            </span>
                                            <input type="file" name="lampiran_bukti[]" id="lampiran_bukti" class="hidden" multiple required onchange="validateFiles(this)">
                                        </label>
                                    </div>
                                    <div>
                                        <label for="penyelesaian" class="block text-sm font-medium text-gray-700 mb-2">Penyelesaian yang dikehendaki <span class="text-red-500">*</span></label>
                                        <textarea name="penyelesaian" id="penyelesaian" rows="3" 
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                                  placeholder="Isi penyelesaian yang dikehendaki" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Table -->
                            <div class="overflow-x-auto hidden">
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
                                                <input type="text" name="created_by" value="{{ auth()->user()->name }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="created_date" value="{{ date('Y-m-d') }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="created_time" value="{{ date('H:i:s') }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-1 text-xs font-medium text-white bg-gray-800 rounded">Submitted</span>
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-2 text-sm text-gray-700">Approval 1</td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval1_by" value="QMS" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval1_date" value="{{ date('Y-m-d') }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval1_time" value="{{ date('H:i:s') }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-1 text-xs font-medium text-white bg-gray-800 rounded">Opened</span>
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-2 text-sm text-gray-700">Approval 2</td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval2_by" value="GM" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval2_date" value="{{ date('Y-m-d') }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval2_time" value="{{ date('H:i:s') }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-1 text-xs font-medium text-white bg-gray-800 rounded">Opened</span>
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-2 text-sm text-gray-700">Approval 3</td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval3_by" value="SLS" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval3_date" value="{{ date('Y-m-d') }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval3_time" value="{{ date('H:i:s') }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-1 text-xs font-medium text-white bg-gray-800 rounded">Opened</span>
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-2 text-sm text-gray-700">Approval 4</td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval4_by" value="DIR" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval4_date" value="{{ date('Y-m-d') }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="text" name="approval4_time" value="{{ date('H:i:s') }}" 
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" readonly>
                                            </td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-1 text-xs font-medium text-white bg-gray-800 rounded">Opened</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 text-sm font-medium text-gray-700" colspan="4">Status Final</td>
                                            <td class="px-4 py-2">
                                                <span class="px-2 py-1 text-xs font-medium text-white bg-gray-800 rounded">Submitted</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex justify-end space-x-3">
                                <button type="reset" 
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    Reset
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                        onclick="return confirm('Simpan data komplain?')">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Validasi jumlah order, kirim, dan komplain -->
    <script>
        const jumlahOrder = document.getElementById('jumlah_order');
        const jumlahKirim = document.getElementById('jumlah_kirim');
        const jumlahKomplain = document.getElementById('jumlah_komplain');

        function validateRealTime() {
            const order = parseInt(jumlahOrder.value) || 0;
            const kirim = parseInt(jumlahKirim.value) || 0;
            const komplain = parseInt(jumlahKomplain.value) || 0;

            if (order < kirim) {
            alert("Jumlah Kirim tidak boleh lebih besar dari Jumlah Order.");
            jumlahKirim.value = ""; // reset nilai yang salah
            } else if (kirim < komplain) {
            alert("Jumlah Komplain tidak boleh lebih besar dari Jumlah Kirim.");
            jumlahKomplain.value = ""; // reset nilai yang salah
            }
        }

        jumlahOrder.addEventListener('input', validateRealTime);
        jumlahKirim.addEventListener('input', validateRealTime);
        jumlahKomplain.addEventListener('input', validateRealTime);
    </script>

    <script>
        function validateFiles(input) {
            if (input.files.length > 3) {
                alert("Maksimal 3 file yang diperbolehkan.");
                input.value = ''; // reset file input
                document.getElementById('file_name_bukti').innerText = 'Upload file';
            } else {
                const fileNames = Array.from(input.files).map(file => file.name).join(', ');
                document.getElementById('file_name_bukti').innerText = fileNames;
            }
        }
    </script>


    <script>
        document.getElementById('lampiran_bukti').addEventListener('change', function () {
            const files = Array.from(this.files).map(file => file.name);
            document.getElementById('file_name_bukti').textContent = files.length > 0 ? files.join(', ') : 'Upload file';
        });
    </script>

    

    <script>
    document.getElementById('lampiran_sj').addEventListener('change', function () {
        const fileName = this.files.length > 0 ? this.files[0].name : 'Upload file';
        document.getElementById('file_name_sj').textContent = fileName;
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
    document.getElementById('lampiran_batch').addEventListener('change', function () {
        const fileName = this.files.length > 0 ? this.files[0].name : "Upload file";
        document.getElementById('file_name_batch').textContent = fileName;
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