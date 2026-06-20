<?php

namespace App\Http\Controllers;

use App\Models\SurveyAgen;
use App\Models\SurveyAgenDetail;
use App\Models\DaftarAgen;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Browser;

class SurveyAgenController extends Controller
{
    /**
     * Tampilkan form survey agen (tanpa login)
     */
    public function create()
    {
        return view('survey-agen.form');
    }

    /**
     * Lookup agen berdasarkan kode agen
     */
    public function lookupAgen(Request $request)
    {
        $kodeAgen = strtoupper(trim((string) $request->get('kode_agen')));

        if ($kodeAgen === '') {
            return response()->json([
                'success' => false,
                'message' => 'Kode agen wajib diisi',
            ], 422);
        }

        $agen = DaftarAgen::where('kode_agen', $kodeAgen)->first();

        if (! $agen) {
            return response()->json([
                'success' => false,
                'message' => 'Kode agen tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kode_agen' => $agen->kode_agen,
                'nama_agen' => $agen->nama_agen,
            ],
        ]);
    }

    /**
     * Simpan data survey agen
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_agen' => 'required|string|max:255',
            'kode_agen' => 'required|string|max:50',
            // sales fields (single submission per sales)
            'nama_sales' => 'required|string|max:255',
            'no_hp' => 'required|string|max:50',
            'area' => 'required|string|max:255',
            'top_10_pareto' => 'required|string',
            'target_penjualan' => 'required|numeric',
            'brands' => 'required|string|max:255',
            'keliling_luar_kota' => 'required|string',
            'toko_butuh_support' => 'required|string',
            'saran_kobin' => 'required|string',
        ], [
            'nama_agen.required' => 'Nama agen wajib diisi',
            'kode_agen.required' => 'Kode agen wajib dipilih',
            'nama_sales.required' => 'Nama sales wajib diisi',
            'no_hp.required' => 'No HP wajib diisi',
            'area.required' => 'Area wajib diisi',
            'top_10_pareto.required' => 'Top 10 Pareto wajib diisi',
            'target_penjualan.required' => 'Target penjualan wajib diisi',
            'brands.required' => 'Brand yang dipegang wajib diisi',
            'keliling_luar_kota.required' => 'Informasi keliling luar kota wajib diisi',
            'toko_butuh_support.required' => 'Toko yang perlu support wajib diisi',
            'saran_kobin.required' => 'Saran untuk Kobin Tiles wajib diisi',
        ]);

        try {
            $survey = null;

            for ($attempt = 0; $attempt < 5; $attempt++) {
                $kodeSurvey = $this->generateUniqueKodeSurvey();

                try {
                    $survey = DB::transaction(function () use ($validated, $kodeSurvey) {
                        $survey = SurveyAgen::create([
                            'kode_survey' => $kodeSurvey,
                            'nama_agen' => strtoupper(trim($validated['nama_agen'])),
                            'kode_agen' => strtoupper(trim($validated['kode_agen'])),
                        ]);

                        SurveyAgenDetail::create([
                            'survey_agen_id' => $survey->id,
                            'nama_sales' => strtoupper(trim($validated['nama_sales'])),
                            'no_hp' => trim($validated['no_hp']),
                            'area' => strtoupper(trim($validated['area'])),
                            'top_10_pareto' => isset($validated['top_10_pareto']) ? strtoupper(trim($validated['top_10_pareto'])) : null,
                            'target_penjualan' => $validated['target_penjualan'] ?? null,
                            'brands' => isset($validated['brands']) ? strtoupper(trim($validated['brands'])) : null,
                            'keliling_luar_kota' => isset($validated['keliling_luar_kota']) ? strtoupper(trim($validated['keliling_luar_kota'])) : null,
                            'toko_butuh_support' => isset($validated['toko_butuh_support']) ? strtoupper(trim($validated['toko_butuh_support'])) : null,
                            'saran_kobin' => isset($validated['saran_kobin']) ? strtoupper(trim($validated['saran_kobin'])) : null,
                        ]);

                        return $survey;
                    });

                    break;
                } catch (QueryException $exception) {
                    if (! $this->isDuplicateKeyException($exception) || $attempt === 4) {
                        throw $exception;
                    }
                }
            }

            if (! $survey) {
                throw new \RuntimeException('Gagal menyimpan survey. Silakan coba lagi.');
            }

            // Log aktivitas (jika ada)
            try {
                LogAktivitas::create([
                    'user_id' => auth()->id() ?? null,
                    'username' => auth()->check() ? auth()->user()->name : ($validated['nama_sales'] ?? 'guest'),
                    'aksi' => 'Tambah',
                    'fitur' => 'Survey Agen (Tanpa Login)',
                    'deskripsi' => "Menyimpan survey {$kodeSurvey} untuk agen " . ($validated['nama_agen'] ?? ''),
                    'ip_address' => $request->ip(),
                    'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {
                // jangan ganggu flow utama jika logging gagal
                \Log::error('Gagal menyimpan log aktivitas survey store: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Data survey berhasil disimpan',
                'kode_survey' => $kodeSurvey,
                'survey_id' => $survey->id,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate kode survey yang unik.
     */
    protected function generateUniqueKodeSurvey(): string
    {
        for ($attempt = 0; $attempt < 25; $attempt++) {
            $kodeUnik = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            $kodeSurvey = "SURVEY-{$kodeUnik}";

            if (! SurveyAgen::where('kode_survey', $kodeSurvey)->exists()) {
                return $kodeSurvey;
            }
        }

        throw new \RuntimeException('Gagal membuat kode survey unik. Silakan coba lagi.');
    }

