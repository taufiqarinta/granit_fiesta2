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
    protected array $validModes = ['semua', 'toko', 'agen'];

    protected function resolveMode(string $mode): string
    {
        return in_array($mode, $this->validModes) ? $mode : 'semua';
    }

    public function index(string $mode = 'semua')
    {
        $mode = $this->resolveMode($mode);

        $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
            ->orderBy('tanggal', 'asc')
            ->first();

        $lokasiEvents = MasterLokasiEvent::all();

        return view('peringkat.index', compact('lokasiEvents', 'defaultLokasi', 'mode'));
    }

    public function getData(Request $request, string $mode = 'semua')
    {
        $mode = $this->resolveMode($mode);

        try {
            Log::info('PeringkatController: getData dipanggil', [
                'mode' => $mode,
                'lokasi_event' => $request->lokasi_event,
                'search' => $request->search,
            ]);

            $peringkat = $mode === 'agen'
                ? $this->getDataAgen($request)
                : $this->getDataToko($request, $mode);

            return response()->json([
                'success' => true,
                'data' => $peringkat,
            ]);

        } catch (\Exception $e) {
            Log::error('Error dalam PeringkatController::getData: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mode "semua" & "toko" — dikelompokkan per toko.
     * Mode "toko" mengecualikan baris yang nama_toko == nama_agen.
     */
    private function getDataToko(Request $request, string $mode)
    {
        $query = FormOrder::select(
            'nama_toko',
            'no_hp',
            'pic',
            'kota',
            DB::raw('SUM(total_point) as total_point_accumulated'),
            DB::raw('SUM(jumlah_voucher) as total_voucher_accumulated'),
            DB::raw('GROUP_CONCAT(DISTINCT kode_agen ORDER BY kode_agen SEPARATOR ", ") as kode_agen_list'),
            DB::raw('COUNT(DISTINCT kode_agen) as jumlah_agen')
        );

        if ($mode === 'toko') {
            $query->whereColumn('nama_toko', '!=', 'nama_agen');
        }

        $query->groupBy('nama_toko', 'no_hp', 'pic', 'kota');

        if ($request->lokasi_event) {
            $query->where('lokasi_event', $request->lokasi_event);
        }

        if ($request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_toko', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('kode_agen', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('pic', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('kota', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('no_hp', 'LIKE', "%{$searchTerm}%");
            });
        }

        return $query->orderByDesc('total_point_accumulated')
            ->get()
            ->map(function ($item, $index) {
                $item->peringkat = $index + 1;
                $item->kode_agen = $item->jumlah_agen > 1
                    ? $item->kode_agen_list . ' (' . $item->jumlah_agen . ' agen)'
                    : $item->kode_agen_list;
                return $item;
            });
    }

    /**
     * Mode "agen" — dikelompokkan per kode_agen + nama_agen,
     * mencakup semua toko termasuk yang nama_toko == nama_agen.
     */
    private function getDataAgen(Request $request)
    {
        $query = FormOrder::select(
            'kode_agen',
            'nama_agen',
            DB::raw('SUM(total_point) as total_point_accumulated'),
            DB::raw('SUM(jumlah_voucher) as total_voucher_accumulated'),
            DB::raw('COUNT(DISTINCT nama_toko) as jumlah_toko')
        )->groupBy('kode_agen', 'nama_agen');

        if ($request->lokasi_event) {
            $query->where('lokasi_event', $request->lokasi_event);
        }

        if ($request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode_agen', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('nama_agen', 'LIKE', "%{$searchTerm}%");
            });
        }

        return $query->orderByDesc('total_point_accumulated')
            ->get()
            ->map(function ($item, $index) {
                $item->peringkat = $index + 1;
                return $item;
            });
    }

    public function getDetail(Request $request, string $mode = 'semua')
    {
        $mode = $this->resolveMode($mode);

        try {
            if ($mode === 'agen') {
                $request->validate([
                    'kode_agen' => 'required|string',
                ]);

                $query = FormOrder::select(
                    'kode_agen', 'nama_agen', 'nama_toko', 'pic',
                    'no_hp', 'kota', 'total_point', 'jumlah_voucher', 'tanggal_order'
                )->where('kode_agen', $request->kode_agen);

            } else {
                $request->validate([
                    'nama_toko' => 'required|string',
                    'no_hp'     => 'nullable|string',
                    'pic'       => 'nullable|string',
                    'kota'      => 'nullable|string',
                ]);

                $query = FormOrder::select(
                    'kode_agen', 'nama_agen', 'nama_toko', 'pic',
                    'no_hp', 'kota', 'total_point', 'jumlah_voucher', 'tanggal_order'
                )
                ->where('nama_toko', $request->nama_toko)
                ->where('no_hp', $request->no_hp)
                ->where('pic', $request->pic)
                ->where('kota', $request->kota);

                if ($mode === 'toko') {
                    $query->whereColumn('nama_toko', '!=', 'nama_agen');
                }
            }

            if ($request->lokasi_event) {
                $query->where('lokasi_event', $request->lokasi_event);
            }

            $detail = $query->orderByDesc('total_point')->get();

            return response()->json([
                'success' => true,
                'data' => $detail,
            ]);

        } catch (\Exception $e) {
            Log::error('Error dalam PeringkatController::getDetail: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportExcel(Request $request, string $mode = 'semua')
    {
        $mode = $this->resolveMode($mode);

        try {
            $lokasiEvent = $request->lokasi_event;
            $search = $request->search;

            $filename = 'peringkat-' . $mode;
            if ($lokasiEvent) {
                $filename .= '-' . Str::slug($lokasiEvent);
            }
            $filename .= '-' . date('Y-m-d') . '.xlsx';

            return Excel::download(new PeringkatExport($lokasiEvent, $search, $mode), $filename);

        } catch (\Exception $e) {
            Log::error('Error dalam PeringkatController::exportExcel: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
}