<?php

namespace App\Http\Controllers;

use App\Models\FormOrder;
use App\Models\MasterLokasiEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exports\PeringkatExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class PeringkatController extends Controller
{
    public function index()
    {
        // Ambil lokasi event aktif dengan tanggal terawal
        $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
            ->orderBy('tanggal', 'asc')
            ->first();

        $lokasiEvents = MasterLokasiEvent::all();
        
        return view('peringkat.index', compact('lokasiEvents', 'defaultLokasi'));
    }

    public function getData(Request $request)
    {
        try {
            Log::info('PeringkatController: getData dipanggil', [
                'lokasi_event' => $request->lokasi_event,
                'search' => $request->search,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $query = FormOrder::select(
                'nama_toko',
                'no_hp',
                'pic',
                'kota',
                DB::raw('SUM(total_point) as total_point_accumulated')
            )
            ->groupBy('nama_toko', 'no_hp', 'pic', 'kota');

            if ($request->lokasi_event) {
                $query->where('lokasi_event', $request->lokasi_event);
                Log::info('Filter lokasi_event diterapkan: ' . $request->lokasi_event);
            }

            // Tambahkan fitur search
            if ($request->search) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nama_toko', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('pic', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('kota', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('no_hp', 'LIKE', "%{$searchTerm}%");
                });
                Log::info('Filter search diterapkan: ' . $searchTerm);
            }

            $peringkat = $query->orderByDesc('total_point_accumulated')
                // ->limit(10)
                ->get()
                ->map(function ($item, $index) {
                    $item->peringkat = $index + 1;
                    return $item;
                });

            // Log::info('Data peringkat berhasil diambil', [
            //     'total_records' => $peringkat->count(),
            //     'data' => $peringkat->toArray()
            // ]);

            return response()->json([
                'success' => true,
                'data' => $peringkat
            ]);

        } catch (\Exception $e) {
            Log::error('Error dalam PeringkatController::getData: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $lokasiEvent = $request->lokasi_event;
            $search = $request->search;

            $filename = 'peringkat-toko';
            if ($lokasiEvent) {
                $filename .= '-' . Str::slug($lokasiEvent);
            }
            $filename .= '-' . date('Y-m-d') . '.xlsx';

            return Excel::download(new PeringkatExport($lokasiEvent, $search), $filename);

        } catch (\Exception $e) {
            Log::error('Error dalam PeringkatController::exportExcel: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
}