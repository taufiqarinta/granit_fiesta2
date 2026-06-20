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

        $queryToko = DaftarToko::query();
        $queryAgen = DaftarAgen::query();
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

        $dataToko = $queryToko->get();
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
                'kode_toko' => $toko->kode_toko,
                'nama_toko' => $toko->nama_toko,
                'nama_agen' => $toko->nama_agen,
                'kode_agen' => $toko->kode_agen,
                'pic' => $toko->pic,
                'kota' => $toko->kota,
                'no_hp' => $toko->nomor_pic,
                'lokasi_event' => $toko->lokasi_event,
                'hadir' => (int) ($toko->hadir ?? 0),
                'jumlah_kehadiran' => (int) ($toko->jumlah_kehadiran ?? 0),
                'hotel' => $toko->hotel,
                'checkin' => $toko->checkin,
                'doorprize' => $this->getDoorprize($toko->nama_toko, $toko->pic, $toko->nomor_pic, $toko->lokasi_event),
            ];
        }

        foreach ($dataAgen as $agen) {
            $allData[] = [
                'type' => 'AGEN',
                'source' => 'DAFTAR_AGEN',
                'kode_toko' => '-',
                'nama_toko' => $agen->nama_agen,
                'nama_agen' => $agen->nama_agen,
                'kode_agen' => $agen->kode_agen,
                'pic' => $agen->pic,
                'kota' => $agen->kota,
                'no_hp' => $agen->nomor_pic,
                'lokasi_event' => $agen->lokasi_event,
                'hadir' => (int) ($agen->hadir ?? 0),
                'jumlah_kehadiran' => (int) ($agen->jumlah_kehadiran ?? 0),
                'hotel' => $agen->hotel,
                'checkin' => $agen->checkin,
                'doorprize' => '-',
            ];
        }

        foreach ($uniqueOrders as $order) {
            $existsInToko = $dataToko->first(function ($toko) use ($order) {
                return $toko->nama_toko == $order->nama_toko
                    && $toko->pic == $order->pic
                    && $toko->kota == $order->kota
                    && $toko->nomor_pic == $order->no_hp
                    && $toko->lokasi_event == $order->lokasi_event
                    && $toko->kode_agen == $order->kode_agen;
            });

            if ($existsInToko) {
                continue;
            }

            $similarToko = $dataToko->first(function ($toko) use ($order) {
                return $toko->nama_toko == $order->nama_toko
                    && $toko->pic == $order->pic
                    && $toko->kota == $order->kota
                    && $toko->nomor_pic == $order->no_hp;
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
                'kode_toko' => $similarToko->kode_toko ?? '-',
                'nama_toko' => $order->nama_toko,
                'nama_agen' => $namaAgen,
                'kode_agen' => $order->kode_agen,
                'pic' => $order->pic,
                'kota' => $order->kota,
                'no_hp' => $order->no_hp,
                'lokasi_event' => $order->lokasi_event,
                'hadir' => (int) ($similarToko->hadir ?? 0),
                'jumlah_kehadiran' => (int) ($similarToko->jumlah_kehadiran ?? 0),
                'hotel' => $similarToko->hotel ?? null,
                'checkin' => $similarToko->checkin ?? null,
                'doorprize' => $similarToko
                    ? $this->getDoorprize($similarToko->nama_toko, $similarToko->pic, $similarToko->nomor_pic, $similarToko->lokasi_event)
                    : '-',
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
                    'nama_agen' => $item['nama_agen'] ?: '-',
                    'nama_toko' => $item['nama_toko'] ?: '-',
                    'lokasi_event' => $item['lokasi_event'],
                    'kota'     => $item['kota'] ?? null,
                    'hadir' => (int) ($item['hadir'] ?? 0),
                    'jumlah_kehadiran' => (int) ($item['jumlah_kehadiran'] ?? 0),
                    'hotel' => $item['hotel'],
                    'checkin' => $item['checkin'],
                    'doorprize' => $item['doorprize'] ?? '-',
                    'order_point' => (int) ($this->calculateTotalOrder($item) ?? 0),
                    'pic' => $item['pic'] ?? null,
                    'no_hp' => $item['no_hp'] ?? null,
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
        
        // Ambil semua data toko
        $dataToko = $queryToko->get();
        
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
        $headers = ['No', 'Lokasi Event', 'Tipe', 'Sumber Data', 'Nama Agen', 'Nama Toko', 'Hadir', 'Order (Point)', 'Hotel', 'Ditempati', 'Doorprize'];
        
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
            'E' => 35,  // Nama Agen
            'F' => 40,  // Nama Toko
            'G' => 15,  // Hadir
            'H' => 20,  // Order (Point)
            'I' => 25,  // Hotel
            'J' => 15,  // Ditempati
            'K' => 25,  // Doorprize
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
                'jumlah_kehadiran' => $toko->jumlah_kehadiran,
                'hotel' => $toko->hotel,
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
                'nama_toko' => '', // Untuk agen, nama toko kosong
                'nama_agen' => $agen->nama_agen,
                'kode_agen' => $agen->kode_agen,
                'pic' => $agen->pic,
                'kota' => $agen->kota,
                'no_hp' => $agen->nomor_pic,
                'lokasi_event' => $agen->lokasi_event,
                'jumlah_kehadiran' => $agen->jumlah_kehadiran,
                'hotel' => $agen->hotel,
                'checkin' => $agen->checkin,
                'doorprize' => '-',
                'key_group' => 'AGEN_' . $agen->kode_agen,
                'key_unique' => 'AGEN_' . $agen->kode_agen
            ];
        }
        
        // 3. Data dari FormOrder yang TIDAK ada di DaftarToko
        foreach ($uniqueOrders as $order) {
            // Cek apakah data ini sudah ada di daftar toko
            $existsInToko = $dataToko->first(function($toko) use ($order) {
                return $toko->nama_toko == $order->nama_toko &&
                    $toko->pic == $order->pic &&
                    $toko->kota == $order->kota &&
                    $toko->nomor_pic == $order->no_hp &&
                    $toko->lokasi_event == $order->lokasi_event &&
                    $toko->kode_agen == $order->kode_agen;
            });
            
            if (!$existsInToko) {
                $keyGroup = $order->nama_toko . '|' . $order->pic . '|' . $order->kota . '|' . $order->no_hp;
                $keyUnique = $keyGroup . '|' . $order->lokasi_event;
                
                // Cari data toko yang sama (nama_toko, pic, kota, no_hp sama) tapi lokasi_event atau agen berbeda
                $similarToko = $dataToko->first(function($toko) use ($order) {
                    return $toko->nama_toko == $order->nama_toko &&
                        $toko->pic == $order->pic &&
                        $toko->kota == $order->kota &&
                        $toko->nomor_pic == $order->no_hp;
                });
                
                // Jika ditemukan toko yang sama, gunakan data kehadiran, hotel, checkin dari toko tersebut
                if ($similarToko) {
                    $jumlahKehadiran = $similarToko->jumlah_kehadiran;
                    $hotel = $similarToko->hotel;
                    $checkin = $similarToko->checkin;
                    $doorprize = $this->getDoorprize($similarToko->nama_toko, $similarToko->pic, $similarToko->nomor_pic, $similarToko->lokasi_event);
                } else {
                    // Jika tidak ditemukan toko yang sama, default 0/kosong
                    $jumlahKehadiran = 0;
                    $hotel = '';
                    $checkin = '';
                    $doorprize = '-';
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
                    'jumlah_kehadiran' => $jumlahKehadiran,
                    'hotel' => $hotel,
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
        $mergeAreas = [];
        
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
                
                // Jika ini group toko dan memiliki lebih dari 1 item, catat untuk merge
                if ($isTokoGroup && count($groupItems) > 1) {
                    $startRow = $row;
                    $endRow = $row + count($groupItems) - 1;
                    
                    // Simpan info merge
                    $mergeAreas[] = [
                        'startRow' => $startRow,
                        'endRow' => $endRow,
                        'groupKey' => $groupKey
                    ];
                }
                
                // Tampilkan semua items dalam group
                foreach ($groupItems as $item) {
                    // Hitung total order
                    $totalOrder = $this->calculateTotalOrder($item);
                    
                    // Isi data ke sheet
                    $sheet->setCellValue('A' . $row, $counter);
                    $sheet->setCellValue('B' . $row, ($item['type'] ?? '') === 'AGEN' ? 'Seluruh Lokasi' : ($item['lokasi_event'] ?? ''));
                    $sheet->setCellValue('C' . $row, $item['type']);
                    $sheet->setCellValue('D' . $row, $item['source']);
                    $sheet->setCellValue('E' . $row, $item['nama_agen'] ?? '');
                    $sheet->setCellValue('F' . $row, $item['nama_toko'] ?: $item['nama_agen']);
                    $sheet->setCellValue('G' . $row, $item['jumlah_kehadiran'] ?? 0);
                    $sheet->setCellValue('H' . $row, $totalOrder ?? 0);
                    $sheet->setCellValue('I' . $row, $item['hotel'] ?? '');
                    $sheet->setCellValue('J' . $row, $item['checkin'] ?? '');
                    $sheet->setCellValue('K' . $row, $item['doorprize'] ?? '-');
                    
                    // Beri warna kuning untuk toko yang sama (lebih dari 1 dalam group)
                    if ($isTokoGroup && count($groupItems) > 1) {
                        $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FFF2CC'] // Kuning
                            ]
                        ]);
                    }
                    
                    $row++;
                    $counter++;
                    
                    // Flush memory setiap 100 record
                    if ($counter % 100 == 0) {
                        gc_collect_cycles();
                    }
                }
            }
        }
        
        // ==================== APPLY MERGE YANG AMAN ====================
        
        foreach ($mergeAreas as $mergeArea) {
            $startRow = $mergeArea['startRow'];
            $endRow = $mergeArea['endRow'];
            
            // Pastikan hanya merge jika benar-benar dalam range yang valid
            if ($endRow > $startRow && $endRow <= $row - 1) {
                // Ambil data di startRow untuk validasi
                $namaTokoStart = $sheet->getCell('F' . $startRow)->getValue();
                $namaTokoEnd = $sheet->getCell('F' . $endRow)->getValue();
                
                // Hanya merge jika nama toko sama (double check)
                if ($namaTokoStart === $namaTokoEnd) {
                    // Merge kolom G (Hadir)
                    $sheet->mergeCells('G' . $startRow . ':G' . $endRow);
                    $sheet->getStyle('G' . $startRow)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                    
                    // Copy value dari row pertama ke merged cell
                    $hadirValue = $sheet->getCell('G' . $startRow)->getValue();
                    $sheet->setCellValue('G' . $startRow, $hadirValue);
                    
                    // Merge kolom I (Hotel)
                    $sheet->mergeCells('I' . $startRow . ':I' . $endRow);
                    $sheet->getStyle('I' . $startRow)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                    
                    // Copy value hotel
                    $hotelValue = $sheet->getCell('I' . $startRow)->getValue();
                    $sheet->setCellValue('I' . $startRow, $hotelValue);
                    
                    // Merge kolom J (Ditempati)
                    $sheet->mergeCells('J' . $startRow . ':J' . $endRow);
                    $sheet->getStyle('J' . $startRow)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                    
                    // Copy value checkin
                    $checkinValue = $sheet->getCell('J' . $startRow)->getValue();
                    $sheet->setCellValue('J' . $startRow, $checkinValue);
                    
                    // Merge kolom K (Doorprize)
                    $sheet->mergeCells('K' . $startRow . ':K' . $endRow);
                    $sheet->getStyle('K' . $startRow)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                    
                    // Copy value doorprize
                    $doorprizeValue = $sheet->getCell('K' . $startRow)->getValue();
                    $sheet->setCellValue('K' . $startRow, $doorprizeValue);
                }
            }
        }
        
        // ==================== STYLING ====================
        
        $lastRow = $row - 1;
        if ($lastRow > 1) {
            // Border untuk semua data
            $sheet->getStyle('A2:K' . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD']
                    ]
                ]
            ]);
            
            // Format angka untuk kolom Order
            $sheet->getStyle('H2:H' . $lastRow)
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
            $sheet->getStyle('G2:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H2:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('I2:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J2:J' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K2:K' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Untuk merged cells, pastikan alignment benar
            foreach ($mergeAreas as $mergeArea) {
                if ($mergeArea['endRow'] > $mergeArea['startRow']) {
                    $sheet->getStyle('G' . $mergeArea['startRow'])
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                        
                    $sheet->getStyle('I' . $mergeArea['startRow'])
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                        
                    $sheet->getStyle('J' . $mergeArea['startRow'])
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                        
                    $sheet->getStyle('K' . $mergeArea['startRow'])
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }
            }
        }
        
        // Set alignment untuk header
        $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Auto size kolom
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
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
        // Generate kode toko otomatis
        $lastToko = DaftarToko::orderBy('id', 'desc')->first();
        $nextNumber = 1;
        
        if ($lastToko && preg_match('/^T(\d+)$/', $lastToko->kode_toko, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }
        
        $kodeToko = 'T' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $provinsis = Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')->get();
        
        $lokasiEvents = MasterLokasiEvent::where('status', 'Aktif')->get();
        
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
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'kode_agen' => 'required|max:50',
            'nama_agen' => 'required|max:255',
            'nama_toko' => 'required|max:255',
            'alamat' => 'required',
            // 'provinsi' => 'required|max:100',
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
            // 'provinsi.required' => 'Provinsi wajib diisi',
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

        // Generate kode toko dengan transaction untuk menghindari race condition
        try {
            DB::beginTransaction();

            // Dapatkan kode toko terakhir dengan lock untuk menghindari race condition
            $lastToko = DaftarToko::lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastToko && preg_match('/^T(\d+)$/', $lastToko->kode_toko, $matches)) {
                $nextNumber = (int)$matches[1] + 1;
            }

            $kodeToko = 'T' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Buat data toko
            $tokoData = [
                'kode_agen' => $request->kode_agen,
                'nama_agen' => $request->nama_agen,
                'kode_toko' => $kodeToko,
                'nama_toko' => $request->nama_toko,
                'alamat' => $request->alamat,
                // 'provinsi' => $request->provinsi,
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

            // LogAktivitas::create([
            //     'user_id'    => auth()->user()->id,
            //     'username'   => auth()->user()->name,
            //     'aksi'       => 'Tambah',
            //     'fitur'      => 'Daftar Toko',
            //     'deskripsi'  => "Menambahkan Data Toko {$kodeToko} - {$request->nama_toko}",
            //     'ip_address' => $request->ip(),
            //     'device' => Browser::browserName() . ' on ' . Browser::platformName(),
            //     'created_at' => now(),
            // ]);

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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DaftarToko $daftartoko)
    {
        $provinsis = Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')->get();
        $lokasiEvents = MasterLokasiEvent::where('status', 'Aktif')->get();

        // Ambil semua agen jika department SLS
        $agenList = [];
        $isSalesDepartment = auth()->user()->department === 'SLS';
        
        if ($isSalesDepartment) {
            $agenList = DaftarAgen::select('kode_agen', 'nama_agen')->orderBy('nama_agen', 'asc')->get();
        }

        return view('daftartoko.edit', compact('daftartoko', 'provinsis', 'lokasiEvents', 'agenList', 'isSalesDepartment'));
    }

    public function update(Request $request, DaftarToko $daftartoko)
    {
        $validator = Validator::make($request->all(), [
            'kode_agen' => 'required|max:50',
            'nama_agen' => 'required|max:255',
            'kode_toko' => 'required|max:50|unique:daftar_toko,kode_toko,' . $daftartoko->id,
            'nama_toko' => 'required|max:255',
            'alamat' => 'required',
            // 'provinsi' => 'required|max:100',
            'kota' => 'required|max:100',
            'pic' => 'required|max:255',
            'nomor_pic' => 'required|max:20',
            'nama_sales' => 'required|max:255',
            'lokasi_event' => 'required|max:100',
            'status' => 'required|in:0,1' // Validasi untuk status
        ], [
            'kode_agen.required' => 'Kode agen wajib diisi',
            'nama_agen.required' => 'Nama agen wajib diisi',
            'kode_toko.required' => 'Kode toko wajib diisi',
            'kode_toko.unique' => 'Kode toko sudah digunakan',
            'nama_toko.required' => 'Nama toko wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            // 'provinsi.required' => 'Provinsi wajib diisi',
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

        $daftartoko->update($request->all());

        // LogAktivitas::create([
        //     'user_id'    => auth()->user()->id,
        //     'username'   => auth()->user()->name,
        //     'aksi'       => 'Ubah',
        //     'fitur'      => 'Daftar Toko',
        //     'deskripsi'  => "Mengubah data toko dengan kode toko {$daftartoko->kode_toko}",
        //     'ip_address' => $request->ip(),
        //     'device' => Browser::browserName() . ' on ' . Browser::platformName(),
        //     'created_at' => now(),
        // ]);

        return redirect()->route('daftartoko.index')
            ->with('success', 'Data toko berhasil diperbarui');
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

    public function updateHotel(Request $request)
    {
        $type = $request->get('type');
        $source = $request->get('source');
        $hotel = strtoupper($request->get('hotel', ''));

        if ($source === 'DAFTAR_TOKO') {
            $query = DaftarToko::where('nama_toko', $request->get('nama_toko'))
                ->where('lokasi_event', $request->get('lokasi_event'));

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

            // ❌ Hapus filter kode_agen untuk DAFTAR_TOKO
            // karena 1 toko fisik bisa punya banyak kode_agen

            $count = $query->count();
            $updated = $query->update(['hotel' => $hotel]);

            LogAktivitas::create([
                'user_id' => auth()->user()->id,
                'username' => auth()->user()->name,
                'aksi' => 'Ubah',
                'fitur' => 'Daftar Toko',
                'deskripsi' => "Memperbarui hotel data toko {$request->get('nama_toko')} di lokasi {$request->get('lokasi_event')}",
                'ip_address' => $request->ip(),
                'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                'created_at' => now(),
            ]);
            
        } elseif ($source === 'DAFTAR_AGEN') {
                $query = DaftarAgen::where('nama_agen', $request->get('nama_agen'))
                    ->where('lokasi_event', $request->get('lokasi_event'));
            
                // Handle NULL/empty pic
                $pic = $request->get('pic');
                if (empty($pic)) {
                    $query->where(function($q) {
                        $q->whereNull('pic')->orWhere('pic', '');
                    });
                } else {
                    $query->where('pic', $pic);
                }
            
                // Handle NULL/empty no_hp
                $noHp = $request->get('no_hp');
                if (empty($noHp)) {
                    $query->where(function($q) {
                        $q->whereNull('nomor_pic')->orWhere('nomor_pic', '');
                    });
                } else {
                    $query->where('nomor_pic', $noHp);
                }
            
                // Handle NULL/empty kode_agen
                $kodeAgen = $request->get('kode_agen');
                if (empty($kodeAgen)) {
                    $query->where(function($q) {
                        $q->whereNull('kode_agen')->orWhere('kode_agen', '');
                    });
                } else {
                    $query->where('kode_agen', $kodeAgen);
                }
            
            $count = $query->count();
            
            $updated = $query->update(['hotel' => $hotel]);

                        LogAktivitas::create([
                            'user_id' => auth()->user()->id,
                            'username' => auth()->user()->name,
                            'aksi' => 'Ubah',
                            'fitur' => 'Daftar Toko',
                            'deskripsi' => "Memperbarui hotel data agen {$request->get('kode_agen')} - {$request->get('nama_agen')} di lokasi {$request->get('lokasi_event')}",
                            'ip_address' => $request->ip(),
                            'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                            'created_at' => now(),
                        ]);

            // Fallback for agen: loose match by nama_agen
            if ($count === 0) {
                $looseMatches = DaftarAgen::where('lokasi_event', $request->get('lokasi_event'))
                    ->where('nama_agen', 'like', '%' . $request->get('nama_agen') . '%')
                    ->get();
                Log::info('DAFTAR_AGEN loose match count: ' . $looseMatches->count());
                if ($looseMatches->count() === 1) {
                    $agen = $looseMatches->first();
                    Log::info('DAFTAR_AGEN loose match record', [
                        'id' => $agen->id ?? null,
                        'nama_agen' => $agen->nama_agen,
                        'pic' => $agen->pic,
                        'nomor_pic' => $agen->nomor_pic,
                        'kode_agen' => $agen->kode_agen,
                    ]);
                    $agen->hotel = $hotel;
                    $agen->save();
                    LogAktivitas::create([
                        'user_id' => auth()->user()->id,
                        'username' => auth()->user()->name,
                        'aksi' => 'Ubah',
                        'fitur' => 'Daftar Toko',
                        'deskripsi' => "Memperbarui hotel data agen {$agen->kode_agen} - {$agen->nama_agen} di lokasi {$request->get('lokasi_event')}",
                        'ip_address' => $request->ip(),
                        'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                        'created_at' => now(),
                    ]);
                    Log::info('DAFTAR_AGEN loose-match update performed, id: ' . ($agen->id ?? 'n/a'));
                } else {
                    $candidates = $looseMatches->take(5)->map(function($r){
                        return [
                            'id' => $r->id ?? null,
                            'nama_agen' => $r->nama_agen,
                            'pic' => $r->pic,
                            'nomor_pic' => $r->nomor_pic,
                            'kode_agen' => $r->kode_agen,
                        ];
                    });
                    Log::info('DAFTAR_AGEN loose match candidates: ' . $candidates->toJson());
                }

                        LogAktivitas::create([
                            'user_id' => auth()->user()->id,
                            'username' => auth()->user()->name,
                            'aksi' => 'Ubah',
                            'fitur' => 'Daftar Toko',
                            'deskripsi' => "Memperbarui checkin data toko {$request->get('nama_toko')} di lokasi {$request->get('lokasi_event')}",
                            'ip_address' => $request->ip(),
                            'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                            'created_at' => now(),
                        ]);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data hotel berhasil diperbarui']);
        }
        return redirect()->back()->with('success', 'Data hotel berhasil diperbarui');
    }

    public function updateCheckin(Request $request)
    {
        $type = $request->get('type');
        $source = $request->get('source');
        $checkin = $request->has('checkin') ? 'Check in' : null;
        
        Log::info('updateCheckin called', [
            'source' => $source,
            'nama_toko' => $request->get('nama_toko'),
            'nama_agen' => $request->get('nama_agen'),
            'pic' => $request->get('pic'),
            'no_hp' => $request->get('no_hp'),
            'lokasi_event' => $request->get('lokasi_event'),
            'kode_agen' => $request->get('kode_agen'),
            'checkin' => $checkin,
        ]);

        if ($source === 'DAFTAR_TOKO') {
            $query = DaftarToko::where('nama_toko', $request->get('nama_toko'))
                ->where('lokasi_event', $request->get('lokasi_event'));

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

            // ❌ Hapus filter kode_agen untuk DAFTAR_TOKO
            // karena 1 toko fisik bisa punya banyak kode_agen

            $count = $query->count();
            $updated = $query->update(['checkin' => $checkin]); 

            LogAktivitas::create([
                'user_id' => auth()->user()->id,
                'username' => auth()->user()->name,
                'aksi' => 'Ubah',
                'fitur' => 'Daftar Toko',
                'deskripsi' => "Memperbarui checkin data toko {$request->get('nama_toko')} di lokasi {$request->get('lokasi_event')}",
                'ip_address' => $request->ip(),
                'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                'created_at' => now(),
            ]);
            
        } elseif ($source === 'DAFTAR_AGEN') {
            $query = DaftarAgen::where('nama_agen', $request->get('nama_agen'))
                ->where('lokasi_event', $request->get('lokasi_event'));
            
            // Handle NULL/empty pic
            $pic = $request->get('pic');
            if (empty($pic)) {
                $query->where(function($q) {
                    $q->whereNull('pic')->orWhere('pic', '');
                });
            } else {
                $query->where('pic', $pic);
            }
            
            // Handle NULL/empty no_hp
            $noHp = $request->get('no_hp');
            if (empty($noHp)) {
                $query->where(function($q) {
                    $q->whereNull('nomor_pic')->orWhere('nomor_pic', '');
                });
            } else {
                $query->where('nomor_pic', $noHp);
            }
            
            // Handle NULL/empty kode_agen
            $kodeAgen = $request->get('kode_agen');
            if (empty($kodeAgen)) {
                $query->where(function($q) {
                    $q->whereNull('kode_agen')->orWhere('kode_agen', '');
                });
            } else {
                $query->where('kode_agen', $kodeAgen);
            }
            
            $count = $query->count();
            Log::info('DAFTAR_AGEN query count: ' . $count);
            Log::info('DAFTAR_AGEN SQL: ' . $query->toSql());
            
            $updated = $query->update(['checkin' => $checkin]);
            LogAktivitas::create([
                'user_id' => auth()->user()->id,
                'username' => auth()->user()->name,
                'aksi' => 'Ubah',
                'fitur' => 'Daftar Toko',
                'deskripsi' => "Memperbarui checkin data agen {$request->get('kode_agen')} - {$request->get('nama_agen')} di lokasi {$request->get('lokasi_event')}",
                'ip_address' => $request->ip(),
                'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                'created_at' => now(),
            ]);
            Log::info('DAFTAR_AGEN updated rows: ' . $updated);

            // Fallback for agen: loose match by nama_agen
            if ($count === 0) {
                $looseMatches = DaftarAgen::where('lokasi_event', $request->get('lokasi_event'))
                    ->where('nama_agen', 'like', '%' . $request->get('nama_agen') . '%')
                    ->get();
                Log::info('DAFTAR_AGEN loose match count: ' . $looseMatches->count());
                if ($looseMatches->count() === 1) {
                    $agen = $looseMatches->first();
                    Log::info('DAFTAR_AGEN loose match record', [
                        'id' => $agen->id ?? null,
                        'nama_agen' => $agen->nama_agen,
                        'pic' => $agen->pic,
                        'nomor_pic' => $agen->nomor_pic,
                        'kode_agen' => $agen->kode_agen,
                    ]);
                    $agen->checkin = $checkin;
                    $agen->save();
                    LogAktivitas::create([
                        'user_id' => auth()->user()->id,
                        'username' => auth()->user()->name,
                        'aksi' => 'Ubah',
                        'fitur' => 'Daftar Toko',
                        'deskripsi' => "Memperbarui checkin data agen {$agen->kode_agen} - {$agen->nama_agen} di lokasi {$request->get('lokasi_event')}",
                        'ip_address' => $request->ip(),
                        'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                        'created_at' => now(),
                    ]);
                    Log::info('DAFTAR_AGEN loose-match checkin update performed, id: ' . ($agen->id ?? 'n/a'));
                } else {
                    $candidates = $looseMatches->take(5)->map(function($r){
                        return [
                            'id' => $r->id ?? null,
                            'nama_agen' => $r->nama_agen,
                            'pic' => $r->pic,
                            'nomor_pic' => $r->nomor_pic,
                            'kode_agen' => $r->kode_agen,
                        ];
                    });
                    Log::info('DAFTAR_AGEN loose match candidates: ' . $candidates->toJson());
                }
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Status checkin berhasil diperbarui']);
        }
        return redirect()->back()->with('success', 'Status checkin berhasil diperbarui');
    }
}