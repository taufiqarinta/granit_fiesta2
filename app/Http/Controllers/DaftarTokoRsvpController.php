<?php

namespace App\Http\Controllers;

use App\Models\DaftarTokoBelumRSVP;
use App\Models\MasterLokasiEvent;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DaftarTokoRsvpController extends Controller
{
    public function index(Request $request)
    {
        $search      = trim((string) $request->get('search', ''));
        $status      = $request->get('status', 'all');
        $lokasiEvent = $request->get('lokasi_event');
        $page        = max(1, (int) $request->get('page', 1));
        $perPage     = 15;

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

        // Ambil SEMUA baris yang cocok filter, lalu group manual per kode_toko
        // (menghindari kombinasi select()->groupBy()->paginate() yang bikin filter search tidak konsisten)
        $allRows   = $query->orderBy('kode_toko')->orderBy('kode_agen')->get();
        $allGroups = $allRows->groupBy('kode_toko');

        // Summary dihitung per kode_toko (1 toko = 1 hitungan, bukan per baris)
        $totalToko  = $allGroups->count();
        $totalSudah = $allGroups->filter(fn ($rows) => (int) $rows->first()->konfirmasi_kehadiran === 1)->count();
        $totalBelum = $totalToko - $totalSudah;

        // Paginasi manual di level toko (bukan di level baris)
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
}