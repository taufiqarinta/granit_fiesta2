<?php

namespace App\Http\Controllers;

use App\Models\DaftarAgen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Exports\DaftarTokoExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\FormOrder;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\LogAktivitas;
use App\Models\Merk;
use App\Models\UsersMerks;
use App\Models\MasterLokasiEvent;
use App\Models\Wilayah;
use Browser;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Imports\DaftarAgenImport;

class DaftarAgenController extends Controller
{
    
    // public function index(Request $request)
    // {
    //     $search = $request->get('search');
        
    //     $query = DaftarAgen::query();
        
    //     if ($search) {
    //         $query->where(function($q) use ($search) {
    //             $q->where('nama_agen', 'like', "%{$search}%")
    //             ->orWhere('kode_agen', 'like', "%{$search}%")
    //             ->orWhere('pic', 'like', "%{$search}%")
    //             ->orWhere('kota', 'like', "%{$search}%");
    //         });
    //     }
        
    //     // Load data agen
    //     $agens = $query->orderBy('created_at', 'desc')
    //                 ->paginate(10);
        
    //     // Ambil nama provinsi dan kota dari tabel wilayah
    //     $provinsiCodes = $agens->pluck('provinsi')->unique()->filter();
    //     $kotaCodes = $agens->pluck('kota')->unique()->filter();
        
    //     $wilayahData = Wilayah::whereIn('kode', $provinsiCodes)
    //                         ->orWhereIn('kode', $kotaCodes)
    //                         ->get()
    //                         ->keyBy('kode');
        
    //     // Tambahkan nama provinsi dan kota ke setiap agen
    //     $agens->getCollection()->transform(function ($agen) use ($wilayahData) {
    //         $agen->provinsi_name = $wilayahData[$agen->provinsi]->nama ?? $agen->provinsi;
    //         $agen->kota_name = $wilayahData[$agen->kota]->nama ?? $agen->kota;
    //         return $agen;
    //     });
        
    //     return view('daftaragen.index', compact('agens', 'search'));
    // }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $lokasiEvent = $request->get('lokasi_event');
        
        $query = DaftarAgen::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_agen', 'like', "%{$search}%")
                ->orWhere('kode_agen', 'like', "%{$search}%")
                ->orWhere('pic', 'like', "%{$search}%")
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
        
        // Load data agen
        $agens = $query->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->appends($request->query());
        
        // Ambil nama provinsi dan kota dari tabel wilayah
        $provinsiCodes = $agens->pluck('provinsi')->unique()->filter();
        $kotaCodes = $agens->pluck('kota')->unique()->filter();
        
        $wilayahData = Wilayah::whereIn('kode', $provinsiCodes)
                            ->orWhereIn('kode', $kotaCodes)
                            ->get()
                            ->keyBy('kode');
        
        // Tambahkan nama provinsi dan kota ke setiap agen
        $agens->getCollection()->transform(function ($agen) use ($wilayahData) {
            $agen->provinsi_name = $wilayahData[$agen->provinsi]->nama ?? $agen->provinsi;
            $agen->kota_name = $wilayahData[$agen->kota]->nama ?? $agen->kota;
            return $agen;
        });
        
