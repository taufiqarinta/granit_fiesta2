<?php

namespace App\Http\Controllers;

use App\Models\DaftarToko;
use App\Models\DaftarAgen;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Exports\DaftarTokoExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TokoTrackingExport;
use App\Models\FormOrder;
use App\Models\Voucher;
use App\Models\LogAktivitas;
use App\Models\MasterLokasiEvent;
use App\Models\Wilayah;
use Browser;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DaftarTokoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $lokasiEvent = $request->get('lokasi_event');
        
        $query = DaftarToko::query();

        if(auth()->user()->role_as == 0){
            $query->where('kode_agen', auth()->user()->id_customer);
        }

        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_toko', 'like', "%{$search}%")
                ->orWhere('kode_toko', 'like', "%{$search}%")
                ->orWhere('pic', 'like', "%{$search}%")
                ->orWhere('lokasi_event', 'like', "%{$search}%")
                ->orWhere('kota', 'like', "%{$search}%");
            });
        }
        
        // Get lokasi events from MasterLokasiEvent
        $lokasiEvents = MasterLokasiEvent::all();
        
        $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
            ->orderBy('tanggal', 'asc')
            ->first();
        
        // Filter by lokasi event - hanya jika ada nilai dan bukan 'semua'
        if ($lokasiEvent && $lokasiEvent != 'semua') {
            $query->where('lokasi_event', $lokasiEvent);
        } 
        // Jika tidak ada filter lokasi dan halaman pertama kali dibuka, gunakan default lokasi
        elseif (!$lokasiEvent && $defaultLokasi) {
            $query->where('lokasi_event', $defaultLokasi->nama_lokasi);
            $lokasiEvent = $defaultLokasi->nama_lokasi; // Set untuk selected option
        }
        
        // Load dengan relasi atau join ke tabel wilayah
        $tokos = $query->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->appends($request->query());
        
        // Ambil nama provinsi dan kota dari tabel wilayah
        $provinsiCodes = $tokos->pluck('provinsi')->unique()->filter();
        $kotaCodes = $tokos->pluck('kota')->unique()->filter();
        
        $wilayahData = Wilayah::whereIn('kode', $provinsiCodes)
                            ->orWhereIn('kode', $kotaCodes)
                            ->get()
                            ->keyBy('kode');
        
        // Tambahkan nama provinsi dan kota ke setiap toko
        $tokos->getCollection()->transform(function ($toko) use ($wilayahData) {
            $toko->provinsi_name = $wilayahData[$toko->provinsi]->nama ?? $toko->provinsi;
            $toko->kota_name = $wilayahData[$toko->kota]->nama ?? $toko->kota;
            return $toko;
        });
        
        return view('daftartoko.index', compact('tokos', 'search', 'lokasiEvents', 'lokasiEvent', 'defaultLokasi'));
    }

    public function export(Request $request)
    {
        $search = $request->query('search');
        $lokasiEvent = $request->query('lokasi_event');
        
        return Excel::download(new DaftarTokoExport($search, $lokasiEvent), 'daftar-toko-' . date('Y-m-d') . '.xlsx');
    }

    public function rekapanGabungan(Request $request)
    {
        $search = $request->query('search');
        $tipe = $request->query('tipe', 'semua');
        $sumberData = $request->query('sumber_data', 'semua');

        $lokasiEvents = MasterLokasiEvent::all();
        
        // Jika tidak ada filter lokasi dan halaman pertama kali dibuka, gunakan default lokasi
        $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
            ->orderBy('tanggal', 'asc')
            ->first();
        
        $lokasiEvent = $request->query('lokasi_event');
        
        if ((!$request->has('lokasi_event') || $lokasiEvent == '') && $defaultLokasi) {
            $lokasiEvent = $defaultLokasi->nama_lokasi;
        }

        $queryToko = DaftarToko::where('status', 1)->orderBy('id', 'asc');
        $queryAgen = DaftarAgen::where('status', 1)->orderBy('id', 'asc');
        $queryFormOrder = FormOrder::query();

        if (auth()->user()->role_as == 0) {
            $kodeAgenUser = auth()->user()->id_customer;
            $queryToko->where('kode_agen', $kodeAgenUser);
            $queryAgen->where('kode_agen', $kodeAgenUser);
            $queryFormOrder->where('kode_agen', $kodeAgenUser);
        }

        // Filter by lokasi event - hanya jika ada nilai dan bukan 'semua'
        if ($lokasiEvent && $lokasiEvent != 'semua') {
            $queryToko->where('lokasi_event', $lokasiEvent);
            $queryAgen->where('lokasi_event', $lokasiEvent);
            $queryFormOrder->where('lokasi_event', $lokasiEvent);
        }

        // Jumlah form order (record) sesuai filter role & lokasi — dipakai untuk summary "Form Order"
        $totalFormOrderRecords = (clone $queryFormOrder)->count();

        // Daftar agen untuk dropdown filter kode agen
        $agenFilterQuery = DaftarAgen::select('kode_agen', 'nama_agen')
            ->whereNotNull('kode_agen')
            ->where('kode_agen', '!=', '');

        if (auth()->user()->role_as == 0) {
            $agenFilterQuery->where('kode_agen', auth()->user()->id_customer);
        }

        $daftarAgenFilter = $agenFilterQuery->distinct()->orderBy('nama_agen')->get();

        $seenTokoKey = [];
        $dataToko = $queryToko->get()->filter(function ($toko) use (&$seenTokoKey) {
            $key = mb_strtolower(implode('|', [
                trim((string) $toko->nama_toko),
                trim((string) ($toko->pic ?? '')),
                trim((string) ($toko->nomor_pic ?? '')),
                trim((string) ($toko->kota ?? '')),
                trim((string) ($toko->lokasi_event ?? '')),
                trim((string) ($toko->kode_agen ?? '')),
            ]));
            if (isset($seenTokoKey[$key])) {
                return false;
            }
            $seenTokoKey[$key] = true;
            return true;
        })->values();
        $dataAgen = $queryAgen->get();

        $uniqueOrders = $queryFormOrder
            ->select([
                'nama_toko',
                'pic',
                'kota',
                'no_hp',
                'lokasi_event',
                'kode_agen',
            ])
            ->distinct()
            ->get();

        // Map nama_agen dari form_order untuk fallback ketika kode_agen tidak ditemukan di daftar_agen.
        // Key dibuat identik dengan kombinasi unik order agar tidak menambah jumlah baris.
        $orderAgenNameByKey = [];
        $orderAgenNameByKode = [];
        $formOrderNameRows = (clone $queryFormOrder)
            ->select([
                'nama_toko',
                'pic',
                'kota',
                'no_hp',
                'lokasi_event',
                'kode_agen',
                'nama_agen',
            ])
            ->whereNotNull('nama_agen')
            ->where('nama_agen', '!=', '')
            ->get();

        foreach ($formOrderNameRows as $fo) {
            $key = implode('|', [
                (string) $fo->nama_toko,
                (string) $fo->pic,
                (string) $fo->kota,
                (string) $fo->no_hp,
                (string) $fo->lokasi_event,
                (string) $fo->kode_agen,
            ]);

            if (!isset($orderAgenNameByKey[$key])) {
                $orderAgenNameByKey[$key] = $fo->nama_agen;
            }

            $kodeAgen = (string) $fo->kode_agen;
            if ($kodeAgen !== '' && !isset($orderAgenNameByKode[$kodeAgen])) {
                $orderAgenNameByKode[$kodeAgen] = $fo->nama_agen;
            }
        }

        $allData = [];

        foreach ($dataToko as $toko) {
            $allData[] = [
                'type' => 'TOKO',
                'source' => 'DAFTAR_TOKO',
                'db_id' => $toko->id, 
                'kode_toko' => $toko->kode_toko,
                'nama_toko' => $toko->nama_toko,
                'nama_agen' => $toko->nama_agen,
                'kode_agen' => $toko->kode_agen,
                'pic' => $toko->pic,
                'kota' => $toko->kota,
                'no_hp' => $toko->nomor_pic,
                'email' => $toko->email ?? '',
                'lokasi_event' => $toko->lokasi_event,
                'hadir' => (int) ($toko->hadir ?? 0),
                'jumlah_kehadiran' => (int) ($toko->jumlah_kehadiran ?? 0),
                'hotel' => $toko->hotel,
                'nomor_kamar_hotel' => $toko->nomor_kamar_hotel,
                'jumlah_orang_menginap' => $toko->jumlah_orang_menginap,
                'checkin' => $toko->checkin,
                'doorprize' => $this->getDoorprize($toko->nama_toko, $toko->pic, $toko->nomor_pic, $toko->lokasi_event),
            ];
        }

        foreach ($dataAgen as $agen) {
            $allData[] = [
                'type' => 'AGEN',
                'source' => 'DAFTAR_AGEN',
                'db_id' => 'agen_' . $agen->id,
                'kode_toko' => '-',
                'nama_toko' => $agen->nama_agen,
                'nama_agen' => $agen->nama_agen,
                'kode_agen' => $agen->kode_agen,
                'pic' => $agen->pic,
                'kota' => $agen->kota,
                'no_hp' => $agen->nomor_pic,
                'email' => $agen->email ?? '',
                'lokasi_event' => $agen->lokasi_event,
                'hadir' => (int) ($agen->hadir ?? 0),
                'jumlah_kehadiran' => (int) ($agen->jumlah_kehadiran ?? 0),
                'hotel' => $agen->hotel,
                'nomor_kamar_hotel' => $agen->nomor_kamar_hotel,
                'jumlah_orang_menginap' => $agen->jumlah_orang_menginap,
                'checkin' => $agen->checkin,
                'doorprize' => '-',
            ];
        }

        foreach ($uniqueOrders as $order) {
            $existsInToko = $dataToko->first(function ($toko) use ($order) {
                // Dibandingkan case-insensitive + trim, selaras dengan collation MySQL (utf8mb4_general_ci)
                // yang dipakai oleh calculateTotalOrder/countFormOrder, supaya tidak ada overlap double count.
                return mb_strtolower(trim((string) $toko->nama_toko)) == mb_strtolower(trim((string) $order->nama_toko))
                    && mb_strtolower(trim((string) ($toko->pic ?? ''))) == mb_strtolower(trim((string) ($order->pic ?? '')))
                    && mb_strtolower(trim((string) ($toko->kota ?? ''))) == mb_strtolower(trim((string) ($order->kota ?? '')))
                    && mb_strtolower(trim((string) ($toko->nomor_pic ?? ''))) == mb_strtolower(trim((string) ($order->no_hp ?? '')))
                    && mb_strtolower(trim((string) ($toko->lokasi_event ?? ''))) == mb_strtolower(trim((string) ($order->lokasi_event ?? '')))
                    && mb_strtolower(trim((string) ($toko->kode_agen ?? ''))) == mb_strtolower(trim((string) ($order->kode_agen ?? '')));
            });

            if ($existsInToko) {
                continue;
            }

            $similarToko = $dataToko->first(function ($toko) use ($order) {
                return mb_strtolower(trim((string) $toko->nama_toko)) == mb_strtolower(trim((string) $order->nama_toko))
                    && mb_strtolower(trim((string) ($toko->pic ?? ''))) == mb_strtolower(trim((string) ($order->pic ?? '')))
                    && mb_strtolower(trim((string) ($toko->kota ?? ''))) == mb_strtolower(trim((string) ($order->kota ?? '')))
                    && mb_strtolower(trim((string) ($toko->nomor_pic ?? ''))) == mb_strtolower(trim((string) ($order->no_hp ?? '')));
            });

            $namaAgen = '';
            if ($order->kode_agen) {
                $agen = $dataAgen->firstWhere('kode_agen', $order->kode_agen);
                if ($agen) {
                    $namaAgen = $agen->nama_agen;
                } else {
                    $orderKey = implode('|', [
                        (string) $order->nama_toko,
                        (string) $order->pic,
                        (string) $order->kota,
                        (string) $order->no_hp,
                        (string) $order->lokasi_event,
                        (string) $order->kode_agen,
                    ]);

                    $namaAgen = $orderAgenNameByKey[$orderKey]
                        ?? $orderAgenNameByKode[(string) $order->kode_agen]
                        ?? '';
                }
            }

            $allData[] = [
                'type' => 'TOKO',
                'source' => 'FORM_ORDER',
                'db_id' => $similarToko->id ?? null,
                'kode_toko' => $similarToko->kode_toko ?? '-',
                'nama_toko' => $order->nama_toko,
                'nama_agen' => $namaAgen,
                'kode_agen' => $order->kode_agen,
                'pic' => $order->pic,
                'kota' => $order->kota,
                'no_hp' => $order->no_hp,
                'email' => '',
                'lokasi_event' => $order->lokasi_event,
                'hadir' => 0,
                'jumlah_kehadiran' => 0,
                'hotel' => null,
                'nomor_kamar_hotel' => null,
                'jumlah_orang_menginap' => null,
                'checkin' => null,
                'doorprize' => '-',
            ];
        }

        // Samakan alur display dengan export Excel:
        // 1) Kelompokkan per lokasi_event
        // 2) Di dalam lokasi, AGEN gunakan key AGEN_{kode_agen} (overwrite jika duplikat)
        // 3) Urutkan group berdasarkan nama toko/agen
        // 4) Flatten kembali jadi baris
        $groupedByLokasi = [];
        foreach ($allData as $item) {
            $lokasi = $item['lokasi_event'] ?? '';
            if (!isset($groupedByLokasi[$lokasi])) {
                $groupedByLokasi[$lokasi] = [];
            }
            $groupedByLokasi[$lokasi][] = $item;
        }
        ksort($groupedByLokasi);

        $displayData = [];
        foreach ($groupedByLokasi as $items) {
            $groupedByToko = [];

            foreach ($items as $item) {
                if (($item['type'] ?? '') === 'TOKO') {
                    $keyGroup = ($item['nama_toko'] ?? '') . '|' . ($item['pic'] ?? '') . '|' . ($item['kota'] ?? '') . '|' . ($item['no_hp'] ?? '');
                    if (!isset($groupedByToko[$keyGroup])) {
                        $groupedByToko[$keyGroup] = [];
                    }
                    $groupedByToko[$keyGroup][] = $item;
                } else {
                    $groupedByToko['AGEN_' . ($item['kode_agen'] ?? '')] = [$item];
                }
            }

            uasort($groupedByToko, function ($a, $b) {
                $nameA = ($a[0]['nama_toko'] ?? '') ?: ($a[0]['nama_agen'] ?? '');
                $nameB = ($b[0]['nama_toko'] ?? '') ?: ($b[0]['nama_agen'] ?? '');
                return strcmp($nameA, $nameB);
            });

            foreach ($groupedByToko as $groupItems) {
                foreach ($groupItems as $item) {
                    $displayData[] = $item;
                }
            }
        }

        $rows = collect($displayData)
            ->map(function ($item) {
                return [
                    'type' => $item['type'] ?? '-',
                    'source' => $item['source'] ?? '-',
                    'db_id' => $item['db_id'] ?? null,
                    'nama_agen' => $item['nama_agen'] ?: '-',
                    'nama_toko' => $item['nama_toko'] ?: '-',
                    'lokasi_event' => $item['lokasi_event'],
                    'kota'     => $item['kota'] ?? null,
                    'hadir' => (int) ($item['hadir'] ?? 0),
                    'jumlah_kehadiran' => (int) ($item['jumlah_kehadiran'] ?? 0),
                    'hotel' => $item['hotel'],
                    'nomor_kamar_hotel' => $item['nomor_kamar_hotel'] ?? null,
                    'jumlah_orang_menginap' => $item['jumlah_orang_menginap'] ?? null,
                    'checkin' => $item['checkin'],
                    'doorprize' => $item['doorprize'] ?? '-',
                    'order_point' => $item['type'] === 'AGEN' ? 0 : (int) ($this->calculateTotalOrder($item) ?? 0),
                    'order_count' => $item['type'] === 'AGEN' ? 0 : (int) $this->countFormOrder($item),
                    'pic' => $item['pic'] ?? null,
                    'no_hp' => $item['no_hp'] ?? null,
                    'email' => $item['email'] ?? '',
                    'kode_agen' => $item['kode_agen'] ?? null,
                ];
            });

        if (!empty($search)) {
            $needle = mb_strtolower($search);
            $rows = $rows->filter(function ($item) use ($needle) {
                $haystack = implode(' ', [
                    (string) ($item['type'] ?? ''),
                    (string) ($item['source'] ?? ''),
                    (string) ($item['nama_agen'] ?? ''),
                    (string) ($item['nama_toko'] ?? ''),
                    (string) ($item['lokasi_event'] ?? ''),
                    (string) ($item['hotel'] ?? ''),
                    (string) ($item['nomor_kamar_hotel'] ?? ''),
                    (string) ($item['jumlah_orang_menginap'] ?? ''),
                    (string) ($item['checkin'] ?? ''),
                    (string) ($item['doorprize'] ?? ''),
                    (string) ($item['order_point'] ?? ''),
                ]);

                return mb_stripos($haystack, $needle) !== false;
            });
        }

        if (!empty($tipe) && $tipe !== 'semua') {
            $rows = $rows->filter(function ($item) use ($tipe) {
                return ($item['type'] ?? '') === $tipe;
            });
        }

        if (!empty($sumberData) && $sumberData !== 'semua') {
            $rows = $rows->filter(function ($item) use ($sumberData) {
                return ($item['source'] ?? '') === $sumberData;
            });
        }

        $rows = $rows
            ->sortBy([
                ['lokasi_event', 'asc'],
                ['nama_agen', 'asc'],
                ['nama_toko', 'asc'],
            ])
            ->values();

        $perPage = 50000;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = $rows->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $rekapan = new LengthAwarePaginator(
            $items,
            $rows->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => $request->query(),
            ]
        );

        return view('daftartoko.rekapan-gabungan', [
            'rekapan' => $rekapan,
            'totalRows' => $rows->count(),
            'search' => $search,
            'lokasiEvent' => $lokasiEvent,
            'tipe' => $tipe,
            'sumberData' => $sumberData,
            'lokasiEvents' => $lokasiEvents,
            'defaultLokasi' => $defaultLokasi,
            'daftarAgenFilter' => $daftarAgenFilter,
            'totalFormOrderRecords' => $totalFormOrderRecords,
        ]);
    }

    public function exportRekapanGabunganExcel(Request $request)
    {   
        // Tingkatkan memory limit
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        
        $search = $request->query('search');
        $lokasiEvent = $request->query('lokasi_event');
        
        // ==================== AMBIL SEMUA DATA UNIK DARI FORM ORDER ====================
        $queryFormOrder = FormOrder::query();
        
        // Filter berdasarkan lokasi event jika ada
        if ($lokasiEvent && $lokasiEvent != 'semua') {
            $queryFormOrder->where('lokasi_event', $lokasiEvent);
        }

        // Jumlah record form order per lokasi — dipakai untuk summary "Form Order" di sheet Summary
        $formOrderCountByLokasi = (clone $queryFormOrder)
            ->select('lokasi_event', DB::raw('COUNT(*) as jml'))
            ->groupBy('lokasi_event')
            ->pluck('jml', 'lokasi_event');

        // Ambil data unik dari form_order berdasarkan kombinasi tertentu
        $uniqueOrders = $queryFormOrder->select([
                'nama_toko',
                'pic',
                'kota',
                'no_hp',
                'lokasi_event',
                'kode_agen'
            ])
            ->distinct()
            ->get();
        
        // ==================== AMBIL DATA TOKO DARI TABEL DAFTAR_TOKO ====================
        $queryToko = DaftarToko::query();
        
        // Filter pencarian untuk toko
        if ($search) {
            $queryToko->where(function($q) use ($search) {
                $q->where('nama_toko', 'like', "%{$search}%")
                ->orWhere('kode_toko', 'like', "%{$search}%")
                ->orWhere('pic', 'like', "%{$search}%")
                ->orWhere('lokasi_event', 'like', "%{$search}%")
                ->orWhere('kota', 'like', "%{$search}%")
                ->orWhere('nama_agen', 'like', "%{$search}%");
            });
        }
        
        // Filter lokasi event untuk toko
        if ($lokasiEvent && $lokasiEvent != 'semua') {
            $queryToko->where('lokasi_event', $lokasiEvent);
        }
        
        // Ambil semua data toko (dedup: 1 baris per toko+agen agar tidak double count)
        $seenTokoKey = [];
        $dataToko = $queryToko->get()->filter(function ($toko) use (&$seenTokoKey) {
            $key = mb_strtolower(implode('|', [
                trim((string) $toko->nama_toko),
                trim((string) ($toko->pic ?? '')),
                trim((string) ($toko->nomor_pic ?? '')),
                trim((string) ($toko->kota ?? '')),
                trim((string) ($toko->lokasi_event ?? '')),
                trim((string) ($toko->kode_agen ?? '')),
            ]));
            if (isset($seenTokoKey[$key])) {
                return false;
            }
            $seenTokoKey[$key] = true;
            return true;
        })->values();
        
        // ==================== AMBIL DATA AGEN DARI TABEL DAFTAR_AGEN ====================
        $queryAgen = DaftarAgen::query();
        
        // Filter pencarian untuk agen
        if ($search) {
            $queryAgen->where(function($q) use ($search) {
                $q->where('nama_agen', 'like', "%{$search}%")
                ->orWhere('kode_agen', 'like', "%{$search}%")
                ->orWhere('pic', 'like', "%{$search}%")
                ->orWhere('lokasi_event', 'like', "%{$search}%")
                ->orWhere('kota', 'like', "%{$search}%");
            });
        }
        
        // Filter lokasi event untuk agen
        if ($lokasiEvent && $lokasiEvent != 'semua') {
            $queryAgen->where('lokasi_event', $lokasiEvent);
        }
        
        // Ambil data agen
        $dataAgen = $queryAgen->get();
        
        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set judul
        $sheet->setTitle('Rekapan Gabungan');
        
        // Header dengan styling
        $headers = ['No', 'Lokasi Event', 'Tipe', 'Sumber Data', 'Kode Agen', 'Nama Agen', 'Nama Toko', 'Hadir', 'Jumlah Kehadiran', 'Fasilitas Hotel', 'No. Kamar', 'Jml Orang', 'Ditempati', 'Form Order', 'Order (Point)', 'Doorprize'];
        
        // Set header dan styling
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $cell = $column . '1';
            
            // Set nilai
            $sheet->setCellValue($cell, $header);
            
            // Styling header - Warna biru
            $sheet->getStyle($cell)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'] // Biru Excel
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
        }
        
        // Set lebar kolom
        $columnWidths = [
            'A' => 8,   // No
            'B' => 20,  // Lokasi Event
            'C' => 10,  // Tipe
            'D' => 15,  // Sumber Data
            'E' => 14,  // Kode Agen
            'F' => 30,  // Nama Agen
            'G' => 35,  // Nama Toko
            'H' => 10,  // Hadir
            'I' => 16,  // Jumlah Kehadiran
            'J' => 25,  // Fasilitas Hotel
            'K' => 16,  // No. Kamar
            'L' => 14,  // Jml Orang
            'M' => 14,  // Ditempati
            'N' => 14,  // Form Order
            'O' => 18,  // Order (Point)
            'P' => 25,  // Doorprize
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        // ==================== GABUNGKAN DAN PROSES DATA ====================
        
        // Array untuk menyimpan semua data yang akan ditampilkan
        $allData = [];
        
        // 1. Data dari DaftarToko
        foreach ($dataToko as $toko) {
            // Key untuk grouping harus TANPA lokasi_event agar hanya toko yang benar-benar sama
            $keyGroup = $toko->nama_toko . '|' . $toko->pic . '|' . $toko->kota . '|' . $toko->nomor_pic;
            // Key untuk identifikasi unik DENGAN lokasi_event
            $keyUnique = $keyGroup . '|' . $toko->lokasi_event;
            
            $allData[] = [
                'type' => 'TOKO',
                'source' => 'DAFTAR_TOKO',
                'nama_toko' => $toko->nama_toko,
                'nama_agen' => $toko->nama_agen,
                'kode_agen' => $toko->kode_agen,
                'pic' => $toko->pic,
                'kota' => $toko->kota,
                'no_hp' => $toko->nomor_pic,
                'lokasi_event' => $toko->lokasi_event,
                'email' => $toko->email ?? '',
                'hadir' => (int) ($toko->hadir ?? 0),
                'jumlah_kehadiran' => $toko->jumlah_kehadiran,
                'hotel' => $toko->hotel,
                'nomor_kamar_hotel' => $toko->nomor_kamar_hotel ?? '',
                'jumlah_orang_menginap' => $toko->jumlah_orang_menginap ?? 0,
                'checkin' => $toko->checkin,
                'doorprize' => $this->getDoorprize($toko->nama_toko, $toko->pic, $toko->nomor_pic, $toko->lokasi_event),
                'key_group' => $keyGroup, // Untuk grouping
                'key_unique' => $keyUnique . '|' . $toko->kode_agen // Untuk identifikasi unik
            ];
        }
        
        // 2. Data dari DaftarAgen
        foreach ($dataAgen as $agen) {
            $allData[] = [
                'type' => 'AGEN',
                'source' => 'DAFTAR_AGEN',
                'nama_toko' => $agen->nama_agen,
                'nama_agen' => $agen->nama_agen,
                'kode_agen' => $agen->kode_agen,
                'pic' => $agen->pic,
                'kota' => $agen->kota,
                'no_hp' => $agen->nomor_pic,
                'lokasi_event' => $agen->lokasi_event,
                'email' => $agen->email ?? '',
                'hadir' => (int) ($agen->hadir ?? 0),
                'jumlah_kehadiran' => $agen->jumlah_kehadiran,
                'hotel' => $agen->hotel,
                'nomor_kamar_hotel' => $agen->nomor_kamar_hotel ?? '',
                'jumlah_orang_menginap' => $agen->jumlah_orang_menginap ?? 0,
                'checkin' => $agen->checkin,
                'doorprize' => '-',
                'key_group' => 'AGEN_' . $agen->kode_agen,
                'key_unique' => 'AGEN_' . $agen->kode_agen
            ];
        }
        
        // 3. Data dari FormOrder yang TIDAK ada di DaftarToko
        foreach ($uniqueOrders as $order) {
            // Cek apakah data ini sudah ada di daftar toko
            // Dibandingkan case-insensitive + trim, selaras dengan collation MySQL (utf8mb4_general_ci)
            // yang dipakai calculateTotalOrder/countFormOrder, supaya tidak ada overlap double count.
            $existsInToko = $dataToko->first(function($toko) use ($order) {
                return mb_strtolower(trim((string) $toko->nama_toko)) == mb_strtolower(trim((string) $order->nama_toko)) &&
                    mb_strtolower(trim((string) ($toko->pic ?? ''))) == mb_strtolower(trim((string) ($order->pic ?? ''))) &&
                    mb_strtolower(trim((string) ($toko->kota ?? ''))) == mb_strtolower(trim((string) ($order->kota ?? ''))) &&
                    mb_strtolower(trim((string) ($toko->nomor_pic ?? ''))) == mb_strtolower(trim((string) ($order->no_hp ?? ''))) &&
                    mb_strtolower(trim((string) ($toko->lokasi_event ?? ''))) == mb_strtolower(trim((string) ($order->lokasi_event ?? ''))) &&
                    mb_strtolower(trim((string) ($toko->kode_agen ?? ''))) == mb_strtolower(trim((string) ($order->kode_agen ?? '')));
            });
            
            if (!$existsInToko) {
                $keyGroup = $order->nama_toko . '|' . $order->pic . '|' . $order->kota . '|' . $order->no_hp;
                $keyUnique = $keyGroup . '|' . $order->lokasi_event;
                
                // FORM_ORDER: hadir, jumlah_kehadiran, hotel, nomor_kamar, jumlah_orang, checkin selalu kosong
                $jumlahKehadiran = 0;
                $hadir = 0;
                $hotel = '';
                $nomorKamar = '';
                $jumlahOrang = 0;
                $checkin = '';
                
                // Doorprize tetap dicari dari toko yang sama jika ada
                $doorprize = '-';
                $similarToko = $dataToko->first(function($toko) use ($order) {
                    return mb_strtolower(trim((string) $toko->nama_toko)) == mb_strtolower(trim((string) $order->nama_toko)) &&
                        mb_strtolower(trim((string) ($toko->pic ?? ''))) == mb_strtolower(trim((string) ($order->pic ?? ''))) &&
                        mb_strtolower(trim((string) ($toko->kota ?? ''))) == mb_strtolower(trim((string) ($order->kota ?? ''))) &&
                        mb_strtolower(trim((string) ($toko->nomor_pic ?? ''))) == mb_strtolower(trim((string) ($order->no_hp ?? '')));
                });
                if ($similarToko) {
                    $doorprize = $this->getDoorprize($similarToko->nama_toko, $similarToko->pic, $similarToko->nomor_pic, $similarToko->lokasi_event);
                }
                
                // Cari nama agen dari daftar agen jika kode_agen ada
                $namaAgen = '';
                if ($order->kode_agen) {
                    $agen = $dataAgen->firstWhere('kode_agen', $order->kode_agen);
                    if ($agen) {
                        $namaAgen = $agen->nama_agen;
                    } elseif ($order->nama_agen) {
                        $namaAgen = $order->nama_agen;
                    }
                }
                
                $allData[] = [
                    'type' => 'TOKO',
                    'source' => 'FORM_ORDER',
                    'nama_toko' => $order->nama_toko,
                    'nama_agen' => $namaAgen,
                    'kode_agen' => $order->kode_agen,
                    'pic' => $order->pic,
                    'kota' => $order->kota,
                    'no_hp' => $order->no_hp,
                    'lokasi_event' => $order->lokasi_event,
                    'email' => '',
                    'hadir' => $hadir,
                    'jumlah_kehadiran' => $jumlahKehadiran,
                    'hotel' => $hotel,
                    'nomor_kamar_hotel' => $nomorKamar,
                    'jumlah_orang_menginap' => $jumlahOrang,
                    'checkin' => $checkin,
                    'doorprize' => $doorprize,
                    'key_group' => $keyGroup,
                    'key_unique' => $keyUnique . '|' . $order->kode_agen
                ];
            }
        }
        
        // ==================== KELOMPOKKAN DATA BERDASARKAN LOKASI EVENT ====================
        
        // Pertama, kelompokkan berdasarkan lokasi_event
        $groupedByLokasi = [];
        
        foreach ($allData as $item) {
            $lokasi = $item['lokasi_event'];
            if (!isset($groupedByLokasi[$lokasi])) {
                $groupedByLokasi[$lokasi] = [];
            }
            $groupedByLokasi[$lokasi][] = $item;
        }
        
        // Urutkan berdasarkan lokasi event
        ksort($groupedByLokasi);
        
        // ==================== PROSES PER LOKASI EVENT ====================
        
        $row = 2;
        $counter = 1;
        
        foreach ($groupedByLokasi as $lokasiEvent => $items) {
            // Kelompokkan items dalam lokasi ini berdasarkan key_group
            $groupedByToko = [];
            
            foreach ($items as $item) {
                if ($item['type'] == 'TOKO') {
                    $keyGroup = $item['key_group'];
                    if (!isset($groupedByToko[$keyGroup])) {
                        $groupedByToko[$keyGroup] = [];
                    }
                    $groupedByToko[$keyGroup][] = $item;
                } else {
                    // Untuk AGEN, tambahkan ke array terpisah
                    $groupedByToko['AGEN_' . $item['kode_agen']] = [$item];
                }
            }
            
            // Urutkan groups berdasarkan nama toko/agen
            uasort($groupedByToko, function($a, $b) {
                $nameA = $a[0]['nama_toko'] ?: $a[0]['nama_agen'];
                $nameB = $b[0]['nama_toko'] ?: $b[0]['nama_agen'];
                return strcmp($nameA, $nameB);
            });
            
            // Tampilkan data per group
            foreach ($groupedByToko as $groupKey => $groupItems) {
                $isTokoGroup = strpos($groupKey, 'AGEN_') === false;
                
                // Tampilkan semua items dalam group
                foreach ($groupItems as $item) {
                    // Hitung total order
                    $totalOrder = $item['type'] === 'AGEN' ? 0 : $this->calculateTotalOrder($item);
                    
                    // Isi data ke sheet
                    $hadirText = ($item['hadir'] ?? 0) ? '✓' : '✗';
                    $checkinText = ($item['checkin'] ?? 0) ? '✓' : '✗';
                    $formOrderText = ($totalOrder ?? 0) ? '✓' : '✗';

                    $sheet->setCellValue('A' . $row, $counter);
                    $sheet->setCellValue('B' . $row, $item['lokasi_event'] ?? '');
                    $sheet->setCellValue('C' . $row, $item['type']);
                    $sheet->setCellValue('D' . $row, $item['source']);
                    $sheet->setCellValue('E' . $row, $item['kode_agen'] ?? '');
                    $sheet->setCellValue('F' . $row, $item['nama_agen'] ?? '');
                    $sheet->setCellValue('G' . $row, $item['nama_toko'] ?: $item['nama_agen']);
                    $sheet->setCellValue('H' . $row, $hadirText);
                    $sheet->setCellValue('I' . $row, $item['jumlah_kehadiran'] ?? 0);
                    $sheet->setCellValue('J' . $row, $item['hotel'] ?? '');
                    $sheet->setCellValue('K' . $row, $item['nomor_kamar_hotel'] ?? '');
                    $sheet->setCellValue('L' . $row, $item['jumlah_orang_menginap'] ?? 0);
                    $sheet->setCellValue('M' . $row, $checkinText);
                    $sheet->setCellValue('N' . $row, $formOrderText);
                    $sheet->setCellValue('O' . $row, $totalOrder ?? 0);
                    $sheet->setCellValue('P' . $row, $item['doorprize'] ?? '-');
                    
                    $row++;
                    $counter++;
                    
                    // Flush memory setiap 100 record
                    if ($counter % 100 == 0) {
                        gc_collect_cycles();
                    }
                }
            }
        }
        
        // ==================== STYLING ====================
        
        $lastRow = $row - 1;
        if ($lastRow > 1) {
            // Border untuk semua data
            $sheet->getStyle('A2:P' . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD']
                    ]
                ]
            ]);
            
            // Format angka untuk kolom Order
            $sheet->getStyle('O2:O' . $lastRow)
                ->getNumberFormat()
                ->setFormatCode('#,##0');
            
            // Alignment untuk semua kolom
            $alignmentCenter = [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ];
            
            $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B2:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C2:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D2:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E2:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H2:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I2:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J2:J' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('K2:K' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L2:L' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('M2:M' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('N2:N' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('O2:O' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('P2:P' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }
        
        // Set alignment untuk header
        $sheet->getStyle('A1:P1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Auto size kolom
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // ==================== SHEET 2: SUMMARY ====================
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Summary');
        
        $summaryHeaders = ['Lokasi Event', 'Hadir', 'Jumlah Kehadiran', 'Fasilitas Hotel', 'Jumlah Orang Menginap', 'Ditempati', 'Form Order', 'Order (Point)'];
        
        foreach ($summaryHeaders as $index => $header) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $summarySheet->setCellValue($col . '1', $header);
            $summarySheet->getStyle($col . '1')->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
        }
        
        // Kelompokkan data per lokasi_event, dedup dengan aturan yg sama seperti di view
        $summaryGroups = [];
        foreach ($allData as $item) {
            $lokasi = $item['lokasi_event'];

            // Summary key SAMA dengan di view: type|nama_toko|pic|no_hp|kota|email
            $summaryKey = mb_strtolower(implode('|', [
                trim($item['type'] ?? ''),
                trim($item['nama_toko'] ?? ''),
                trim($item['pic'] ?? ''),
                trim($item['no_hp'] ?? ''),
                trim($item['kota'] ?? ''),
                trim($item['email'] ?? ''),
            ]));

            if (!isset($summaryGroups[$lokasi])) {
                $summaryGroups[$lokasi] = [];
            }

            // Aturan dedup SAMA dengan di view:
            // - FORM_ORDER selalu kalah jika ada DAFTAR_TOKO/DAFTAR_AGEN
            $currentSource = $item['source'] ?? '';
            $existing = $summaryGroups[$lokasi][$summaryKey] ?? null;

            $hadirVal = (int) ($item['hadir'] ?? 0);
            $kehadiranVal = (int) ($item['jumlah_kehadiran'] ?? 0);

            if (
                !$existing ||
                ($existing['source'] === 'FORM_ORDER' && $currentSource !== 'FORM_ORDER')
            ) {
                $summaryGroups[$lokasi][$summaryKey] = [
                    'source' => $currentSource,
                    'hadir' => $hadirVal,
                    'jumlah_kehadiran' => $kehadiranVal,
                    'hotel' => !empty($item['hotel']),
                    'checkin' => !empty($item['checkin']),
                    'jumlah_orang_menginap' => (int) ($item['jumlah_orang_menginap'] ?? 0),
                    'type' => $item['type'] ?? '',
                    'order_point' => $item['type'] === 'AGEN' ? 0 : (int) ($this->calculateTotalOrder($item) ?? 0),
                ];
            }
        }
        
        $summaryRow = 2;
        ksort($summaryGroups);

        // Hitung order point dari ALL data (bukan dari hasil dedup), sama seperti di view.
        // Form Order diambil dari jumlah RECORD form_order per lokasi ($formOrderCountByLokasi),
        // bukan dari perhitungan baris, supaya sinkron dengan tabel form-order/index.
        $orderPointByLokasi = [];
        foreach ($allData as $item) {
            $lok = $item['lokasi_event'] ?? '';
            if (!isset($orderPointByLokasi[$lok])) {
                $orderPointByLokasi[$lok] = 0;
            }
            if (($item['type'] ?? '') !== 'AGEN') {
                $itemOrderPoint = (int) ($this->calculateTotalOrder($item) ?? 0);
                $orderPointByLokasi[$lok] += $itemOrderPoint;
            }
        }

        foreach ($summaryGroups as $lokasi => $groups) {
            $hadir = 0;
            $kehadiran = 0;
            $hotel = 0;
            $checkin = 0;
            $jumlahOrang = 0;
            
            foreach ($groups as $g) {
                $hadir += $g['hadir'];
                $kehadiran += $g['jumlah_kehadiran'];
                if ($g['hotel']) $hotel++;
                if ($g['checkin']) $checkin++;
                $jumlahOrang += $g['jumlah_orang_menginap'];
            }
            
            $formOrder = $formOrderCountByLokasi[$lokasi] ?? 0;
            $orderPoint = $orderPointByLokasi[$lokasi] ?? 0;
            
            $summarySheet->setCellValue('A' . $summaryRow, $lokasi);
            $summarySheet->setCellValue('B' . $summaryRow, $hadir);
            $summarySheet->setCellValue('C' . $summaryRow, $kehadiran);
            $summarySheet->setCellValue('D' . $summaryRow, $hotel);
            $summarySheet->setCellValue('E' . $summaryRow, $jumlahOrang);
            $summarySheet->setCellValue('F' . $summaryRow, $checkin);
            $summarySheet->setCellValue('G' . $summaryRow, $formOrder);
            $summarySheet->setCellValue('H' . $summaryRow, $orderPoint);
            
            $summaryRow++;
        }
        
        // Styling summary
        $lastSummaryRow = $summaryRow - 1;
        if ($lastSummaryRow > 1) {
            $summarySheet->getStyle('A2:H' . $lastSummaryRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD']
                    ]
                ]
            ]);
            $summarySheet->getStyle('H2:H' . $lastSummaryRow)->getNumberFormat()->setFormatCode('#,##0');
            foreach (range('A', 'H') as $col) {
                $summarySheet->getStyle($col . '2:' . $col . $lastSummaryRow)
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $summarySheet->getStyle('A2:A' . $lastSummaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }
        
        foreach (range('A', 'H') as $col) {
            $summarySheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Nama file
        $fileName = 'rekapan-gabungan-lengkap-' . date('Y-m-d') . '.xlsx';
        
        // Simpan ke temporary file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'rekapan_lengkap_');
        $writer->save($tempFile);
        
        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // Helper function untuk mendapatkan doorprize
    private function getDoorprize($namaToko, $pic, $noHp, $lokasiEvent)
    {
        $voucher = Voucher::where('nama_toko', $namaToko)
            ->where('nama_pic', $pic)
            ->where('no_hp', $noHp)
            ->where('lokasi_event', $lokasiEvent)
            ->whereNotNull('hadiah')
            ->where('hadiah', '!=', '')
            ->first();
        
        return $voucher->hadiah ?? '-';
    }

    // Helper function untuk menghitung total order
    private function calculateTotalOrder($item)
    {
        if ($item['type'] == 'TOKO') {
            return FormOrder::where('nama_toko', $item['nama_toko'])
                ->where('pic', $item['pic'])
                ->where('no_hp', $item['no_hp'])
                ->where('kota', $item['kota'])
                ->where('lokasi_event', $item['lokasi_event'])
                ->where('kode_agen', $item['kode_agen'])
                ->sum('total_point');
        } else {
            return FormOrder::where('kode_agen', $item['kode_agen'])
                ->sum('total_point');
        }
    }

    // Helper function untuk menghitung jumlah record form order per kombinasi toko+agen
    private function countFormOrder($item)
    {
        if ($item['type'] == 'TOKO') {
            return FormOrder::where('nama_toko', $item['nama_toko'])
                ->where('pic', $item['pic'])
                ->where('no_hp', $item['no_hp'])
                ->where('kota', $item['kota'])
                ->where('lokasi_event', $item['lokasi_event'])
                ->where('kode_agen', $item['kode_agen'])
                ->count();
        }
        return 0;
    }

    public function exportTracking(Request $request)
    {
        $search = $request->query('search');
        $lokasiEvent = $request->query('lokasi_event');
        
        return Excel::download(new TokoTrackingExport($search, $lokasiEvent), 'tracking-toko-' . date('Y-m-d') . '.xlsx');
    }

    public function exportTrackingExcel(Request $request)
    {
        // Tingkatkan memory limit
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        
        $search = $request->query('search');
        $lokasiEvent = $request->query('lokasi_event');
        
        $query = DaftarToko::query();
        
        // Filter pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_toko', 'like', "%{$search}%")
                ->orWhere('kode_toko', 'like', "%{$search}%")
                ->orWhere('pic', 'like', "%{$search}%")
                ->orWhere('lokasi_event', 'like', "%{$search}%")
                ->orWhere('kota', 'like', "%{$search}%");
            });
        }
        
        // Filter lokasi event
        if ($lokasiEvent && $lokasiEvent != 'semua') {
            $query->where('lokasi_event', $lokasiEvent);
        }
        
        // Ambil data
        $data = $query->selectRaw('
                nama_toko,
                pic,
                kota,
                nomor_pic,
                lokasi_event,
                MAX(kode_toko) as kode_toko,
                MAX(jumlah_kehadiran) as jumlah_kehadiran,
                MAX(hotel) as hotel,
                MAX(checkin) as checkin
            ')
            ->groupBy('nama_toko', 'pic', 'kota', 'nomor_pic', 'lokasi_event')
            ->orderBy('nama_toko', 'asc')
            ->cursor();
        
        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set judul
        $sheet->setTitle('Tracking Toko');
        
        // Header dengan styling
        $headers = ['No', 'Nama Toko', 'Hadir', 'Order (Point)', 'Hotel', 'Ditempati', 'Doorprize'];
        
        // Set header dan styling
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $cell = $column . '1';
            
            // Set nilai
            $sheet->setCellValue($cell, $header);
            
            // Styling header - Warna biru
            $sheet->getStyle($cell)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'] // Biru Excel
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
        }
        
        // Set lebar kolom
        $sheet->getColumnDimension('A')->setWidth(8);  // No
        $sheet->getColumnDimension('B')->setWidth(40); // Nama Toko
        $sheet->getColumnDimension('C')->setWidth(15); // Hadir
        $sheet->getColumnDimension('D')->setWidth(20); // Order
        $sheet->getColumnDimension('E')->setWidth(25); // Hotel
        $sheet->getColumnDimension('F')->setWidth(15); // Ditempati
        $sheet->getColumnDimension('G')->setWidth(25); // Doorprize
        
        // Isi data
        $row = 2;
        $counter = 1;
        
        foreach ($data as $toko) {
            // Hitung total order
            $totalOrder = FormOrder::where('nama_toko', $toko->nama_toko)
                ->where('pic', $toko->pic)
                ->where('no_hp', $toko->nomor_pic)
                ->where('kota', $toko->kota)
                ->where('lokasi_event', $toko->lokasi_event)
                ->sum('total_point');
            
            // Ambil doorprize
            $voucher = Voucher::where('nama_toko', $toko->nama_toko)
                ->where('nama_pic', $toko->pic)
                ->where('no_hp', $toko->nomor_pic)
                ->where('lokasi_event', $toko->lokasi_event)
                ->whereNotNull('hadiah')
                ->where('hadiah', '!=', '')
                ->first();
            
            // Isi data ke sheet
            $sheet->setCellValue('A' . $row, $counter);
            $sheet->setCellValue('B' . $row, $toko->nama_toko);
            $sheet->setCellValue('C' . $row, $toko->jumlah_kehadiran ?? 0);
            $sheet->setCellValue('D' . $row, $totalOrder ?? 0);
            $sheet->setCellValue('E' . $row, $toko->hotel ?? '');
            $sheet->setCellValue('F' . $row, $toko->checkin ?? '');
            $sheet->setCellValue('G' . $row, $voucher->hadiah ?? '');
            
            // Alternatif styling untuk baris (zebra pattern)
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F2F2F2']
                    ]
                ]);
            }
            
            // Tambah border untuk setiap cell
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD']
                    ]
                ]
            ]);
            
            $row++;
            $counter++;
            
            // Flush memory setiap 100 record
            if ($counter % 100 == 0) {
                gc_collect_cycles();
            }
        }
        
        // Auto size kolom setelah data diisi
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Set alignment untuk kolom angka
        $sheet->getStyle('A:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Nama file
        $fileName = 'tracking-toko-' . date('Y-m-d') . '.xlsx';
        
        // Simpan ke temporary file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'tracking_');
        $writer->save($tempFile);
        
        // Return download response
        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function exportTrackingCSV(Request $request)
    {
        // Tingkatkan memory limit
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        
        $search = $request->query('search');
        $lokasiEvent = $request->query('lokasi_event');
        
        $query = DaftarToko::query();
        
        // Filter berdasarkan role user
        // if(auth()->user()->role_as == 0){
        //     $query->where('kode_agen', auth()->user()->id_customer);
        // }
        
        // Filter pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_toko', 'like', "%{$search}%")
                ->orWhere('kode_toko', 'like', "%{$search}%")
                ->orWhere('pic', 'like', "%{$search}%")
                ->orWhere('lokasi_event', 'like', "%{$search}%")
                ->orWhere('kota', 'like', "%{$search}%");
            });
        }
        
        // Filter lokasi event
        if ($lokasiEvent && $lokasiEvent != 'semua') {
            $query->where('lokasi_event', $lokasiEvent);
        }
        
        // Ambil data dengan chunk untuk hemat memory
        $data = $query->selectRaw('
                nama_toko,
                pic,
                kota,
                nomor_pic,
                lokasi_event,
                MAX(kode_toko) as kode_toko,
                MAX(jumlah_kehadiran) as jumlah_kehadiran,
                MAX(hotel) as hotel,
                MAX(checkin) as checkin
            ')
            ->groupBy('nama_toko', 'pic', 'kota', 'nomor_pic', 'lokasi_event')
            ->orderBy('nama_toko', 'asc')
            ->cursor(); // Gunakan cursor untuk hemat memory
        
        $fileName = 'tracking-toko-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan BOM untuk Excel UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Header
            fputcsv($file, ['No', 'Nama Toko', 'Hadir', 'Order (Point)', 'Hotel', 'Ditempati', 'Doorprize']);
            
            $counter = 1;
            
            foreach ($data as $toko) {
                // Hitung total order
                $totalOrder = FormOrder::where('nama_toko', $toko->nama_toko)
                    ->where('pic', $toko->pic)
                    ->where('no_hp', $toko->nomor_pic)
                    ->where('kota', $toko->kota)
                    ->where('lokasi_event', $toko->lokasi_event)
                    ->sum('total_point');
                
                // Ambil doorprize
                $voucher = Voucher::where('nama_toko', $toko->nama_toko)
                    ->where('nama_pic', $toko->pic)
                    ->where('no_hp', $toko->nomor_pic)
                    ->where('lokasi_event', $toko->lokasi_event)
                    ->whereNotNull('hadiah')
                    ->where('hadiah', '!=', '')
                    ->first();
                
                fputcsv($file, [
                    $counter++,
                    $toko->nama_toko,
                    $toko->jumlah_kehadiran ?? 0,
                    $totalOrder ?? 0,
                    $toko->hotel ?? '',
                    $toko->checkin ?? '',
                    $voucher->hadiah ?? ''
                ]);
                
                // Flush output setiap 50 record
                if ($counter % 50 == 0) {
                    fflush($file);
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        // Generate kode toko otomatis - cari angka terbesar dari kode_toko yang sesuai format
        $lastToko = DaftarToko::where('kode_toko', 'REGEXP', '^T[0-9]+$')
            ->orderByRaw('CAST(SUBSTRING(kode_toko, 2) AS UNSIGNED) DESC')
            ->first();
        
        $nextNumber = 1;
        if ($lastToko && preg_match('/^T(\d+)$/', $lastToko->kode_toko, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }
        
        $kodeToko = 'T' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        
        $provinsis = Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')->get();
        // $lokasiEvents = MasterLokasiEvent::where('status', 'Aktif')->get();
        $lokasiEvents = MasterLokasiEvent::get();
        
        // Ambil semua agen jika department SLS
        $agenList = [];
        $isSalesDepartment = auth()->user()->department === 'SLS';
        
        if ($isSalesDepartment) {
            $agenList = DaftarAgen::select('kode_agen', 'nama_agen')->orderBy('nama_agen', 'asc')->get();
        }
        
        return view('daftartoko.create', compact('provinsis', 'kodeToko', 'lokasiEvents', 'agenList', 'isSalesDepartment'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_agen' => 'required|max:50',
            'nama_agen' => 'required|max:255',
            'nama_toko' => 'required|max:255',
            'alamat' => 'required',
            'kota' => 'required|max:100',
            'pic' => 'required|max:255',
            'nomor_pic' => 'required|max:20',
            'nama_sales' => 'required|max:255',
            'lokasi_event' => 'required|max:100'
        ], [
            'kode_agen.required' => 'Kode agen wajib diisi',
            'nama_agen.required' => 'Nama agen wajib diisi',
            'nama_toko.required' => 'Nama toko wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'kota.required' => 'Kota wajib diisi',
            'pic.required' => 'PIC wajib diisi',
            'nomor_pic.required' => 'Nomor PIC wajib diisi',
            'nama_sales.required' => 'Nama sales wajib diisi',
            'lokasi_event.required' => 'Lokasi event wajib diisi'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Dapatkan kode toko dengan angka terbesar dari format T[angka]
            $lastToko = DaftarToko::where('kode_toko', 'REGEXP', '^T[0-9]+$')
                ->orderByRaw('CAST(SUBSTRING(kode_toko, 2) AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();

            $nextNumber = 1;
            if ($lastToko && preg_match('/^T(\d+)$/', $lastToko->kode_toko, $matches)) {
                $nextNumber = (int)$matches[1] + 1;
            }

            $kodeToko = 'T' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Buat data toko
            $tokoData = [
                'kode_agen' => $request->kode_agen,
                'nama_agen' => $request->nama_agen,
                'kode_toko' => $kodeToko,
                'nama_toko' => $request->nama_toko,
                'alamat' => $request->alamat,
                'kota' => $request->kota,
                'pic' => $request->pic,
                'nomor_pic' => $request->nomor_pic,
                'nama_sales' => $request->nama_sales,
                'lokasi_event' => $request->lokasi_event,
                'status' => 1,
                'hadir' => 0,
                'jumlah_kehadiran' => 0,
            ];

            DaftarToko::create($tokoData);

            LogAktivitas::create([
                'user_id'    => auth()->user()->id,
                'username'   => auth()->user()->name,
                'aksi'       => 'Tambah',
                'fitur'      => 'Daftar Toko',
                'deskripsi'  => "Menambahkan Data Toko {$kodeToko} - {$request->nama_toko}",
                'ip_address' => $request->ip(),
                'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('daftartoko.index')
                ->with('success', 'Data toko berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(DaftarToko $daftartoko)
    {
        // Ambil nama provinsi dan kota dari tabel wilayah
        $provinsi = Wilayah::where('kode', $daftartoko->provinsi)->first();
        $kota = Wilayah::where('kode', $daftartoko->kota)->first();
        
        return view('daftartoko.show', compact('daftartoko', 'provinsi', 'kota'));
    }

    public function getAgenByKodeToko(Request $request)
    {
        $kodeToko = $request->get('kode_toko');
        
        if (!$kodeToko) {
            return response()->json([]);
        }
        
        $tokos = DaftarToko::where('kode_toko', $kodeToko)
            ->where('status', 1)
            ->get();
        
        $agenList = [];
        foreach ($tokos as $toko) {
            if ($toko->kode_agen && $toko->nama_agen) {
                $agenList[] = [
                    'kode_agen' => $toko->kode_agen,
                    'nama_agen' => $toko->nama_agen,
                    'id' => $toko->id
                ];
            }
        }
        
        // Remove duplicates
        $uniqueAgen = [];
        foreach ($agenList as $agen) {
            $key = $agen['kode_agen'];
            if (!isset($uniqueAgen[$key])) {
                $uniqueAgen[$key] = $agen;
            }
        }
        
        return response()->json(array_values($uniqueAgen));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DaftarToko $daftartoko)
    {
        $provinsis = Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')->get();
        // $lokasiEvents = MasterLokasiEvent::where('status', 'Aktif')->get();
        $lokasiEvents = MasterLokasiEvent::get();

        // Ambil semua agen jika department SLS
        $agenList = [];
        $isSalesDepartment = auth()->user()->department === 'SLS';
        
        if ($isSalesDepartment) {
            $agenList = DaftarAgen::select('kode_agen', 'nama_agen')->orderBy('nama_agen', 'asc')->get();
        }

        // Ambil agen yang sudah terdaftar untuk toko ini
        $existingAgenCodes = DaftarToko::where('kode_toko', $daftartoko->kode_toko)
            ->where('status', 1)
            ->pluck('kode_agen')
            ->toArray();
        
        // Ambil agen yang belum terdaftar (hanya yang lokasi_event-nya sama)
        $availableAgen = DaftarAgen::whereNotIn('kode_agen', $existingAgenCodes)
            ->where('lokasi_event', $daftartoko->lokasi_event)
            ->orderBy('nama_agen', 'asc')
            ->get();

        return view('daftartoko.edit', compact(
            'daftartoko', 
            'provinsis', 
            'lokasiEvents', 
            'agenList', 
            'isSalesDepartment',
            'availableAgen'
        ));
    }

    public function update(Request $request, DaftarToko $daftartoko)
    {
        $validator = Validator::make($request->all(), [
            'kode_agen' => 'nullable|max:50',
            'nama_agen' => 'nullable|max:255',
            'nama_toko' => 'required|max:255',
            'alamat' => 'required',
            'kota' => 'required|max:100',
            'pic' => 'required|max:255',
            'nomor_pic' => 'required|max:20',
            'nama_sales' => 'required|max:255',
            'lokasi_event' => 'required|max:100',
            'status' => 'required|in:0,1'
        ], [
            'nama_toko.required' => 'Nama toko wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'kota.required' => 'Kota wajib diisi',
            'pic.required' => 'PIC wajib diisi',
            'nomor_pic.required' => 'Nomor PIC wajib diisi',
            'nama_sales.required' => 'Nama sales wajib diisi',
            'lokasi_event.required' => 'Lokasi event wajib diisi',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status harus berupa Aktif atau Tidak Aktif'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Data yang akan diupdate
            $updateData = [
                'nama_toko' => $request->nama_toko,
                'alamat' => $request->alamat,
                'kota' => $request->kota,
                'pic' => $request->pic,
                'nomor_pic' => $request->nomor_pic,
                'nama_sales' => $request->nama_sales,
                'lokasi_event' => $request->lokasi_event,
                'status' => $request->status,
            ];

            // Update SEMUA record dengan kode_toko yang sama
            $affectedRows = DaftarToko::where('kode_toko', $daftartoko->kode_toko)
                ->where('status', 1) // Hanya update yang aktif
                ->update($updateData);

            // Jika ada kode_agen yang dikirim (dari hidden input), update juga untuk record tertentu
            if ($request->filled('kode_agen') && $request->filled('nama_agen')) {
                // Update kode_agen dan nama_agen untuk record yang sedang diedit
                DaftarToko::where('id', $daftartoko->id)
                    ->update([
                        'kode_agen' => $request->kode_agen,
                        'nama_agen' => $request->nama_agen,
                    ]);
            }

            // Log aktivitas
            LogAktivitas::create([
                'user_id'    => auth()->user()->id,
                'username'   => auth()->user()->name,
                'aksi'       => 'Ubah',
                'fitur'      => 'Daftar Toko',
                'deskripsi'  => "Mengupdate data toko dengan kode_toko {$daftartoko->kode_toko} (memengaruhi {$affectedRows} record)",
                'ip_address' => $request->ip(),
                'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('daftartoko.index')
                ->with('success', "Data toko berhasil diperbarui untuk {$affectedRows} agen");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function storeAgenFromEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_toko' => 'required|exists:daftar_toko,kode_toko',
            'kode_agen' => 'required|max:50',
            'nama_agen' => 'required|max:255',
        ], [
            'kode_toko.required' => 'Kode toko tidak valid',
            'kode_toko.exists' => 'Kode toko tidak ditemukan',
            'kode_agen.required' => 'Silakan pilih agen',
            'nama_agen.required' => 'Nama agen wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('tab', 'agen'); // Untuk menunjukkan tab agen
        }

        // Cek apakah kombinasi kode_toko + kode_agen sudah ada
        $exists = DaftarToko::where('kode_toko', $request->kode_toko)
            ->where('kode_agen', $request->kode_agen)
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->with('error', 'Agen dengan kode ' . $request->kode_agen . ' sudah terdaftar untuk toko ini')
                ->withInput()
                ->with('tab', 'agen');
        }

        // Ambil data toko existing untuk di-copy
        $existingToko = DaftarToko::where('kode_toko', $request->kode_toko)->first();
        
        if (!$existingToko) {
            return redirect()->back()
                ->with('error', 'Data toko tidak ditemukan')
                ->with('tab', 'agen');
        }

        try {
            DB::beginTransaction();

            // Buat data toko baru dengan kode_toko yang sama
            $tokoData = [
                'kode_agen' => $request->kode_agen,
                'nama_agen' => $request->nama_agen,
                'kode_toko' => $request->kode_toko,
                'nama_toko' => $existingToko->nama_toko,
                'alamat' => $existingToko->alamat,
                'kota' => $existingToko->kota,
                'pic' => $existingToko->pic,
                'nomor_pic' => $existingToko->nomor_pic,
                'nama_sales' => $existingToko->nama_sales,
                'lokasi_event' => $existingToko->lokasi_event,
                'status' => 1,
                'hadir' => $existingToko->hadir,
                'jumlah_kehadiran' => $existingToko->jumlah_kehadiran,
                'waktu_kehadiran' => $existingToko->waktu_kehadiran,
                'hotel' => null,
                'checkin' => null,
            ];

            DaftarToko::create($tokoData);

            LogAktivitas::create([
                'user_id'    => auth()->user()->id,
                'username'   => auth()->user()->name,
                'aksi'       => 'Tambah Agen',
                'fitur'      => 'Daftar Toko',
                'deskripsi'  => "Menambahkan agen {$request->nama_agen} ({$request->kode_agen}) untuk toko {$request->kode_toko} - {$existingToko->nama_toko}",
                'ip_address' => $request->ip(),
                'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('daftartoko.edit', $existingToko->id)
                ->with('success', 'Agen baru berhasil ditambahkan untuk toko ' . $request->kode_toko)
                ->with('tab', 'agen');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan agen: ' . $e->getMessage())
                ->withInput()
                ->with('tab', 'agen');
        }
    }

    public function removeAgen(Request $request)
    {
        $kodeToko = $request->get('kode_toko');
        $kodeAgen = $request->get('kode_agen');
        $currentId = $request->get('current_id'); // ID toko yang sedang diedit
        
        // Validasi
        if (!$kodeToko || !$kodeAgen) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ], 400);
        }
        
        // Cek apakah ini satu-satunya agen untuk toko ini
        $totalAgen = DaftarToko::where('kode_toko', $kodeToko)
            ->where('status', 1)
            ->count();
        
        if ($totalAgen <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus agen terakhir. Minimal harus ada 1 agen untuk setiap toko.'
            ], 400);
        }
        
        // Cari data toko yang akan dihapus
        $tokoToRemove = DaftarToko::where('kode_toko', $kodeToko)
            ->where('kode_agen', $kodeAgen)
            ->where('status', 1)
            ->first();
        
        if (!$tokoToRemove) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        
        // Jika ini adalah agen yang sedang aktif (current), pilih agen lain sebagai pengganti
        $isCurrent = ($tokoToRemove->id == $currentId);
        $newCurrentId = null;
        
        if ($isCurrent) {
            // Cari agen lain untuk dijadikan current
            $otherAgen = DaftarToko::where('kode_toko', $kodeToko)
                ->where('status', 1)
                ->where('id', '!=', $tokoToRemove->id)
                ->first();
            
            if ($otherAgen) {
                $newCurrentId = $otherAgen->id;
            }
        }
        
        try {
            DB::beginTransaction();
            
            // Soft delete atau update status menjadi 0
            $tokoToRemove->update(['status' => 0]);
            
            // Log aktivitas
            LogAktivitas::create([
                'user_id'    => auth()->user()->id,
                'username'   => auth()->user()->name,
                'aksi'       => 'Hapus Agen',
                'fitur'      => 'Daftar Toko',
                'deskripsi'  => "Menghapus agen {$tokoToRemove->nama_agen} ({$tokoToRemove->kode_agen}) dari toko {$kodeToko}",
                'ip_address' => $request->ip(),
                'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                'created_at' => now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Agen berhasil dihapus',
                'new_current_id' => $newCurrentId,
                'redirect' => $isCurrent ? route('daftartoko.edit', $newCurrentId) : null
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy(Request $request, $id)
    {
        $daftartoko = DaftarToko::findOrFail($id);

        $daftartoko->update(['status' => 0]); 

        LogAktivitas::create([
            'user_id'    => auth()->user()->id,
            'username'   => auth()->user()->name,
            'aksi'       => 'Hapus',
            'fitur'      => 'Daftar Toko',
            'deskripsi'  => "Menonaktifkan data toko {$daftartoko->kode_toko} - {$daftartoko->nama_toko}",
            'ip_address' => $request->ip(),
            'device' => Browser::browserName() . ' on ' . Browser::platformName(),
            'created_at' => now(),
        ]);

        return redirect()->route('daftartoko.index')
            ->with('success', 'Data toko berhasil dihapus');
    }

    public function generateQR(Request $request)
    {
        $lokasiEvents = MasterLokasiEvent::all();
        $selectedLokasi = $request->get('lokasi_event');
        $tokos = collect();
        
        if ($selectedLokasi && $selectedLokasi != 'semua') {
            $tokosRaw = DaftarToko::where('lokasi_event', $selectedLokasi)
                ->where('status', 1) // hanya toko aktif
                ->orderBy('nama_toko', 'asc')
                ->get();
            
            // Grouping data toko yang sama
            $tokos = $this->groupSimilarToko($tokosRaw, $selectedLokasi);
        }
        
        return view('daftartoko.generate-qr', compact('lokasiEvents', 'selectedLokasi', 'tokos'));
    }

    /**
     * Export QR code ke PDF
     */
    public function exportQRPDF(Request $request)
    {
        $lokasiEvent = $request->get('lokasi_event');
        
        if (!$lokasiEvent || $lokasiEvent == 'semua') {
            return redirect()->back()->with('error', 'Silakan pilih lokasi event terlebih dahulu');
        }
        
        $tokosRaw = DaftarToko::where('lokasi_event', $lokasiEvent)
            ->where('status', 1)
            ->orderBy('nama_toko', 'asc')
            ->get();
        
        if ($tokosRaw->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada toko aktif di lokasi event yang dipilih');
        }
        
        // Grouping data toko yang sama
        $tokos = $this->groupSimilarToko($tokosRaw, $lokasiEvent);
        
        if ($tokos->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data toko setelah grouping');
        }
        
        // Generate QR Code sebagai base64 image untuk setiap toko yang sudah di-group
        foreach ($tokos as $toko) {
            // Generate QR code menggunakan kode_toko dari toko dengan ID terkecil
            $qrCodeSvg = QrCode::format('svg')->size(150)->generate($toko->kode_toko);
            $toko->qr_code_base64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
            
            // Tambahkan informasi jumlah duplikat yang digabung
            $toko->jumlah_duplikat = $toko->duplicate_count ?? 1;
            $toko->all_kode_toko = $toko->all_kode_toko ?? [$toko->kode_toko];
        }
        
        $lokasiEventName = $lokasiEvent;
        $date = now()->format('d-m-Y');
        
        $pdf = Pdf::loadView('daftartoko.qr-pdf', compact('tokos', 'lokasiEventName', 'date'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('QR-Code_Toko_' . $lokasiEvent . '_' . $date . '.pdf');
    }

    private function groupSimilarToko($tokosRaw, $lokasiEvent)
    {
        $groupedData = [];
        
        foreach ($tokosRaw as $toko) {
            // Buat unique key berdasarkan kriteria yang ditentukan
            $uniqueKey = strtolower(trim($toko->nama_toko)) . '|' . 
                        strtolower(trim($toko->pic)) . '|' . 
                        strtolower(trim($toko->nomor_pic)) . '|' . 
                        strtolower(trim($toko->kota)) . '|' . 
                        strtolower(trim($lokasiEvent));
            
            if (isset($groupedData[$uniqueKey])) {
                // Jika sudah ada, update data dengan ID terkecil
                $existingData = $groupedData[$uniqueKey];
                
                // Simpan semua kode_toko yang digabung (gunakan array biasa)
                if (!isset($existingData['all_kode_toko'])) {
                    $existingData['all_kode_toko'] = [$existingData['kode_toko']];
                }
                $existingData['all_kode_toko'][] = $toko->kode_toko;
                
                // Jika ID toko saat ini lebih kecil, update data utama
                if ($toko->id < $existingData['id']) {
                    $existingData['id'] = $toko->id;
                    $existingData['kode_toko'] = $toko->kode_toko;
                    $existingData['nama_toko'] = $toko->nama_toko;
                    $existingData['pic'] = $toko->pic;
                    $existingData['nomor_pic'] = $toko->nomor_pic;
                    $existingData['kota'] = $toko->kota;
                    $existingData['alamat'] = $toko->alamat;
                    $existingData['provinsi'] = $toko->provinsi;
                }
                
                // Increment counter duplikat
                $existingData['duplicate_count'] = ($existingData['duplicate_count'] ?? 1) + 1;
                
                $groupedData[$uniqueKey] = $existingData;
            } else {
                // Data baru, konversi ke array
                $groupedData[$uniqueKey] = [
                    'id' => $toko->id,
                    'kode_toko' => $toko->kode_toko,
                    'nama_toko' => $toko->nama_toko,
                    'pic' => $toko->pic,
                    'nomor_pic' => $toko->nomor_pic,
                    'alamat' => $toko->alamat,
                    'provinsi' => $toko->provinsi,
                    'kota' => $toko->kota,
                    'lokasi_event' => $toko->lokasi_event,
                    'status' => $toko->status,
                    'duplicate_count' => 1,
                    'all_kode_toko' => [$toko->kode_toko]
                ];
            }
        }
        
        // Konversi ke collection object (ubah array menjadi object)
        $result = collect(array_values($groupedData))->map(function($item) {
            return (object) $item;
        });
        
        // Urutkan berdasarkan nama_toko
        $result = $result->sortBy('nama_toko')->values();
        
        return $result;
    }

    private function buildUpdateQuery($request, $table)
    {
        if ($table === 'DAFTAR_TOKO') {
            $query = DaftarToko::where('nama_toko', $request->get('nama_toko'))
                ->where('lokasi_event', $request->get('lokasi_event'));
        } else {
            $query = DaftarAgen::where('nama_agen', $request->get('nama_agen'))
                ->where('lokasi_event', $request->get('lokasi_event'));
        }

        $pic = $request->get('pic');
        if (empty($pic)) {
            $query->where(function($q) {
                $q->whereNull('pic')->orWhere('pic', '');
            });
        } else {
            $query->where('pic', $pic);
        }

        $noHp = $request->get('no_hp');
        if (empty($noHp)) {
            $query->where(function($q) {
                $q->whereNull('nomor_pic')->orWhere('nomor_pic', '');
            });
        } else {
            $query->where('nomor_pic', $noHp);
        }

        $kota = $request->get('kota');
        if (empty($kota)) {
            $query->where(function($q) {
                $q->whereNull('kota')->orWhere('kota', '');
            });
        } else {
            $query->where('kota', $kota);
        }

        $kodeAgen = $request->get('kode_agen');
        if (empty($kodeAgen)) {
            $query->where(function($q) {
                $q->whereNull('kode_agen')->orWhere('kode_agen', '');
            });
        } else {
            $query->where('kode_agen', $kodeAgen);
        }

        return $query;
    }

    private function updateField($request, $table, $column, $value)
    {
        $query = $this->buildUpdateQuery($request, $table);
        $query->update([$column => $value]);
    }

    public function updateHotel(Request $request)
    {
        $source = $request->get('source');
        $value = $request->filled('hotel') ? strtoupper($request->input('hotel')) : null;
        $this->updateField($request, $source, 'hotel', $value);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data hotel berhasil diperbarui']);
        }
        return redirect()->back()->with('success', 'Data hotel berhasil diperbarui');
    }

    public function updateNomorKamar(Request $request)
    {
        $source = $request->get('source');
        $value = $request->filled('nomor_kamar_hotel') ? $request->input('nomor_kamar_hotel') : null;
        $this->updateField($request, $source, 'nomor_kamar_hotel', $value);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Nomor kamar berhasil diperbarui']);
        }
        return redirect()->back()->with('success', 'Nomor kamar berhasil diperbarui');
    }

    public function updateJumlahOrang(Request $request)
    {
        $source = $request->get('source');
        $value = $request->filled('jumlah_orang_menginap') ? $request->input('jumlah_orang_menginap') : null;
        $this->updateField($request, $source, 'jumlah_orang_menginap', $value);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Jumlah orang berhasil diperbarui']);
        }
        return redirect()->back()->with('success', 'Jumlah orang berhasil diperbarui');
    }

    public function updateCheckin(Request $request)
    {
        $source = $request->get('source');
        $checkin = $request->has('checkin') ? 'Check in' : null;

        $this->updateField($request, $source, 'checkin', $checkin);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Status checkin berhasil diperbarui']);
        }
        return redirect()->back()->with('success', 'Status checkin berhasil diperbarui');
    }
}