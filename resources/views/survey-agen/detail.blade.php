<x-app-layout>
            <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                        {{ __('Detail Form Survey') }}
            </h2>
            <a href="{{ route('form-survey.index') }}" class="text-red-600 hover:text-red-700 font-semibold">← Kembali</a>
        </div>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Banner -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg shadow-sm p-8 text-center mb-2">
                <p class="text-sm opacity-90">Nomor Form Survey</p>
                <h2 class="text-3xl font-bold font-mono mt-2">{{ $survey->kode_survey }}</h2>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- QR Code Section -->
                <div class="p-6 border-b border-gray-200 text-center bg-gray-50">
                    <p class="text-sm font-semibold text-gray-700 mb-4">QR Code Survey</p>
                    @if($qrCodeBase64)
                        <img src="{{ $qrCodeBase64 }}" alt="QR Code" class="w-48 h-48 mx-auto border border-gray-300 rounded-lg p-2 bg-white">
                    @else
                        <p class="text-gray-500">QR Code tidak tersedia</p>
                    @endif
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6 border-b border-gray-200">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase mb-2">Nama Agen</p>
                        <p class="text-xl font-bold text-gray-900">{{ $survey->nama_agen }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase mb-2">Kode Agen</p>
                        <p class="text-xl font-bold text-gray-900">{{ $survey->kode_agen }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase mb-2">Nama Sales</p>
                        <p class="text-xl font-bold text-gray-900">{{ optional($survey->details->first())->nama_sales ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase mb-2">No HP Sales</p>
                        <p class="text-xl font-bold text-gray-900">{{ optional($survey->details->first())->no_hp ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase mb-2">Tanggal Survey</p>
                        <p class="text-xl font-bold text-gray-900">{{ $survey->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-semibold text-gray-600 uppercase mb-2">Status Klaim Hadiah</p>
                        <div id="statusDisplay">
                            @if($survey->status_klaim_hadiah == 1)
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Hadiah Sudah Diklaim
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-red-100 text-red-800">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Hadiah Belum Diklaim
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sales Data -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        Data Sales
                    </h3>
                    <div class="space-y-3">
                        @foreach($survey->details as $index => $detail)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <p class="font-semibold text-gray-900">Nama Sales</p>
                                        <p class="text-gray-600">{{ $detail->nama_sales ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Area</p>
                                        <p class="text-gray-600">{{ $detail->area ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Brands</p>
                                        <p class="text-gray-600">{{ $detail->brands ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Top 10 Pareto</p>
                                        <p class="text-gray-600">{{ $detail->top_10_pareto ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Target Penjualan Perbulan (Box)</p>
                                        <p class="text-gray-600">{{ $detail->target_penjualan ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Keliling Luar Kota</p>
                                        <p class="text-gray-600">{{ $detail->keliling_luar_kota ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Toko Butuh Support</p>
                                        <p class="text-gray-600">{{ $detail->toko_butuh_support ?? '-' }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <p class="font-semibold text-gray-900">Saran untuk Kobin Tiles</p>
                                        <p class="text-gray-600">{{ $detail->saran_kobin ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="p-6 bg-gray-50 flex gap-3 flex-wrap">
                    @if($survey->status_klaim_hadiah == 0)
                        <button 
                            onclick="claimReward()" 
                            id="claimBtn"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition flex items-center justify-center gap-2 min-w-[200px]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13L7 13m5-5l5 5"></path>
                            </svg>
                            Klaim Hadiah
                        </button>
                    @else
                        <a href="{{ route('form-survey.index') }}" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-lg text-center transition min-w-[200px]">
                            Kembali ke Tabel
                        </a>
                        <a href="{{ route('form-survey.scan-qr') }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg text-center transition min-w-[200px]">
                            Kembali ke Scanner
                        </a>
                    @endif

                    @if($survey->status_klaim_hadiah == 0)
                        <a href="{{ route('form-survey.index') }}" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-lg text-center transition min-w-[200px]">
                            Kembali ke Tabel
                        </a>
                    @endif
                </div>
            </div>

            <!-- Loading Overlay -->
            <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" style="display: none;">
                <div class="bg-white rounded-lg p-8 text-center">
                    <div class="inline-block">
                        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600 mt-4">Sedang memproses klaim hadiah...</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
function claimReward() {
    Swal.fire({
        title: 'Konfirmasi Klaim Hadiah',
        text: 'Apakah Anda yakin ingin klaim hadiah untuk survey ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Klaim',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        const claimBtn = document.getElementById('claimBtn');
        const loadingOverlay = document.getElementById('loadingOverlay');
        
        claimBtn.disabled = true;
        loadingOverlay.style.display = 'flex';

        fetch('{{ route("form-survey.claim-reward") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                kode_survey: @json($survey->kode_survey)
            })
        })
        .then(response => response.json())
        .then(data => {
            loadingOverlay.style.display = 'none';

            if (data.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Hadiah berhasil diklaim!',
                    icon: 'success',
                    confirmButtonColor: '#dc2626'
                }).then(() => {
                    window.location.href = '{{ route('form-survey.scan-qr') }}';
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'Terjadi kesalahan saat mengklaim hadiah',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
                claimBtn.disabled = false;
            }
        })
        .catch(error => {
            loadingOverlay.style.display = 'none';
            claimBtn.disabled = false;
            Swal.fire({
                title: 'Error',
                text: 'Terjadi kesalahan: ' + error.message,
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        });
    });
}
</script>

<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>

@if($survey->status_klaim_hadiah == 1)
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        title: 'Sudah Diklaim',
        text: 'Data survey ini sudah di klaim hadiahnya',
        icon: 'info',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false
    });
});
</script>
@endif
