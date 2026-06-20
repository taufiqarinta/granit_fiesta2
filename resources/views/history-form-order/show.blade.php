<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail History Order
            </h2>
            <a href="{{ route('history-form-order.index') }}"
                class="text-sm text-red-500 hover:text-red-700 font-semibold">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>

    <div class="py-6 px-4 max-w-5xl mx-auto space-y-6">

        {{-- Header Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-6">
            <div class="flex items-center gap-3 mb-5">
                @if($history->aksi === 'create')
                    <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">✚ CREATE</span>
                @else
                    <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full">✎ UPDATE</span>
                @endif
                <span class="text-gray-400 text-sm">{{ $history->created_at->format('d F Y, H:i:s') }}</span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @php
                $fields = [
                    'Form Order ID' => '#' . $history->form_order_id,
                    'Nama Toko'     => $history->nama_toko,
                    'Kode Toko'     => $history->kode_toko,
                    'Kode Agen'     => $history->kode_agen,
                    'Nama Agen'     => $history->nama_agen,
                    'Brand'         => $history->brand,
                    'Lokasi Event'  => $history->lokasi_event,
                    'Kota'          => $history->kota,
                    'PIC'           => $history->pic,
                    'No. HP'        => $history->no_hp,
                    'Nama Sales'    => $history->nama_sales,
                    'Nama Terang'   => $history->nama_terang,
                ];
                @endphp

                @foreach($fields as $label => $value)
                <div class="bg-red-50/50 rounded-xl p-3">
                    <p class="text-xs font-semibold text-red-400 uppercase tracking-wide mb-1">{{ $label }}</p>
                    <p class="text-sm font-medium text-gray-800">{{ $value ?? '-' }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Summary --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5 text-center">
                <p class="text-xs font-semibold text-red-400 uppercase tracking-wide mb-1">Total Point</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($history->total_point, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5 text-center">
                <p class="text-xs font-semibold text-red-400 uppercase tracking-wide mb-1">Total Kupon</p>
                <p class="text-2xl font-bold text-gray-800">{{ $history->total_kupon }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5 text-center">
                <p class="text-xs font-semibold text-red-400 uppercase tracking-wide mb-1">Jumlah Voucher</p>
                <p class="text-2xl font-bold text-gray-800">{{ $history->jumlah_voucher }}</p>
                @if($history->kode_unik_voucher)
                <p class="text-xs text-gray-400 mt-1">{{ $history->kode_unik_voucher }}</p>
                @endif
            </div>
        </div>

        {{-- Detail Targets --}}
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-red-50">
                <h3 class="font-bold text-gray-800 text-sm">📦 Detail Paket</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-red-50 text-left">
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Paket</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Point/Paket</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Jumlah</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Total Point</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Kupon/Paket</th>
                            <th class="px-4 py-3 text-xs font-700 text-red-500 uppercase tracking-wide">Total Kupon</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-50">
                        @forelse($history->detail_targets ?? [] as $detail)
                        <tr class="hover:bg-red-50/40 transition">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $detail['paket'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ number_format($detail['point_per_paket'] ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800">{{ $detail['jumlah_pengambilan'] ?? 0 }}</td>
                            <td class="px-4 py-3 font-semibold text-red-600">{{ number_format($detail['total_point'] ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $detail['kupon_per_paket'] ?? 0 }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $detail['total_kupon'] ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">
                                Tidak ada detail paket
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Info Pencatat --}}
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5">
            <h3 class="font-bold text-gray-800 text-sm mb-4">👤 Info Pencatat</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="bg-red-50/50 rounded-xl p-3">
                    <p class="text-xs font-semibold text-red-400 uppercase tracking-wide mb-1">Dicatat Oleh</p>
                    <p class="text-sm font-medium text-gray-800">{{ $history->username ?? '-' }}</p>
                </div>
                <div class="bg-red-50/50 rounded-xl p-3">
                    <p class="text-xs font-semibold text-red-400 uppercase tracking-wide mb-1">IP Address</p>
                    <p class="text-sm font-medium text-gray-800">{{ $history->ip_address ?? '-' }}</p>
                </div>
                <div class="bg-red-50/50 rounded-xl p-3">
                    <p class="text-xs font-semibold text-red-400 uppercase tracking-wide mb-1">Waktu</p>
                    <p class="text-sm font-medium text-gray-800">{{ $history->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>