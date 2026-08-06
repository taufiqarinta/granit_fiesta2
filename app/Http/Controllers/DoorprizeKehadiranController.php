<?php

namespace App\Http\Controllers;

use App\Models\DaftarToko;
use App\Models\DoorprizeKehadiran;
use App\Models\DoorprizeKehadiranLokasi;
use App\Models\DoorprizeKehadiranPemenang;
use Illuminate\Http\Request;

class DoorprizeKehadiranController extends Controller
{
    const BATAS_JAM_KEHADIRAN = '18:00:00';

    /**
     * Menampilkan halaman undian doorprize kehadiran (majemuk)
     */
    public function index($lokasi)
    {
        $lokasi = strtoupper($lokasi);

        // Ambil doorprize kehadiran yang aktif di lokasi ini dengan data lokasi
        $doorprizes = DoorprizeKehadiran::whereHas('lokasi', function($query) use ($lokasi) {
            $query->where('lokasi_event', $lokasi)
                  ->where('status', 1);
        })
        ->with(['lokasi' => function($query) use ($lokasi) {
            $query->where('lokasi_event', $lokasi)
                  ->where('status', 1);
        }])
        ->get();

        // Transform data untuk menambahkan jumlah dari tabel pivot
        $doorprizes = $doorprizes->map(function($doorprize) use ($lokasi) {
            $lokasiData = $doorprize->lokasi->first();
            $doorprize->jumlah_doorprize = $lokasiData ? $lokasiData->jumlah_doorprize : 0;
            return $doorprize;
        });

        return view('doorprize_kehadiran.index', compact('doorprizes', 'lokasi'));
    }

    /**
     * Start undian untuk beberapa pemenang (majemuk)
     */
    public function startUndian(Request $request, $lokasi)
    {
        $request->validate([
            'doorprize_id' => 'required|exists:doorprize_kehadiran,id'
        ]);

        $lokasi = strtoupper($lokasi);
        $doorprize = DoorprizeKehadiran::findOrFail($request->doorprize_id);

        // Ambil jumlah doorprize dari tabel pivot berdasarkan lokasi
        $doorprizeLokasi = DoorprizeKehadiranLokasi::where('doorprize_kehadiran_id', $doorprize->id)
            ->where('lokasi_event', $lokasi)
            ->first();

        if (!$doorprizeLokasi) {
            return response()->json([
                'success' => false,
                'message' => "Doorprize kehadiran tidak tersedia untuk lokasi $lokasi"
            ]);
        }

        $jumlahPemenang = $doorprizeLokasi->jumlah_doorprize;

        // Ambil kode toko yang berhak ikut undian
        $kodeTersedia = $this->eligibleKodeToko($lokasi);

        if (count($kodeTersedia) < $jumlahPemenang) {
            return response()->json([
                'success' => false,
                'message' => "Toko yang berhak ikut undian untuk lokasi $lokasi tidak cukup untuk jumlah doorprize"
            ]);
        }

        // Ambil kode toko secara acak
        $kodeMenang = collect($kodeTersedia)->shuffle()->take($jumlahPemenang)->values();

        // Ambil data toko perwakilan untuk tiap kode toko
        $tokoMenang = collect();
        foreach ($kodeMenang as $kodeToko) {
            $toko = $this->getTokoByKodeLokasi($kodeToko, $lokasi);
            if ($toko) {
                $tokoMenang->push($toko);
            }
        }

        if ($tokoMenang->count() < $jumlahPemenang) {
            return response()->json([
                'success' => false,
                'message' => "Tidak cukup toko yang berhak ikut undian untuk lokasi $lokasi"
            ]);
        }

        // Simpan pemenang ke tabel doorprize_kehadiran_pemenang
        foreach ($tokoMenang as $toko) {
            DoorprizeKehadiranPemenang::create([
                'doorprize_kehadiran_id' => $doorprize->id,
                'kode_toko' => $toko->kode_toko,
                'nama_toko' => $toko->nama_toko,
                'nama_pic' => $toko->pic,
                'kota' => $toko->kota,
                'lokasi_event' => $lokasi,
                'hadiah' => $doorprize->nama_doorprize,
                'sudah_ditukarkan' => 0,
            ]);
        }

        // Format data toko pemenang
        $tokos = $tokoMenang->map(function($toko) {
            return [
                'kode_toko' => $toko->kode_toko,
                'nama_toko' => $toko->nama_toko,
                'nama_pic' => $toko->pic,
            ];
        });

        return response()->json([
            'success' => true,
            'vouchers' => $tokos,
            'doorprize' => [
                'nama' => $doorprize->nama_doorprize,
                'jumlah' => $jumlahPemenang
            ],
            'lokasi' => $lokasi
        ]);
    }