        return view('daftaragen.index', compact('agens', 'search', 'lokasiEvents', 'lokasiEvent', 'defaultLokasi'));
    }

    public function export()
    {
        return Excel::download(new DaftarTokoExport, 'daftar-toko-' . date('Y-m-d') . '.xlsx');
    }

    public function exportAgenExcel(Request $request)
    {
        // Tingkatkan memory limit
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        
        $search = $request->query('search');
        $lokasiEvent = $request->query('lokasi_event');
        
        $query = DaftarAgen::query();
        
        // Filter pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_agen', 'like', "%{$search}%")
                ->orWhere('kode_agen', 'like', "%{$search}%")
                ->orWhere('kode_agen', 'like', "%{$search}%")
                ->orWhere('pic', 'like', "%{$search}%")
                ->orWhere('lokasi_event', 'like', "%{$search}%")
                ->orWhere('kota', 'like', "%{$search}%");
            });
        }
        
        // Filter lokasi event
        if ($lokasiEvent && $lokasiEvent != 'semua') {
            $query->where('lokasi_event', $lokasiEvent);
        }
        
        // Ambil data dengan grouping
        $data = $query->selectRaw('
                nama_agen,
                pic,
                kota,
                nomor_pic,
                lokasi_event,
                kode_agen,
                MAX(jumlah_kehadiran) as jumlah_kehadiran,
                MAX(hotel) as hotel,
                MAX(checkin) as checkin
            ')
            ->groupBy('nama_agen', 'pic', 'kota', 'nomor_pic', 'lokasi_event', 'kode_agen')
            ->orderBy('nama_agen', 'asc')
            ->cursor();
        
        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set judul
        $sheet->setTitle('Tracking Agen');
        
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
        $columnWidths = [
            'A' => 8,   // No
            'B' => 40,  // Nama Toko
            'C' => 15,  // Hadir
            'D' => 20,  // Order
            'E' => 25,  // Hotel
            'F' => 15,  // Ditempati
            'G' => 25,  // Doorprize
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        // Isi data
        $row = 2;
        $counter = 1;
        
        foreach ($data as $agen) {
            // Hitung total order berdasarkan kode_agen saja
            $totalOrder = FormOrder::where('kode_agen', $agen->kode_agen)
                ->sum('total_point');
            
            // Isi data ke sheet
            $sheet->setCellValue('A' . $row, $counter);
            $sheet->setCellValue('B' . $row, $agen->nama_agen);
            $sheet->setCellValue('C' . $row, $agen->jumlah_kehadiran ?? 0);
            $sheet->setCellValue('D' . $row, $totalOrder ?? 0);
            $sheet->setCellValue('E' . $row, $agen->hotel ?? '');
            $sheet->setCellValue('F' . $row, $agen->checkin ?? '');
            $sheet->setCellValue('G' . $row, '-'); // Doorprize selalu "-"
            
            // Styling untuk baris data
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD']
                    ]
                ]
            ];
            
            // Zebra pattern
            if ($row % 2 == 0) {
                $styleArray['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8F9FA']
                ];
            }
            
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($styleArray);
            
            // Alignment khusus untuk kolom angka
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $row++;
            $counter++;
            
            // Flush memory setiap 100 record
            if ($counter % 100 == 0) {
                gc_collect_cycles();
            }
        }
        
        // Format angka untuk kolom Order
        $lastRow = $row - 1;
        if ($lastRow > 1) {
            $sheet->getStyle('D2:D' . $lastRow)
                ->getNumberFormat()
                ->setFormatCode('#,##0');
        }
        
        // Set alignment untuk header
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Nama file
        $fileName = 'tracking-agen-' . date('Y-m-d') . '.xlsx';
        
        // Simpan ke temporary file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'tracking_agen_');
        $writer->save($tempFile);
        
        // Return download response
        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function create()
    {
        // Generate kode agen otomatis
        $lastAgen = DaftarAgen::orderBy('kode_agen', 'desc')->first();
        $nextNumber = 1;
        
        if ($lastAgen && preg_match('/^C(\d+)$/', $lastAgen->kode_agen, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }
        
        $kodeAgen = 'C' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $provinsis = Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')->get();
        $lokasiEvents = MasterLokasiEvent::where('status', 'Aktif')->get();
        
        // Ambil semua merk yang tersedia
        $merks = Merk::orderBy('name')->get();

        return view('daftaragen.create', compact('provinsis', 'kodeAgen', 'lokasiEvents', 'merks'));
    }

    // public function create()
    // {
    //     // Generate kode agen otomatis
    //     $lastAgen = DaftarAgen::orderBy('kode_agen', 'desc')->first();
    //     $nextNumber = 1;
        
    //     if ($lastAgen && preg_match('/^C(\d+)$/', $lastAgen->kode_agen, $matches)) {
    //         $nextNumber = (int)$matches[1] + 1;
    //     }
        
    //     $kodeAgen = 'C' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    //     $provinsis = Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')->get();

    //     $lokasiEvents = MasterLokasiEvent::where('status', 'Aktif')->get();

        
    //     return view('daftaragen.create', compact('provinsis', 'kodeAgen', 'lokasiEvents'));
    // }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'kode_agen' => 'required|max:50',
    //         'nama_agen' => 'required|max:255',
    //         'alamat' => 'required',
    //         'provinsi' => 'required|max:100',
    //         'kota' => 'required|max:100',
    //         'pic' => 'required|max:255',
    //         'nomor_pic' => 'required|max:20',
    //         'lokasi_event' => 'required|max:100'
    //     ], [
    //         'kode_agen.required' => 'Kode agen wajib diisi',
    //         'nama_agen.required' => 'Nama agen wajib diisi',
    //         'alamat.required' => 'Alamat wajib diisi',
    //         'provinsi.required' => 'Provinsi wajib diisi',
    //         'kota.required' => 'Kota wajib diisi',
    //         'pic.required' => 'PIC wajib diisi',
    //         'nomor_pic.required' => 'Nomor PIC wajib diisi',
    //         'lokasi_event.required' => 'Lokasi event wajib diisi'
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->back()
    //             ->withErrors($validator)
    //             ->withInput();
    //     }

    //     // Generate kode agen dengan transaction untuk menghindari race condition
    //     try {
    //         DB::beginTransaction();

    //         // Dapatkan kode agen terakhir dengan lock untuk menghindari race condition
    //         $lastAgen = DaftarAgen::lockForUpdate()
    //             ->orderBy('kode_agen', 'desc')
    //             ->first();

    //         $nextNumber = 1;
    //         if ($lastAgen && preg_match('/^C(\d+)$/', $lastAgen->kode_agen, $matches)) {
    //             $nextNumber = (int)$matches[1] + 1;
    //         }

    //         $kodeAgen = 'C' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    //         // Buat data agen
    //         $agenData = [
    //             'kode_agen' => $kodeAgen,
    //             'nama_agen' => $request->nama_agen,
    //             'alamat' => $request->alamat,
    //             'provinsi' => $request->provinsi,
    //             'kota' => $request->kota,
    //             'pic' => $request->pic,
    //             'nomor_pic' => $request->nomor_pic,
    //             'lokasi_event' => $request->lokasi_event,
    //             'status' => 1,
    //             'hadir' => 0,
    //             'jumlah_kehadiran' => 0,
    //         ];

    //         DaftarAgen::create($agenData);

    //         if (isset($validated['merks'])) {
    //             foreach ($validated['merks'] as $merkId) {
    //                 UsersMerks::create([
    //                     'id_customer' => $kodeAgen,
    //                     'id_merks' => $merkId,
    //                     'created_at' => now(),
    //                     'updated_at' => now()
    //                 ]);
    //             }
    //         }

    //         // LogAktivitas::create([
    //         //     'user_id'    => auth()->user()->id,
    //         //     'username'   => auth()->user()->name,
    //         //     'aksi'       => 'Tambah',
    //         //     'fitur'      => 'Daftar Agen',
    //         //     'deskripsi'  => "Menambahkan Data Agen {$kodeAgen} - {$request->nama_agen}",
    //         //     'ip_address' => $request->ip(),
    //         //     'device' => Browser::browserName() . ' on ' . Browser::platformName(),
    //         //     'created_at' => now(),
    //         // ]);

    //         DB::commit();

    //         return redirect()->route('daftaragen.index')
    //             ->with('success', 'Data agen berhasil ditambahkan');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
            
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
    //             ->withInput();
    //     }
    // }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_agen' => 'required|max:50|unique:daftar_agen,kode_agen',
            'nama_agen' => 'required|max:255',
            'alamat' => 'required',
            'kota' => 'required|max:100',
            'pic' => 'required|max:255',
            'nomor_pic' => 'required|max:20',
            'lokasi_event' => 'required|max:100',
            'merks' => 'required|array',
            'merks.*' => 'exists:merks,id'
        ], [
            'kode_agen.required' => 'Kode agen wajib diisi',
            'kode_agen.unique' => 'Kode agen sudah digunakan',
            'nama_agen.required' => 'Nama agen wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'kota.required' => 'Kota wajib diisi',
            'pic.required' => 'PIC wajib diisi',
            'nomor_pic.required' => 'Nomor PIC wajib diisi',
            'lokasi_event.required' => 'Lokasi event wajib diisi',
            'merks.required' => 'Merk wajib dipilih',
            'merks.array' => 'Format merk tidak valid',
            'merks.*.exists' => 'Merk yang dipilih tidak valid'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Ambil kode agen dari request
            $kodeAgen = strtoupper(trim($request->kode_agen));

            // Cek apakah kode agen sudah ada
            $existingAgen = DaftarAgen::where('kode_agen', $kodeAgen)->first();
            if ($existingAgen) {
                return redirect()->back()
                    ->withErrors(['kode_agen' => 'Kode agen sudah digunakan'])
                    ->withInput();
            }

            // Buat data agen
            $agenData = [
                'kode_agen' => $kodeAgen,
                'nama_agen' => strtoupper(trim($request->nama_agen)),
                'alamat' => strtoupper(trim($request->alamat)),
                'kota' => strtoupper(trim($request->kota)),
                'pic' => strtoupper(trim($request->pic)),
                'nomor_pic' => trim($request->nomor_pic),
                'lokasi_event' => $request->lokasi_event,
                'status' => 1,
                'hadir' => 0,
                'jumlah_kehadiran' => 0,
            ];

            // Simpan data agen
            $agen = DaftarAgen::create($agenData);

            // Simpan merks yang dipilih
            if ($request->has('merks') && is_array($request->merks)) {
                foreach ($request->merks as $merkId) {
                    UsersMerks::create([
                        'id_customer' => $kodeAgen, // Gunakan kode_agen sebagai referensi
                        'id_merks' => $merkId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('daftaragen.index')
                ->with('success', 'Data agen berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    // public function show($id)
    // {
    //     $agen = DaftarAgen::findOrFail($id);
        
    //     // Tambahkan nama provinsi dan kota jika diperlukan
    //     $provinsi = Wilayah::where('kode', $agen->provinsi)->first();
    //     $kota = Wilayah::where('kode', $agen->kota)->first();
        
    //     $agen->provinsi_nama = $provinsi->nama ?? $agen->provinsi;
    //     $agen->kota_nama = $kota->nama ?? $agen->kota;
        
    //     return view('daftaragen.show', compact('agen'));
    // }

    public function show($id)
    {
        $agen = DaftarAgen::with(['merks'])->findOrFail($id);
        
        // Tambahkan nama provinsi dan kota jika diperlukan
        $provinsi = Wilayah::where('kode', $agen->provinsi)->first();
        $kota = Wilayah::where('kode', $agen->kota)->first();
        
        $agen->provinsi_nama = $provinsi->nama ?? $agen->provinsi;
        $agen->kota_nama = $kota->nama ?? $agen->kota;
        
        // Ambil data merk yang dimiliki agen
        $merks = $agen->merks;
        
        return view('daftaragen.show', compact('agen', 'merks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit($id)
    // {
    //     $agen = DaftarAgen::findOrFail($id);
    //     $provinsis = Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')->get();

    //     $lokasiEvents = MasterLokasiEvent::where('status', 'Aktif')->get();

        
    //     return view('daftaragen.edit', compact('agen', 'provinsis', 'lokasiEvents'));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, $id)
    // {
    //     $agen = DaftarAgen::findOrFail($id);

    //     $validator = Validator::make($request->all(), [
    //         'kode_agen' => 'required|max:50',
    //         'nama_agen' => 'required|max:255',
    //         'alamat' => 'required',
    //         'provinsi' => 'required|max:100',
    //         'kota' => 'required|max:100',
    //         'pic' => 'required|max:255',
    //         'nomor_pic' => 'required|max:20',
    //         'lokasi_event' => 'required|max:100',
    //         'status' => 'required|in:0,1'
    //     ], [
    //         'kode_agen.required' => 'Kode agen wajib diisi',
    //         'nama_agen.required' => 'Nama agen wajib diisi',
    //         'alamat.required' => 'Alamat wajib diisi',
    //         'provinsi.required' => 'Provinsi wajib diisi',
    //         'kota.required' => 'Kota wajib diisi',
    //         'pic.required' => 'PIC wajib diisi',
    //         'nomor_pic.required' => 'Nomor PIC wajib diisi',
    //         'lokasi_event.required' => 'Lokasi event wajib diisi',
    //         'status.required' => 'Status wajib diisi'
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->back()
    //             ->withErrors($validator)
    //             ->withInput();
    //     }

    //     try {
    //         $agen->update([
    //             'kode_agen' => $request->kode_agen,
    //             'nama_agen' => $request->nama_agen,
    //             'alamat' => $request->alamat,
    //             'provinsi' => $request->provinsi,
    //             'kota' => $request->kota,
    //             'pic' => $request->pic,
    //             'nomor_pic' => $request->nomor_pic,
    //             'lokasi_event' => $request->lokasi_event,
    //             'status' => $request->status,
    //         ]);

    //         // LogAktivitas::create([
    //         //     'user_id'    => auth()->user()->id,
    //         //     'username'   => auth()->user()->name,
    //         //     'aksi'       => 'Edit',
    //         //     'fitur'      => 'Daftar Agen',
    //         //     'deskripsi'  => "Mengedit Data Agen {$agen->kode_agen} - {$agen->nama_agen}",
    //         //     'ip_address' => $request->ip(),
    //         //     'device' => Browser::browserName() . ' on ' . Browser::platformName(),
    //         //     'created_at' => now(),
    //         // ]);

    //         return redirect()->route('daftaragen.index')
    //             ->with('success', 'Data agen berhasil diupdate');

    //     } catch (\Exception $e) {
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage())
    //             ->withInput();
    //     }
    // }

    public function edit($id)
    {
        $agen = DaftarAgen::findOrFail($id);
        $provinsis = Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')->get();
        $lokasiEvents = MasterLokasiEvent::where('status', 'Aktif')->get();
        
        // Ambil semua merk yang tersedia
        $merks = Merk::orderBy('name')->get();

        // Ambil merk yang sudah dipilih oleh agen berdasarkan kode_agen
        $selectedMerks = UsersMerks::where('id_customer', $agen->kode_agen)
            ->pluck('id_merks')
            ->toArray();

        return view('daftaragen.edit', compact('agen', 'provinsis', 'lokasiEvents', 'merks', 'selectedMerks'));
    }

    public function update(Request $request, $id)
    {
        $agen = DaftarAgen::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'kode_agen' => 'required|max:50',
            'nama_agen' => 'required|max:255',
            'alamat' => 'required',
            // 'provinsi' => 'required|max:100',
            'kota' => 'required|max:100',
            'pic' => 'required|max:255',
            'nomor_pic' => 'required|max:20',
            'lokasi_event' => 'required|max:100',
            'status' => 'required|in:0,1',
            'merks' => 'required|array',
            'merks.*' => 'exists:merks,id'
        ], [
            'kode_agen.required' => 'Kode agen wajib diisi',
            'nama_agen.required' => 'Nama agen wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            // 'provinsi.required' => 'Provinsi wajib diisi',
            'kota.required' => 'Kota wajib diisi',
            'pic.required' => 'PIC wajib diisi',
            'nomor_pic.required' => 'Nomor PIC wajib diisi',
            'lokasi_event.required' => 'Lokasi event wajib diisi',
            'status.required' => 'Status wajib diisi',
            'merks.required' => 'Merk wajib dipilih',
            'merks.array' => 'Format merk tidak valid',
            'merks.*.exists' => 'Merk yang dipilih tidak valid'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update data agen
            $agen->update([
                'kode_agen' => $request->kode_agen,
                'nama_agen' => $request->nama_agen,
                'alamat' => $request->alamat,
                // 'provinsi' => $request->provinsi,
                'kota' => $request->kota,
                'pic' => $request->pic,
                'nomor_pic' => $request->nomor_pic,
                'lokasi_event' => $request->lokasi_event,
                'status' => $request->status,
            ]);

            // Hapus semua merk yang sebelumnya dipilih berdasarkan kode_agen
            UsersMerks::where('id_customer', $agen->kode_agen)->delete();

            // Simpan merk yang baru dipilih
            if ($request->has('merks') && is_array($request->merks)) {
                foreach ($request->merks as $merkId) {
                    UsersMerks::create([
                        'id_customer' => $agen->kode_agen, // GUNAKAN KODE_AGEN
                        'id_merks' => $merkId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('daftaragen.index')
                ->with('success', 'Data agen berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $daftaragen = DaftarAgen::findOrFail($id);

            $daftaragen->update(['status' => 0]); 

            LogAktivitas::create([
                'user_id'    => auth()->user()->id,
                'username'   => auth()->user()->name,
                'aksi'       => 'Hapus',
                'fitur'      => 'Daftar Toko',
                'deskripsi'  => "Menonaktifkan data toko {$daftaragen->kode_agen} - {$daftaragen->nama_agen}",
                'ip_address' => $request->ip(),
                'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                'created_at' => now(),
            ]);

            return redirect()->route('daftaragen.index')
                ->with('success', 'Data agen berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR Code untuk agen
     */
    public function generateQR(Request $request)
    {
        $lokasiEvents = MasterLokasiEvent::all();
        $selectedLokasi = $request->get('lokasi_event');
        $agens = collect();
        
        if ($selectedLokasi && $selectedLokasi != 'semua') {
            $agens = DaftarAgen::where('lokasi_event', $selectedLokasi)
                ->where('status', 1) // hanya agen aktif
                ->orderBy('nama_agen', 'asc')
                ->get();
        }
        
        return view('daftaragen.generate-qr', compact('lokasiEvents', 'selectedLokasi', 'agens'));
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
        
        $agens = DaftarAgen::where('lokasi_event', $lokasiEvent)
            ->where('status', 1)
            ->orderBy('nama_agen', 'asc')
            ->get();
        
        if ($agens->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada agen aktif di lokasi event yang dipilih');
        }
        
        // Generate QR Code sebagai base64 image untuk setiap agen
        foreach ($agens as $agen) {
            // Generate QR code sebagai SVG lalu konversi ke base64
            $qrCodeSvg = QrCode::format('svg')->size(150)->generate($agen->kode_agen);
            $agen->qr_code_base64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
        }
        
        $lokasiEventName = $lokasiEvent;
        $date = now()->format('d-m-Y');
        
        $pdf = Pdf::loadView('daftaragen.qr-pdf', compact('agens', 'lokasiEventName', 'date'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('QR-Code_Agen_' . $lokasiEvent . '_' . $date . '.pdf');
    }

    public function showImportForm()
    {
        return view('daftaragen.import');
    }

    /**
     * Process import excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120', // Max 5MB
        ]);
        
        try {
            $import = new DaftarAgenImport();
            Excel::import($import, $request->file('file'));
            
            $successCount = $import->getSuccessCount();
            $errors = $import->getErrorRows();
            
            if ($successCount > 0) {
                $message = "Berhasil mengimport {$successCount} data agen";
                
                if (count($errors) > 0) {
                    $message .= ", namun " . count($errors) . " data gagal diimport";
                    
                    // Simpan errors ke session untuk ditampilkan
                    session()->flash('import_errors', $errors);
                }
                
                return redirect()->route('daftaragen.index')
                    ->with('success', $message);
            } else {
                return redirect()->back()
                    ->with('error', 'Tidak ada data yang berhasil diimport. Pastikan file Excel berisi data yang valid.')
                    ->with('import_errors', $errors);
            }
            
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Download template import
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers sesuai dengan kolom di Excel
        $headers = [
            'NO', 'KODE Agen', 'NAMA Agen', 'ALAMAT TOKO', 'KOTA', 'PIC', 'NO WA PIC', 
            'Lokasi Event', 'Nama Hotel', 'Checkin'
        ];
        
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
        }
        
        // Set styling untuk header
        $sheet->getStyle('A1:J1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ]
        ]);
        
        // Set lebar kolom
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setWidth(20);
        }
        
        // Tambahkan contoh data
        $sheet->setCellValue('A2', '1');
        $sheet->setCellValue('B2', 'C0001');
        $sheet->setCellValue('C2', 'TOKO ABC');
        $sheet->setCellValue('D2', 'Jl. Contoh No. 123');
        $sheet->setCellValue('E2', 'JAKARTA');
        $sheet->setCellValue('F2', 'BUDI');
        $sheet->setCellValue('G2', '081234567890');
        $sheet->setCellValue('H2', 'EVENT A');
        $sheet->setCellValue('I2', 'HOTEL INDONESIA');
        $sheet->setCellValue('J2', 'Ya');
        
        // Set keterangan
        $sheet->setCellValue('A4', 'Catatan:');
        $sheet->setCellValue('A5', '- Kolom NO bisa diisi atau dikosongkan');
        $sheet->setCellValue('A6', '- Kolom Checkin diisi "Ya" atau "Tidak" (Ya = Check in, Tidak = null)');
        $sheet->setCellValue('A7', '- Kolom yang kosong akan disimpan sebagai NULL');
        $sheet->setCellValue('A8', '- Kode Agen dan Nama Agen wajib diisi');
        
        $sheet->getStyle('A4:A8')->getFont()->setSize(10);
        $sheet->getStyle('A4:A8')->getFont()->setItalic(true);
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'template_agen_');
        $writer->save($tempFile);
        
        return response()->download($tempFile, 'template_import_agen.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}