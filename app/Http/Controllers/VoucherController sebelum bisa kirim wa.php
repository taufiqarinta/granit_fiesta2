<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Menampilkan halaman cek voucher public
     */
    public function cekVoucherPublic()
    {
        return view('voucher.public');
    }

    /**
     * Proses pencarian voucher
     */
    public function prosesCekVoucher(Request $request)
    {
        $request->validate([
            'kode_unik' => 'required|array|min:1',
            'kode_unik.*' => 'required|string|max:20'
        ]);

        // Ambil kode unik dari array input
        $kodeUnikArray = $request->kode_unik;
        
        // Bersihkan dan filter kode unik
        $kodeUnikArray = array_map('trim', $kodeUnikArray);
        $kodeUnikArray = array_filter($kodeUnikArray);
        $kodeUnikArray = array_map('strtoupper', $kodeUnikArray);

        if (empty($kodeUnikArray)) {
            return back()->with('error', 'Masukkan kode unik voucher!');
        }

        // Gabungkan kode unik untuk ditampilkan kembali di form
        $kodeUnikInput = implode("\n", $kodeUnikArray);

        // Cari voucher berdasarkan kode unik
        $vouchers = Voucher::whereIn('kode_unik', $kodeUnikArray)
            ->orderBy('kode_unik')
            ->orderBy('nomor_voucher')
            ->get();

        // Group by kode unik untuk memudahkan tampilan
        $groupedVouchers = $vouchers->groupBy('kode_unik');

        return view('voucher.public', compact('vouchers', 'groupedVouchers', 'kodeUnikInput'));
    }
}