    /**
     * Halaman undian doorprize kehadiran untuk satu hadiah tertentu (single)
     */
    public function singleDoorprize($lokasi, $doorprizeId)
    {
        $lokasi = strtoupper($lokasi);

        // Ambil doorprize dengan data lokasi
        $doorprize = DoorprizeKehadiran::with(['lokasi' => function($query) use ($lokasi) {
            $query->where('lokasi_event', $lokasi)
                  ->where('status', 1);
        }])->findOrFail($doorprizeId);

        // Tambahkan jumlah dari tabel pivot
        $lokasiData = $doorprize->lokasi->first();
        $doorprize->jumlah_doorprize = $lokasiData ? $lokasiData->jumlah_doorprize : 0;

        return view('doorprize_kehadiran.single', compact('doorprize', 'lokasi'));
    }

    public function singleDoorprizeRoda($lokasi, $doorprizeId)
    {
        $lokasi = strtoupper($lokasi);
        // Ambil doorprize dengan data lokasi
        $doorprize = DoorprizeKehadiran::with(['lokasi' => function($query) use ($lokasi) {
            $query->where('lokasi_event', $lokasi)
                  ->where('status', 1);
        }])->findOrFail($doorprizeId);
        // Tambahkan jumlah dari tabel pivot
        $lokasiData = $doorprize->lokasi->first();
        $doorprize->jumlah_doorprize = $lokasiData ? $lokasiData->jumlah_doorprize : 0;
        return view('doorprize_kehadiran.roda', compact('doorprize', 'lokasi'));
    }

    /**
     * Start undian untuk satu pemenang
     */
    public function startSingleUndian(Request $request, $lokasi, $doorprizeId)
    {
        $request->validate([
            'doorprize_id' => 'required|exists:doorprize_kehadiran,id'
        ]);

        $lokasi = strtoupper($lokasi);
        $doorprize = DoorprizeKehadiran::findOrFail($doorprizeId);

        // Ambil jumlah dari tabel pivot
        $doorprizeLokasi = DoorprizeKehadiranLokasi::where('doorprize_kehadiran_id', $doorprizeId)
            ->where('lokasi_event', $lokasi)
            ->first();

        if (!$doorprizeLokasi || $doorprizeLokasi->jumlah_doorprize < 1) {
            return response()->json([
                'success' => false,
                'message' => "Doorprize kehadiran tidak tersedia untuk lokasi $lokasi"
            ]);
        }

        // Jumlah pemenang sesuai jumlah hadiah doorprize
        $jumlahPemenang = $doorprizeLokasi->jumlah_doorprize;

        // Ambil kode toko yang berhak ikut undian
        $kodeTersedia = $this->eligibleKodeToko($lokasi);

        if (count($kodeTersedia) < $jumlahPemenang) {
            return response()->json([
                'success' => false,
                'message' => "Toko yang berhak ikut undian untuk lokasi $lokasi tidak cukup untuk jumlah doorprize"
            ]);
        }

        // Ambil kode toko secara acak sesuai jumlah pemenang
        $kodeMenang = collect($kodeTersedia)->shuffle()->take($jumlahPemenang)->values();

        // Ambil data toko perwakilan untuk tiap kode toko
        $tokoMenang = collect();
        foreach ($kodeMenang as $kodeToko) {
            $toko = $this->getTokoByKodeLokasi($kodeToko, $lokasi);
            if ($toko) {
                $tokoMenang->push($toko);
            }
        }

        if ($tokoMenang->count() < $jumlahPemenang) {
            return response()->json([
                'success' => false,
                'message' => "Tidak cukup toko yang berhak ikut undian untuk lokasi $lokasi"
            ]);
        }

        // Simpan pemenang ke tabel doorprize_kehadiran_pemenang
        foreach ($tokoMenang as $toko) {
            DoorprizeKehadiranPemenang::create([
                'doorprize_kehadiran_id' => $doorprize->id,
                'kode_toko' => $toko->kode_toko,
                'nama_toko' => $toko->nama_toko,
                'nama_pic' => $toko->pic,
                'kota' => $toko->kota,
                'lokasi_event' => $lokasi,
                'hadiah' => $doorprize->nama_doorprize,
                'sudah_ditukarkan' => 0,
            ]);
        }

        // Format data toko pemenang
        $tokos = $tokoMenang->map(function($toko) {
            return [
                'kode_toko' => $toko->kode_toko,
                'nama_toko' => $toko->nama_toko,
                'nama_pic' => $toko->pic,
            ];
        });

        return response()->json([
            'success' => true,
            'vouchers' => $tokos,
            'doorprize' => [
                'nama' => $doorprize->nama_doorprize,
                'id' => $doorprize->id
            ],
            'lokasi' => $lokasi
        ]);
    }