    /**
     * Deteksi error duplicate key dari database.
     */
    protected function isDuplicateKeyException(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        $driverCode = $exception->errorInfo[1] ?? null;
        $message = strtolower($exception->getMessage());

        return $sqlState === '23000'
            || $driverCode === 1062
            || str_contains($message, 'duplicate entry');
    }

    /**
     * Tampilkan hasil survey dengan QR code
     */
    public function show($kodeSurvey)
    {
        $survey = SurveyAgen::where('kode_survey', $kodeSurvey)->with('details')->firstOrFail();
        // Generate QR code sebagai base64
        $qrCodeSvg = QrCode::format('svg')->size(200)->generate($survey->kode_survey);
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
        return view('survey-agen.success', compact('survey', 'qrCodeBase64'));
    }

    /**
     * Download QR code dan nomor survey sebagai image
     */
    public function downloadImage($kodeSurvey)
    {
        $survey = SurveyAgen::where('kode_survey', $kodeSurvey)->with('details')->firstOrFail();
        
        $qrCodeSvg = QrCode::format('svg')->size(320)->generate($survey->kode_survey);
        $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        $surveyName = e(mb_strimwidth($survey->nama_agen, 0, 60, ''));
        $surveyCode = e($survey->kode_survey);
        $createdAt = e($survey->created_at->format('d/m/Y H:i'));

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="1000" viewBox="0 0 800 1000">
    <rect width="800" height="1000" fill="#ffffff"/>
    <rect x="0" y="0" width="800" height="10" fill="#4361ee"/>
    <text x="50" y="70" fill="#1e293b" font-family="Arial, sans-serif" font-size="34" font-weight="700">SURVEY AGEN</text>
    <text x="50" y="120" fill="#1e293b" font-family="Arial, sans-serif" font-size="22">Agen: {$surveyName}</text>
    <image href="{$qrDataUri}" x="250" y="200" width="300" height="300"/>
    <text x="50" y="620" fill="#1e293b" font-family="Arial, sans-serif" font-size="28" font-weight="700">NOMOR SURVEY:</text>
    <text x="50" y="670" fill="#4361ee" font-family="Arial, sans-serif" font-size="34" font-weight="700">{$surveyCode}</text>
    <text x="50" y="730" fill="#64748b" font-family="Arial, sans-serif" font-size="22">Tanggal: {$createdAt}</text>
    <text x="50" y="900" fill="#64748b" font-family="Arial, sans-serif" font-size="18"></text>
</svg>
SVG;

        $filename = 'survey-agen-' . $survey->kode_survey . '.svg';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    /**
     * Tampilkan tabel daftar survey agen (memerlukan login)
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $surveys = SurveyAgen::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('kode_survey', 'like', '%' . $search . '%')
                        ->orWhere('kode_agen', 'like', '%' . $search . '%')
                        ->orWhere('nama_agen', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('survey-agen.index', compact('surveys', 'search'));
    }

    /**
     * Tampilkan halaman scan QR atau input kode survey (memerlukan login)
     */
    public function scanQrPage()
    {
        return view('survey-agen.scan-qr');
    }

    /**
     * Cari survey berdasarkan kode survey (untuk scan QR)
     */
    public function searchSurvey(Request $request)
    {
        $kodeSurvey = strtoupper(trim((string) $request->get('kode_survey')));

        if ($kodeSurvey === '') {
            return response()->json([
                'success' => false,
                'message' => 'Kode survey wajib diisi',
            ], 422);
        }

        $survey = SurveyAgen::where('kode_survey', $kodeSurvey)
            ->with('details')
            ->first();

        if (! $survey) {
            return response()->json([
                'success' => false,
                'message' => 'Kode survey tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $survey->id,
                'kode_survey' => $survey->kode_survey,
                'nama_agen' => $survey->nama_agen,
                'kode_agen' => $survey->kode_agen,
                'first_sales_name' => optional($survey->details->first())->nama_sales,
                'first_sales_no_hp' => optional($survey->details->first())->no_hp,
                'status_klaim_hadiah' => $survey->status_klaim_hadiah,
            ],
        ]);
    }

    /**
     * Tampilkan detail survey (memerlukan login)
     */
    public function detail($kodeSurvey)
    {
        $survey = SurveyAgen::where('kode_survey', $kodeSurvey)
            ->with('details')
            ->firstOrFail();

        // Generate QR code sebagai base64
        $qrCodeSvg = QrCode::format('svg')->size(200)->generate($survey->kode_survey);
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        return view('survey-agen.detail', compact('survey', 'qrCodeBase64'));
    }

    /**
     * Klaim hadiah untuk survey agen
     */
    public function claimReward(Request $request)
    {
        $kodeSurvey = strtoupper(trim((string) $request->get('kode_survey')));

        if ($kodeSurvey === '') {
            return response()->json([
                'success' => false,
                'message' => 'Kode survey wajib diisi',
            ], 422);
        }

        $survey = SurveyAgen::where('kode_survey', $kodeSurvey)->first();

        if (! $survey) {
            return response()->json([
                'success' => false,
                'message' => 'Kode survey tidak ditemukan',
            ], 404);
        }

        if ($survey->status_klaim_hadiah === 1) {
            return response()->json([
                'success' => false,
                'message' => 'Hadiah untuk survey ini sudah diklaim',
            ], 422);
        }

        $survey->update(['status_klaim_hadiah' => 1]);

        try {
            LogAktivitas::create([
                'user_id' => auth()->id() ?? null,
                'username' => auth()->check() ? auth()->user()->name : 'guest',
                'aksi' => 'Klaim Hadiah',
                'fitur' => 'Survey Agen',
                'deskripsi' => "Klaim hadiah untuk survey {$survey->kode_survey}",
                'ip_address' => $request->ip(),
                'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan log aktivitas claimReward: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Hadiah berhasil diklaim',
            'data' => [
                'id' => $survey->id,
                'kode_survey' => $survey->kode_survey,
                'status_klaim_hadiah' => $survey->status_klaim_hadiah,
            ],
        ]);
    }
}

