<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Detail Form Order') }}
        </h2>
    </x-slot>

    <style>
        body::after {
            content: "";
            position: fixed;
            right: 20px;
            bottom: 20px;
            width: 240px;
            height: 240px;
            background-image: url('{{ asset('corner.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.1;
            pointer-events: none;
            z-index: 5;
        }
        
        .max-w-7xl {
            position: relative;
            z-index: 10;
        }
        
        table {
            background: white;
            position: relative;
            z-index: 15;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-xl font-bold">Detail Form Order</h3>
                            <p class="text-gray-600">Tanggal: {{ $formOrder->tanggal_order_formatted }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('form-order.edit', $formOrder->id) }}" 
                               class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                               style="background-color: #f1c40f;">
                                Edit
                            </a>
                            <a href="{{ route('form-order.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Kembali
                            </a>
                        </div>
                    </div>

                    <!-- Informasi Header -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">NAMA AGEN:</label>
                                <p class="text-gray-900">{{ $formOrder->nama_agen }}</p>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">NAMA SALES:</label>
                                <p class="text-gray-900">{{ $formOrder->nama_sales }}</p>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">BRAND:</label>
                                <p class="text-gray-900">{{ $formOrder->brand }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">NAMA TOKO:</label>
                                <p class="text-gray-900">{{ $formOrder->nama_toko }}</p>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">PIC:</label>
                                <p class="text-gray-900">{{ $formOrder->pic }}</p>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">NO.HP:</label>
                                <p class="text-gray-900">{{ $formOrder->no_hp }}</p>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">KOTA:</label>
                                <p class="text-gray-900">{{ $formOrder->kota }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Voucher -->
                    @if($formOrder->jumlah_voucher > 0)
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4">Informasi Voucher Undian</h4>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-green-800 font-bold mb-2">KODE UNIK VOUCHER:</label>
                                    <p class="text-green-900 font-mono text-lg font-bold">{{ $formOrder->kode_unik_voucher }}</p>
                                </div>
                                <div>
                                    <label class="block text-green-800 font-bold mb-2">JUMLAH VOUCHER:</label>
                                    <p class="text-green-900 text-lg font-bold">{{ $formOrder->jumlah_voucher }} voucher</p>
                                </div>
                            </div>
                            <!-- <div class="mt-3">
                                <label class="block text-green-800 font-bold mb-2">STATUS:</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Aktif
                                </span>
                            </div>
                            <div class="mt-2 text-sm text-green-600">
                                * Setiap 500 point mendapatkan 1 voucher undian
                            </div> -->
                        </div>
                    </div>
                    @else
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4">Informasi Voucher Undian</h4>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                                <p class="font-medium">Belum ada voucher</p>
                                <p class="text-sm mt-1">Total point kurang dari 500</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Detail Paket -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-4">Detail Paket yang Diambil</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-2 border-gray-800">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-sm font-bold text-gray-900 uppercase border-2 border-gray-800">PAKET</th>
                                        <th class="px-4 py-3 text-center text-sm font-bold text-gray-900 uppercase border-2 border-gray-800">POINT PER PAKET</th>
                                        <th class="px-4 py-3 text-center text-sm font-bold text-gray-900 uppercase border-2 border-gray-800">JUMLAH PENGAMBILAN</th>
                                        <th class="px-4 py-3 text-center text-sm font-bold text-gray-900 uppercase border-2 border-gray-800">TOTAL POINT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($formOrder->details as $detail)
                                        <tr>
                                            <td class="px-4 py-4 border-2 border-gray-800 font-medium text-gray-900 text-center">
                                                {{ strtoupper($detail->paket) }}
                                            </td>
                                            <td class="px-4 py-4 text-center border-2 border-gray-800 font-bold">
                                                {{ number_format($detail->point_per_paket, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-4 text-center border-2 border-gray-800">
                                                {{ $detail->jumlah_pengambilan }}
                                            </td>
                                            <td class="px-4 py-4 text-center border-2 border-gray-800 font-bold text-blue-600">
                                                {{ number_format($detail->total_point, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-right font-bold text-lg border-2 border-gray-800">
                                            TOTAL POINT KESELURUHAN
                                        </td>
                                        <td class="px-4 py-4 text-center font-bold text-lg border-2 border-gray-800 text-green-600">
                                            {{ number_format($formOrder->total_point, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <!-- @if($formOrder->jumlah_voucher > 0)
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-right font-bold text-lg border-2 border-gray-800">
                                            TOTAL VOUCHER DIDAPATKAN
                                        </td>
                                        <td class="px-4 py-4 text-center font-bold text-lg border-2 border-gray-800 text-purple-600">
                                            {{ $formOrder->jumlah_voucher }} voucher
                                        </td>
                                    </tr>
                                    @endif -->
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Informasi Perhitungan Voucher -->
                    <!-- @if($formOrder->jumlah_voucher > 0)
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h5 class="font-semibold text-blue-800 mb-2">Perhitungan Voucher:</h5>
                        <div class="text-sm text-blue-700">
                            <p>Total Point: <span class="font-bold">{{ number_format($formOrder->total_point, 0, ',', '.') }}</span></p>
                            <p>Konversi: <span class="font-bold">{{ number_format($formOrder->total_point, 0, ',', '.') }}</span> point ÷ 500 = <span class="font-bold">{{ $formOrder->jumlah_voucher }}</span> voucher</p>
                            <p class="mt-1 text-xs">* Pembulatan ke bawah: {{ floor($formOrder->total_point / 500) }} voucher</p>
                        </div>
                    </div>
                    @endif -->

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('form-order.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>