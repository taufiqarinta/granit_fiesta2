<?php

namespace App\Http\Controllers;

use App\Models\FormOrder;
use App\Models\MasterLokasiEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RankingCardController extends Controller
{
    public function index($lokasi)
    {
        try {
            // Validasi dan format lokasi
            $lokasiEvent = strtolower(trim($lokasi));
            
            // Cek apakah ada data untuk lokasi ini
            $exists = FormOrder::where(DB::raw('LOWER(lokasi_event)'), $lokasiEvent)
                ->exists();
                
            // if (!$exists) {
            //     return view('peringkat.not-found', compact('lokasi'));
            // }

            return view('ranking.card', compact('lokasi'));
            
        } catch (\Exception $e) {
            Log::error('Error dalam PeringkatController::index: ' . $e->getMessage());
            return view('peringkat.error');
        }
    }

    public function getData($lokasi, Request $request)
    {
        try {
            Log::info('PeringkatController: getData dipanggil', [
                'lokasi' => $lokasi,
                'ip' => $request->ip()
            ]);

            $lokasiEvent = strtolower(trim($lokasi));

            $peringkat = FormOrder::select(
                'nama_toko',
                'no_hp',
                'pic',
                'kota',
                DB::raw('SUM(total_point) as total_point_accumulated')
            )
            ->where(DB::raw('LOWER(lokasi_event)'), $lokasiEvent)
            ->groupBy('nama_toko', 'no_hp', 'pic', 'kota')
            ->orderByDesc('total_point_accumulated')
            ->limit(3) // Ambil 5 teratas saja
            ->get()
            ->map(function ($item, $index) {
                $item->peringkat = $index + 1;
                return $item;
            });

            // Log::info('Data peringkat berhasil diambil', [
            //     'total_records' => $peringkat->count(),
            //     'lokasi' => $lokasiEvent
            // ]);

            return response()->json([
                'success' => true,
                'data' => $peringkat,
                'lokasi' => $lokasiEvent
            ]);

        } catch (\Exception $e) {
            Log::error('Error dalam PeringkatController::getData: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}