    /**
     * Start undian roda (selalu 1 pemenang per putaran)
     */
    public function startRoda(Request $request, $lokasi, $doorprizeId)
    {
        $request->validate([
            'doorprize_id' => 'required|exists:doorprize_kehadiran,id'
        ]);

        $lokasi = strtoupper($lokasi);
        $doorprize = DoorprizeKehadiran::findOrFail($doorprizeId);

        // Ambil jumlah dari tabel pivot
        $doorprizeLokasi = DoorprizeKehadiranLokasi::where('doorprize_kehadiran_id', $doorprizeId)
            ->where('lokasi_event', $lokasi)
            ->first();

        if (!$doorprizeLokasi || $doorprizeLokasi->jumlah_doorprize < 1) {
            return response()->json([
                'success' => false,
                'message' => "Doorprize kehadiran tidak tersedia untuk lokasi $lokasi"
            ]);
        }

        // Ambil kode toko yang berhak ikut undian
        $kodeTersedia = $this->eligibleKodeToko($lokasi);

        if (count($kodeTersedia) < 1) {
            return response()->json([
                'success' => false,
                'message' => "Tidak ada toko yang berhak ikut undian untuk lokasi $lokasi"
            ]);
        }

        // Ambil 1 kode toko secara acak
        $kodeMenang = collect($kodeTersedia)->shuffle()->first();

        $toko = $this->getTokoByKodeLokasi($kodeMenang, $lokasi);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => "Tidak ada toko yang berhak ikut undian untuk lokasi $lokasi"
            ]);
        }

        // Simpan pemenang
        DoorprizeKehadiranPemenang::create([
            'doorprize_kehadiran_id' => $doorprize->id,
            'kode_toko' => $toko->kode_toko,
            'nama_toko' => $toko->nama_toko,
            'nama_pic' => $toko->pic,
            'kota' => $toko->kota,
            'lokasi_event' => $lokasi,
            'hadiah' => $doorprize->nama_doorprize,
            'sudah_ditukarkan' => 0,
        ]);

        // Format data toko pemenang
        $tokoData = [
            'kode_toko' => $toko->kode_toko,
            'nama_toko' => $toko->nama_toko,
            'nama_pic' => $toko->pic,
        ];

        return response()->json([
            'success' => true,
            'voucher' => $tokoData,
            'doorprize' => [
                'nama' => $doorprize->nama_doorprize,
                'id' => $doorprize->id
            ],
            'lokasi' => $lokasi
        ]);
    }

    /**
     * Get semua toko untuk animasi random berdasarkan lokasi
     */
    public function getAllTokoForAnimation($lokasi)
    {
        $lokasi = strtoupper($lokasi);

        $kodeTersedia = $this->eligibleKodeToko($lokasi);

        $tokos = [];
        foreach (array_slice($kodeTersedia, 0, 100) as $kodeToko) {
            $toko = $this->getTokoByKodeLokasi($kodeToko, $lokasi);
            if ($toko) {
                $tokos[] = [
                    'kode_toko' => $toko->kode_toko,
                    'nama_toko' => $toko->nama_toko,
                    'nama_pic' => $toko->pic,
                ];
            }
        }

        return response()->json($tokos);
    }

    /**
     * Jumlah toko yang berhak ikut undian untuk lokasi tertentu
     */
    public function tokoTersedia($lokasi)
    {
        $lokasi = strtoupper($lokasi);

        $tersedia = count($this->eligibleKodeToko($lokasi));

        return response()->json([
            'tersedia' => $tersedia,
            'lokasi' => $lokasi
        ]);
    }

    /**
     * Halaman tabel toko yang berhak ikut undian doorprize kehadiran
     */
    public function showTokoBerhakPage($lokasi)
    {
        return view('doorprize_kehadiran.toko_berhak', [
            'lokasi' => $lokasi
        ]);
    }

    /**
     * Data toko yang berhak ikut undian (pagination, unik per kode_toko)
     */
    public function getTokoBerhak($lokasi)
    {
        try {
            $lokasi = strtoupper($lokasi);

            $query = DaftarToko::select('kode_toko')
                ->selectRaw('MAX(id) as max_id')
                ->where('lokasi_event', $lokasi)
                ->where('status', 1)
                ->where('hadir', 1)
                ->whereNotNull('waktu_kehadiran')
                ->where('waktu_kehadiran', '<=', self::BATAS_JAM_KEHADIRAN)
                ->where(function($query) {
                    $query->whereNull('nama_agen')
                          ->orWhereRaw('LOWER(TRIM(nama_toko)) != LOWER(TRIM(nama_agen))');
                })
                ->groupBy('kode_toko')
                ->orderByDesc('max_id');

            $paginated = $query->paginate(request('per_page', 10));

            // Daftar kode toko yang sudah pernah menang untuk lokasi ini
            $sudahMenang = DoorprizeKehadiranPemenang::where('lokasi_event', $lokasi)
                ->pluck('kode_toko')
                ->flip();

            $tokos = $paginated->getCollection()->map(function($row) use ($sudahMenang) {
                $toko = DaftarToko::find($row->max_id);

                if (!$toko) {
                    return null;
                }

                return [
                    'kode_toko' => $toko->kode_toko,
                    'nama_toko' => $toko->nama_toko,
                    'nama_pic' => $toko->pic,
                    'kota' => $toko->kota,
                    'waktu_kehadiran' => $toko->waktu_kehadiran,
                    'sudah_menang' => $sudahMenang->has($toko->kode_toko),
                ];
            })->filter()->values();

            return response()->json([
                'success' => true,
                'tokos' => $tokos,
                'total' => $paginated->total(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data toko'
            ], 500);
        }
    }

    /**
     * Untuk mengisi kartu undian secara otomatis jika sudah didapatkan
     */
    public function getWinnersByDoorprize($lokasi, $doorprizeId)
    {
        try {
            $lokasi = strtoupper($lokasi);

            $doorprize = DoorprizeKehadiran::findOrFail($doorprizeId);
            $namaDoorprize = $doorprize->nama_doorprize;

            $winners = DoorprizeKehadiranPemenang::where('lokasi_event', $lokasi)
                ->where('doorprize_kehadiran_id', $doorprizeId)
                ->orderByDesc('id')
                ->get()
                ->map(function($pemenang) {
                    return [
                        'kode_toko' => $pemenang->kode_toko,
                        'nama_toko' => $pemenang->nama_toko,
                        'nama_pic' => $pemenang->nama_pic,
                        'hadiah' => $pemenang->hadiah,
                    ];
                });

            // Ambil jumlah dari tabel pivot
            $doorprizeLokasi = DoorprizeKehadiranLokasi::where('doorprize_kehadiran_id', $doorprizeId)
                ->where('lokasi_event', $lokasi)
                ->first();

            return response()->json([
                'success' => true,
                'winners' => $winners,
                'total_winners' => $winners->count(),
                'doorprize' => [
                    'nama' => $namaDoorprize,
                    'jumlah' => $doorprizeLokasi ? $doorprizeLokasi->jumlah_doorprize : 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data pemenang'
            ], 500);
        }
    }

    /**
     * Ambil daftar kode toko unik yang berhak ikut undian untuk lokasi tertentu
     */
    private function eligibleKodeToko($lokasi)
    {
        return DaftarToko::where('lokasi_event', $lokasi)
            ->where('status', 1)
            ->where('hadir', 1)
            ->whereNotNull('waktu_kehadiran')
            ->where('waktu_kehadiran', '<=', self::BATAS_JAM_KEHADIRAN)
            ->where(function($query) {
                $query->whereNull('nama_agen')
                      ->orWhereRaw('LOWER(TRIM(nama_toko)) != LOWER(TRIM(nama_agen))');
            })
            ->whereNotIn('kode_toko', function($query) use ($lokasi) {
                $query->select('kode_toko')
                    ->from('doorprize_kehadiran_pemenang')
                    ->where('lokasi_event', $lokasi);
            })
            ->distinct()
            ->pluck('kode_toko')
            ->toArray();
    }

    /**
     * Ambil data toko perwakilan berdasarkan kode_toko + lokasi_event
     */
    private function getTokoByKodeLokasi($kodeToko, $lokasi)
    {
        return DaftarToko::where('kode_toko', $kodeToko)
            ->where('lokasi_event', $lokasi)
            ->orderByDesc('id')
            ->first();
    }
}
