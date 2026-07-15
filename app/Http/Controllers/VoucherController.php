<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\DaftarToko;
use App\Models\MasterLokasiEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Menampilkan halaman cek voucher public.
     * Bisa auto-proses kalau ada parameter 'd' (base64) atau kode_unik[] dari link.
     */
    public function cekVoucherPublic(Request $request)
    {
        // CASE 1: Link dari sistem, bawa 'd' (base64 encoded payload berisi kode_toko)
        if ($request->filled('d')) {
            $kodeToko = $this->decodeKodeTokoFromPayload($request->d);

            if ($kodeToko) {
                return $this->prosesCekVoucherByKodeToko($kodeToko);
            }

            return view('voucher.public')->with('error', 'Link tidak valid.');
        }

        // CASE 2: Link/submit bawa kode_unik[] langsung
        if ($request->has('kode_unik') && is_array($request->kode_unik) && count(array_filter($request->kode_unik))) {
            return $this->prosesCekVoucher($request);
        }

        // CASE 3: Halaman kosong (belum ada input)
        return view('voucher.public');
    }

    /**
     * Proses pencarian voucher berdasarkan kode_unik[] (dari form manual)
     */
    public function prosesCekVoucher(Request $request)
    {
        $request->validate([
            'kode_unik' => 'required|array|min:1',
            'kode_unik.*' => 'required|string|max:20'
        ]);

        $kodeUnikArray = $this->cleanKodeUnikArray($request->kode_unik);

        if (empty($kodeUnikArray)) {
            return back()->with('error', 'Masukkan kode unik voucher!');
        }

        return $this->tampilkanHasil($kodeUnikArray);
    }

    /**
     * Decode payload base64 dan ambil kode_toko dari dalamnya
     */
    private function decodeKodeTokoFromPayload(string $encoded): ?string
    {
        try {
            $decoded = base64_decode($encoded, true);
            if ($decoded === false) return null;

            $data = json_decode($decoded, true);
            if (!is_array($data) || empty($data['kode_toko'])) return null;

            return trim($data['kode_toko']);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Cek apakah lokasi event milik toko masih berlaku.
     * Berlaku sampai jam 23:55:00 di tanggal event tersebut.
     */
    private function lokasiEventMasihBerlaku(string $kodeToko): bool
    {
        $toko = DaftarToko::where('kode_toko', $kodeToko)->first();

        if (!$toko || !$toko->lokasi_event) {
            // Toko tidak ditemukan / tidak punya lokasi_event -> anggap tidak berlaku
            return false;
        }

        $lokasiEvent = MasterLokasiEvent::where('nama_lokasi', $toko->lokasi_event)->first();

        if (!$lokasiEvent) {
            return false;
        }

        $batasWaktu = Carbon::parse($lokasiEvent->tanggal)->setTime(23, 55, 0);

        return now()->lessThanOrEqualTo($batasWaktu);
    }

    /**
     * Proses pencarian voucher berdasarkan kode_toko.
     * Otomatis ambil SEMUA kode_unik yang pernah dibuat toko tersebut.
     */
    private function prosesCekVoucherByKodeToko(string $kodeToko)
    {
        if (empty($kodeToko)) {
            return view('voucher.public')->with('error', 'Kode toko tidak valid.');
        }

        // Cek dulu apakah lokasi event toko ini masih berlaku
        if (!$this->lokasiEventMasihBerlaku($kodeToko)) {
            return view('form-order.event-ended');
        }

        $kodeUnikArray = Voucher::where('kode_toko', $kodeToko)
            ->whereNotNull('kode_unik')
            ->distinct()
            ->pluck('kode_unik')
            ->filter()
            ->values()
            ->toArray();

        if (empty($kodeUnikArray)) {
            return view('voucher.public')->with('error', 'Voucher untuk toko ini tidak ditemukan.');
        }

        return $this->tampilkanHasil($kodeUnikArray);
    }

    /**
     * Bersihkan array kode unik: trim, filter kosong, uppercase
     */
    private function cleanKodeUnikArray(array $kodeUnikArray): array
    {
        $kodeUnikArray = array_map('trim', $kodeUnikArray);
        $kodeUnikArray = array_filter($kodeUnikArray);
        $kodeUnikArray = array_map('strtoupper', $kodeUnikArray);

        return array_values($kodeUnikArray);
    }

    /**
     * Query voucher dan render hasil ke view
     */
    private function tampilkanHasil(array $kodeUnikArray)
    {
        $kodeUnikInput = implode("\n", $kodeUnikArray);

        $vouchers = Voucher::whereIn('kode_unik', $kodeUnikArray)
            ->orderBy('kode_unik')
            ->orderBy('nomor_voucher')
            ->get();

        $groupedVouchers = $vouchers->groupBy('kode_unik');

        return view('voucher.public', compact('vouchers', 'groupedVouchers', 'kodeUnikInput'));
    }
}