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
    public function index($lokasi)
    {
        $parts = explode('.', $lokasi);
        if (count($parts) !== 2) {
            abort(404);
        }

        [$b64, $hmac] = $parts;
        $expected = hash_hmac('sha256', $b64, config('app.key'));
        if (!hash_equals($expected, $hmac)) {
            abort(404);
        }

        $lokasiEvent = base64_url_decode($b64);
        if ($lokasiEvent === false) {
            abort(404);
        }

        $lokasiData = MasterLokasiEvent::active()
            ->where('nama_lokasi', $lokasiEvent)
            ->first();

        if (!$lokasiData) {
            abort(404);
        }

        return view('konfirmasi-kehadiran.index', compact('lokasiEvent'));
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

        $tokoList = DaftarTokoBelumRSVP::select('kode_toko', 'nama_toko', 'kota', 'alamat')
            ->where('lokasi_event', $request->lokasi_event)
            ->where('konfirmasi_kehadiran', 0)
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nama_toko', 'LIKE', '%' . $q . '%');
            })
            ->groupBy('kode_toko', 'nama_toko', 'kota', 'alamat')
            ->orderBy('nama_toko')
            ->limit(30)
            ->get();

        $results = $tokoList->map(function ($toko) {
            return [
                'id'   => $toko->kode_toko,
                'text' => $toko->nama_toko . ' — ' . $toko->kota . ' — ' . Str::limit($toko->alamat, 40),
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Ambil detail kota & alamat lengkap toko yang dipilih
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
                'kota'      => $toko->kota,
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
            'nomor_pic'    => 'nullable|string|max:20',
        ]);

        $dataUpdate = [
            'pic'                  => strtoupper($validated['pic']),
            'konfirmasi_kehadiran' => 1,
        ];

        // Nomor PIC opsional: hanya update kalau user mengisi,
        // supaya data existing di DB tidak tertimpa kosong
        if ($request->filled('nomor_pic')) {
            $dataUpdate['nomor_pic'] = $validated['nomor_pic'];
        }

        $updated = DaftarTokoBelumRSVP::where('kode_toko', $validated['kode_toko'])
            ->where('lokasi_event', $validated['lokasi_event'])
            ->where('konfirmasi_kehadiran', 0)
            ->update($dataUpdate);

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

    /**
     * Tampilkan halaman admin untuk generate link konfirmasi per lokasi event
     */
    public function generateLinks()
    {
        if (auth()->user()->department !== 'IT') {
            abort(403);
        }

        $lokasiList = MasterLokasiEvent::active()
            ->orderBy('nama_lokasi')
            ->get();

        $links = $lokasiList->map(function ($lokasi) {
            return [
                'lokasi' => $lokasi,
                'url'    => generateLinkKonfirmasi($lokasi->nama_lokasi),
            ];
        });

        return view('admin.link-konfirmasi.index', compact('links'));
    }
}