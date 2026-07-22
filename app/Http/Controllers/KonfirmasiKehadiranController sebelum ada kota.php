<?php

namespace App\Http\Controllers;

use App\Models\DaftarTokoBelumRSVP;
use App\Models\MasterLokasiEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KonfirmasiKehadiranController extends Controller
{
    /**
     * Tampilkan halaman konfirmasi kehadiran
     */
    public function index()
    {
        $lokasiEvents = MasterLokasiEvent::active()
            ->orderBy('nama_lokasi')
            ->get();

        return view('konfirmasi-kehadiran.index', compact('lokasiEvents'));
    }

    /**
     * Select2 AJAX search nama toko berdasarkan lokasi event terpilih
     * Group by kode_toko karena 1 toko bisa punya banyak baris (per agen)
     */
    public function searchToko(Request $request)
    {
        $request->validate([
            'lokasi_event' => 'required|string',
        ]);

        $q = trim((string) $request->get('q', ''));

        $tokoList = DaftarTokoBelumRSVP::select('kode_toko', 'nama_toko', 'alamat')
            ->where('lokasi_event', $request->lokasi_event)
            ->where('konfirmasi_kehadiran', 0)
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nama_toko', 'LIKE', '%' . $q . '%');
            })
            ->groupBy('kode_toko', 'nama_toko', 'alamat')
            ->orderBy('nama_toko')
            ->limit(30)
            ->get();

        $results = $tokoList->map(function ($toko) {
            return [
                'id'   => $toko->kode_toko,
                'text' => $toko->nama_toko . ' — ' . Str::limit($toko->alamat, 40),
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Ambil detail alamat lengkap toko yang dipilih
     */
    public function getTokoDetail($kode_toko, Request $request)
    {
        $request->validate([
            'lokasi_event' => 'required|string',
        ]);

        $toko = DaftarTokoBelumRSVP::where('kode_toko', $kode_toko)
            ->where('lokasi_event', $request->lokasi_event)
            ->where('konfirmasi_kehadiran', 0)
            ->first();

        if (! $toko) {
            return response()->json([
                'success' => false,
                'message' => 'Data toko tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kode_toko' => $toko->kode_toko,
                'nama_toko' => $toko->nama_toko,
                'alamat'    => $toko->alamat,
            ],
        ]);
    }

    /**
     * Simpan konfirmasi kehadiran.
     * Update dilakukan majemuk berdasarkan kode_toko + lokasi_event,
     * karena 1 toko fisik bisa terpisah jadi beberapa baris per agen.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'lokasi_event' => 'required|string',
            'kode_toko'    => 'required|string',
            'pic'          => 'required|string|max:255',
        ]);

        $updated = DaftarTokoBelumRSVP::where('kode_toko', $validated['kode_toko'])
            ->where('lokasi_event', $validated['lokasi_event'])
            ->where('konfirmasi_kehadiran', 0)
            ->update([
                'pic'                  => strtoupper($validated['pic']),
                'konfirmasi_kehadiran' => 1,
            ]);

        if ($updated === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Data toko tidak ditemukan atau sudah dikonfirmasi sebelumnya',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Konfirmasi kehadiran berhasil disimpan',
            'rows_updated' => $updated,
        ]);
    }
}