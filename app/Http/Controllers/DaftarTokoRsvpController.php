<?php

namespace App\Http\Controllers;

use App\Exports\TokoRsvpExport;
use App\Exports\TokoRsvpMergeExport;
use App\Models\DaftarTokoBelumRSVP;
use App\Models\MasterLokasiEvent;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;

class DaftarTokoRsvpController extends Controller
{
    public function index(Request $request)
    {
        $search      = trim((string) $request->get('search', ''));
        $status      = $request->get('status', 'all');
        $lokasiEvent = $request->get('lokasi_event');
        $page        = max(1, (int) $request->get('page', 1));
        $perPage     = 15;

        $allRows   = $this->getFilteredRows($search, $status, $lokasiEvent);
        $allGroups = $allRows->groupBy('kode_toko');

        $totalToko  = $allGroups->count();
        $totalSudah = $allGroups->filter(fn ($rows) => (int) $rows->first()->konfirmasi_kehadiran === 1)->count();
        $totalBelum = $totalToko - $totalSudah;

        $kodeTokoKeys = $allGroups->keys();
        $pagedKeys    = $kodeTokoKeys->slice(($page - 1) * $perPage, $perPage)->values();
        $pagedGroups  = $pagedKeys->mapWithKeys(fn ($kode) => [$kode => $allGroups->get($kode)]);

        $paginator = new LengthAwarePaginator(
            $pagedGroups,
            $totalToko,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $lokasiEvents = MasterLokasiEvent::active()->orderBy('nama_lokasi')->get();

        return view('toko-rsvp.index', [
            'groups'       => $pagedGroups,
            'paginator'    => $paginator,
            'totalToko'    => $totalToko,
            'totalSudah'   => $totalSudah,
            'totalBelum'   => $totalBelum,
            'lokasiEvents' => $lokasiEvents,
            'search'       => $search,
            'status'       => $status,
            'lokasiEvent'  => $lokasiEvent,
        ]);
    }

    /**
     * Export Excel - versi merge (rowspan per toko, sesuai tampilan tabel)
     */
    public function exportMerge(Request $request)
    {
        $rows = $this->getFilteredRows(
            trim((string) $request->get('search', '')),
            $request->get('status', 'all'),
            $request->get('lokasi_event')
        );

        $groups = $rows->groupBy('kode_toko');

        return Excel::download(
            new TokoRsvpMergeExport($groups),
            'data-rsvp-toko-merge-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    /**
     * Export Excel - versi flat/no-merge (1 baris per data agen)
     */
    public function exportFlat(Request $request)
    {
        $rows = $this->getFilteredRows(
            trim((string) $request->get('search', '')),
            $request->get('status', 'all'),
            $request->get('lokasi_event')
        );

        return Excel::download(
            new TokoRsvpExport($rows),
            'data-rsvp-toko-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    /**
     * Ambil semua baris sesuai filter search/status/lokasi_event, tanpa pagination
     */
    private function getFilteredRows(string $search, string $status, ?string $lokasiEvent)
    {
        $query = DaftarTokoBelumRSVP::query();

        if ($status === 'sudah') {
            $query->where('konfirmasi_kehadiran', 1);
        } elseif ($status === 'belum') {
            $query->where('konfirmasi_kehadiran', 0);
        }

        if (!empty($lokasiEvent)) {
            $query->where('lokasi_event', $lokasiEvent);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_toko', 'LIKE', "%{$search}%")
                  ->orWhere('kode_toko', 'LIKE', "%{$search}%")
                  ->orWhere('kode_agen', 'LIKE', "%{$search}%")
                  ->orWhere('nama_agen', 'LIKE', "%{$search}%")
                  ->orWhere('pic', 'LIKE', "%{$search}%")
                  ->orWhere('kota', 'LIKE', "%{$search}%");
            });
        }

        return $query->orderBy('kode_toko')->orderBy('kode_agen')->get();
    }
}