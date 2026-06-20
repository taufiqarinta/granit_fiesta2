<?php

namespace App\Http\Controllers;

use App\Models\Doorprize;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoorprizeController extends Controller
{
    /**
     * Menampilkan halaman undian doorprize
     */
    public function index()
    {
        $doorprizes = Doorprize::active()->get();
        return view('doorprize.index', compact('doorprizes'));
    }

    public function startUndian(Request $request)
    {
        $request->validate([
            'doorprize_id' => 'required|exists:doorprizes,id'
        ]);

        $doorprize = Doorprize::findOrFail($request->doorprize_id);
        $jumlahPemenang = $doorprize->jumlah_doorprize;
        
        // Cek apakah masih ada voucher yang tersedia
        $voucherTersedia = Voucher::where('status', 0)->count();
        
        if ($voucherTersedia < $jumlahPemenang) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher yang tersedia tidak cukup untuk jumlah doorprize'
            ]);
        }

        // Ambil voucher secara acak yang statusnya masih 0
        $voucherMenang = Voucher::where('status', 0)
            ->inRandomOrder()
            ->limit($jumlahPemenang)
            ->get();

        if ($voucherMenang->count() < $jumlahPemenang) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak cukup voucher yang tersedia untuk diundi'
            ]);
        }

        // Update status voucher menjadi sudah dipakai
        $voucherIds = $voucherMenang->pluck('id')->toArray();
        Voucher::whereIn('id', $voucherIds)->update(['status' => 1]);

        // Format data voucher untuk response
        $vouchers = $voucherMenang->map(function($voucher) {
            return [
                'nomor_voucher' => $voucher->nomor_voucher,
                'nama_toko' => $voucher->nama_toko,
                'nama_pic' => $voucher->nama_pic,
                'kode_unik' => $voucher->kode_unik
            ];
        });

        return response()->json([
            'success' => true,
            'vouchers' => $vouchers,
            'doorprize' => [
                'nama' => $doorprize->nama_doorprize,
                'jumlah' => $doorprize->jumlah_doorprize
            ]
        ]);
    }

    /**
     * Get semua voucher untuk animasi random
     */
    public function getAllVouchersForAnimation()
    {
        $vouchers = Voucher::where('status', 0)
            ->inRandomOrder()
            ->limit(100) // Ambil 200 voucher untuk animasi
            ->get()
            ->map(function($voucher) {
                return [
                    'nomor_voucher' => $voucher->nomor_voucher,
                    'nama_toko' => $voucher->nama_toko,
                    'nama_pic' => $voucher->nama_pic,
                    'kode_unik' => $voucher->kode_unik
                ];
            });

        return response()->json($vouchers);
    }

    public function voucherTersedia()
    {
        $tersedia = Voucher::where('status', 0)->count();
        
        return response()->json([
            'tersedia' => $tersedia
        ]);
    }
}