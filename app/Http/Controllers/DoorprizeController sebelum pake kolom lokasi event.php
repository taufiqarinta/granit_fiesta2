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
    public function index($lokasi)
    {
        // Validasi lokasi event
        $lokasi = strtoupper($lokasi);
        // $validLokasi = ['JAKARTA', 'SEMARANG', 'SURABAYA', 'BANDUNG', 'MEDAN']; // Sesuaikan dengan lokasi yang ada
        
        // if (!in_array($lokasi, $validLokasi)) {
        //     abort(404, 'Lokasi event tidak valid');
        // }

        $doorprizes = Doorprize::active()->get();
        return view('doorprize.index', compact('doorprizes', 'lokasi'));
    }

    public function startUndian(Request $request, $lokasi)
    {
        $request->validate([
            'doorprize_id' => 'required|exists:doorprizes,id'
        ]);

        $lokasi = strtoupper($lokasi);

        $doorprize = Doorprize::findOrFail($request->doorprize_id);
        $jumlahPemenang = $doorprize->jumlah_doorprize;
        
        // Cek apakah masih ada voucher yang tersedia untuk lokasi ini
        $voucherTersedia = Voucher::where('status', 0)
            ->where('lokasi_event', $lokasi)
            ->count();
        
        if ($voucherTersedia < $jumlahPemenang) {
            return response()->json([
                'success' => false,
                'message' => "Voucher yang tersedia untuk lokasi $lokasi tidak cukup untuk jumlah doorprize"
            ]);
        }

        // Ambil voucher secara acak yang statusnya masih 0 dan sesuai lokasi
        $voucherMenang = Voucher::where('status', 0)
            ->where('lokasi_event', $lokasi)
            ->inRandomOrder()
            ->limit($jumlahPemenang)
            ->get();

        if ($voucherMenang->count() < $jumlahPemenang) {
            return response()->json([
                'success' => false,
                'message' => "Tidak cukup voucher yang tersedia untuk lokasi $lokasi"
            ]);
        }

        // Update status voucher menjadi sudah dipakai
        $voucherIds = $voucherMenang->pluck('id')->toArray();
        Voucher::whereIn('id', $voucherIds)->update([
            'status' => 1,
            'hadiah' => $doorprize->nama_doorprize
        ]);

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
            ],
            'lokasi' => $lokasi
        ]);
    }

    public function singleDoorprize($lokasi, $doorprizeId)
    {
        // Validasi lokasi event
        $lokasi = strtoupper($lokasi);
        
        // Ambil doorprize berdasarkan ID
        $doorprize = Doorprize::findOrFail($doorprizeId);
        
        return view('doorprize.single', compact('doorprize', 'lokasi'));
    }

    /**
     * Start undian untuk satu pemenang
     */
    public function startSingleUndian(Request $request, $lokasi, $doorprizeId)
    {
        $request->validate([
            'doorprize_id' => 'required|exists:doorprizes,id'
        ]);

        $lokasi = strtoupper($lokasi);
        $doorprize = Doorprize::findOrFail($doorprizeId);

        // Cek apakah masih ada voucher yang tersedia untuk lokasi ini
        $voucherTersedia = Voucher::where('status', 0)
            ->where('lokasi_event', $lokasi)
            ->count();
        
        if ($voucherTersedia < 1) {
            return response()->json([
                'success' => false,
                'message' => "Tidak ada voucher yang tersedia untuk lokasi $lokasi"
            ]);
        }

        // Ambil 1 voucher secara acak
        $voucherMenang = Voucher::where('status', 0)
            ->where('lokasi_event', $lokasi)
            ->inRandomOrder()
            ->first();

        if (!$voucherMenang) {
            return response()->json([
                'success' => false,
                'message' => "Tidak ada voucher yang tersedia untuk lokasi $lokasi"
            ]);
        }

        // Update status voucher menjadi sudah dipakai
        $voucherMenang->update([
            'status' => 1,
            'hadiah' => $doorprize->nama_doorprize
        ]);

        // Format data voucher untuk response
        $voucherData = [
            'nomor_voucher' => $voucherMenang->nomor_voucher,
            'nama_toko' => $voucherMenang->nama_toko,
            'nama_pic' => $voucherMenang->nama_pic,
            'kode_unik' => $voucherMenang->kode_unik
        ];

        return response()->json([
            'success' => true,
            'voucher' => $voucherData,
            'doorprize' => [
                'nama' => $doorprize->nama_doorprize,
                'id' => $doorprize->id
            ],
            'lokasi' => $lokasi
        ]);
    }

    /**
     * Get semua voucher untuk animasi random berdasarkan lokasi
     */
    public function getAllVouchersForAnimation($lokasi)
    {
        // Validasi lokasi event
        $lokasi = strtoupper($lokasi);
        // $validLokasi = ['JAKARTA', 'SEMARANG', 'SURABAYA', 'BANDUNG', 'MEDAN'];
        
        // if (!in_array($lokasi, $validLokasi)) {
        //     return response()->json([], 404);
        // }

        $vouchers = Voucher::where('status', 0)
            ->where('lokasi_event', $lokasi)
            ->inRandomOrder()
            ->limit(100) // Ambil 100 voucher untuk animasi
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

    public function voucherTersedia($lokasi)
    {
        // Validasi lokasi event
        $lokasi = strtoupper($lokasi);
        // $validLokasi = ['JAKARTA', 'SEMARANG', 'SURABAYA', 'BANDUNG', 'MEDAN'];
        
        // if (!in_array($lokasi, $validLokasi)) {
        //     return response()->json(['tersedia' => 0], 404);
        // }

        $tersedia = Voucher::where('status', 0)
            ->where('lokasi_event', $lokasi)
            ->count();
        
        return response()->json([
            'tersedia' => $tersedia,
            'lokasi' => $lokasi
        ]);
    }

    public function showWinnersPage($lokasi)
    {
        return view('doorprize.winners', [
            'lokasi' => $lokasi
        ]);
    }

    public function getWinners($lokasi)
    {
        try {
            $lokasi = strtoupper($lokasi);
            
            $winners = Voucher::where('status', 1)
                ->where('lokasi_event', $lokasi)
                ->orderBy('updated_at', 'desc')
                ->paginate(request('per_page', 10));

            return response()->json([
                'success' => true,
                'winners' => $winners->map(function($voucher) {
                    return [
                        'nama_toko' => $voucher->nama_toko,
                        'nama_pic' => $voucher->nama_pic,
                        'nomor_voucher' => $voucher->nomor_voucher,
                        'kode_unik' => $voucher->kode_unik,
                        'hadiah' => $voucher->hadiah,
                        'updated_at' => $voucher->updated_at
                    ];
                }),
                'total' => $winners->total(),
                'current_page' => $winners->currentPage(),
                'last_page' => $winners->lastPage(),
                'per_page' => $winners->perPage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data pemenang'
            ], 500);
        }
    }

    // Untuk mengisi card undian secara otomatis jika sudah disapatkan
    public function getWinnersByDoorprize($lokasi, $doorprizeId)
    {
        try {
            $lokasi = strtoupper($lokasi);
            
            $doorprize = Doorprize::findOrFail($doorprizeId);
            $namaDoorprize = $doorprize->nama_doorprize;

            // Ambil pemenang yang sudah ada untuk doorprize ini di lokasi ini
            $winners = Voucher::where('status', 1)
                ->where('lokasi_event', $lokasi)
                ->where('hadiah', $namaDoorprize)
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function($voucher) {
                    return [
                        'nomor_voucher' => $voucher->nomor_voucher,
                        'nama_toko' => $voucher->nama_toko,
                        'nama_pic' => $voucher->nama_pic,
                        'kode_unik' => $voucher->kode_unik,
                        'hadiah' => $voucher->hadiah,
                        'updated_at' => $voucher->updated_at
                    ];
                });

            return response()->json([
                'success' => true,
                'winners' => $winners,
                'total_winners' => $winners->count(),
                'doorprize' => [
                    'nama' => $namaDoorprize,
                    'jumlah' => $doorprize->jumlah_doorprize
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data pemenang'
            ], 500);
        }
    }
}