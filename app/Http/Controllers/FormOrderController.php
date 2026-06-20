<?php

namespace App\Http\Controllers;

use App\Models\FormOrder;
use App\Models\FormOrderDetail;
use App\Models\MasterTarget;
use App\Models\MasterLokasiEvent;
use App\Models\DaftarToko;
use App\Models\Voucher;
use App\Models\Merk;
use App\Models\UsersMerks;
use App\Models\DaftarAgen;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Wilayah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\FormOrderExport;
use App\Exports\FormOrderDetailExport;
use Browser;
use App\Models\HistoryFormOrder;

class FormOrderController extends Controller
{

    // public function index(Request $request)
    // {
    //     $query = FormOrder::with('details');
        
    //     // Filter untuk user role
    //     if(auth()->user()->role_as == 0) {
    //         $query->where('kode_agen', auth()->user()->id_customer);
    //     }
        
    //     // Search functionality
    //     if ($request->has('search') && $request->search != '') {
    //         $search = $request->search;
    //         $query->where(function($q) use ($search) {
    //             $q->where('nama_agen', 'like', "%{$search}%")
    //               ->orWhere('nama_sales', 'like', "%{$search}%")
    //               ->orWhere('nama_toko', 'like', "%{$search}%")
    //               ->orWhere('pic', 'like', "%{$search}%")
    //               ->orWhere('kota', 'like', "%{$search}%")
    //               ->orWhere('brand', 'like', "%{$search}%");
    //         });
    //     }
        
    //     // Filter by lokasi event
    //     if ($request->has('lokasi_event') && $request->lokasi_event != '') {
    //         $query->where('lokasi_event', $request->lokasi_event);
    //     }
        
    //     // Get unique lokasi events for filter dropdown
    //     $lokasiEvents = FormOrder::select('lokasi_event')
    //         ->distinct()
    //         ->whereNotNull('lokasi_event')
    //         ->where('lokasi_event', '!=', '')
    //         ->orderBy('lokasi_event')
    //         ->pluck('lokasi_event');
        
    //     $formOrders = $query->orderBy('created_at', 'desc')
    //                     ->paginate(10)
    //                     ->appends($request->query());

    //     return view('form-order.index', compact('formOrders', 'lokasiEvents'));
    // }

    // public function index(Request $request)
    // {
    //     $query = FormOrder::with(['details', 'vouchers']);
        
    //     // Filter untuk user role
    //     // if(auth()->user()->role_as == 0) {
    //     //     $query->where('kode_agen', auth()->user()->id_customer);
    //     // }
        
    //     // Search functionality
    //     if ($request->has('search') && $request->search != '') {
    //         $search = $request->search;
    //         $query->where(function($q) use ($search) {
    //             $q->where('nama_agen', 'like', "%{$search}%")
    //             ->orWhere('nama_sales', 'like', "%{$search}%")
    //             ->orWhere('nama_toko', 'like', "%{$search}%")
    //             ->orWhere('pic', 'like', "%{$search}%")
    //             ->orWhere('kota', 'like', "%{$search}%")
    //             ->orWhere('brand', 'like', "%{$search}%");
    //         });
    //     }
        
    //     // Filter by lokasi event
    //     if ($request->has('lokasi_event') && $request->lokasi_event != '') {
    //         $query->where('lokasi_event', $request->lokasi_event);
    //     }
        
    //     // Get unique lokasi events for filter dropdown
    //     // $lokasiEvents = FormOrder::select('lokasi_event')
    //     //     ->distinct()
    //     //     ->whereNotNull('lokasi_event')
    //     //     ->where('lokasi_event', '!=', '')
    //     //     ->orderBy('lokasi_event')
    //     //     ->pluck('lokasi_event');

    //     $lokasiEvents = MasterLokasiEvent::all();


    //     $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
    //         ->orderBy('tanggal', 'asc')
    //         ->first();
        
    //     $formOrders = $query->orderBy('created_at', 'desc')
    //                     ->paginate(10)
    //                     ->appends($request->query());

    //     return view('form-order.index', compact('formOrders', 'lokasiEvents', 'defaultLokasi'));
    // }

    public function index(Request $request)
    {
        $query = FormOrder::with(['details', 'vouchers']);
        
        // Filter untuk user role
        // if(auth()->user()->role_as == 0) {
        //     $query->where('kode_agen', auth()->user()->id_customer);
        // }
        
        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_agen', 'like', "%{$search}%")
                ->orWhere('nama_sales', 'like', "%{$search}%")
                ->orWhere('nama_toko', 'like', "%{$search}%")
                ->orWhere('pic', 'like', "%{$search}%")
                ->orWhere('kota', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%");
            });
        }
        
        // Filter by lokasi event - hanya jika ada nilai dan bukan 'semua'
        if ($request->has('lokasi_event') && $request->lokasi_event != '' && $request->lokasi_event != 'semua') {
            $query->where('lokasi_event', $request->lokasi_event);
        }
        
        // Jika tidak ada filter lokasi dan halaman pertama kali dibuka, gunakan default lokasi
        if (!$request->has('lokasi_event') || $request->lokasi_event == '') {
            $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
                ->orderBy('tanggal', 'asc')
                ->first();
            
            if ($defaultLokasi) {
                $query->where('lokasi_event', $defaultLokasi->nama_lokasi);
            }
        }
        
        // Get unique lokasi events for filter dropdown
        $lokasiEvents = MasterLokasiEvent::all();

        $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
            ->orderBy('tanggal', 'asc')
            ->first();
        
        $formOrders = $query->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->appends($request->query());

        return view('form-order.index', compact('formOrders', 'lokasiEvents', 'defaultLokasi'));
    }

    public function exportExcel(Request $request)
    {
        // Buat request baru dengan parameter yang sudah termasuk default lokasi
        $exportRequest = new Request();
        
        // Copy semua parameter dari request asli
        $exportRequest->merge($request->all());
        
        // Jika tidak ada parameter lokasi_event atau 'semua', tidak usah set default
        // Biarkan FormOrderExport handle logic filternya
        if (!$request->has('lokasi_event') || $request->lokasi_event == '') {
            $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
                ->orderBy('tanggal', 'asc')
                ->first();
            
            if ($defaultLokasi) {
                $exportRequest->merge(['lokasi_event' => $defaultLokasi->nama_lokasi]);
            }
        }
        
        // Ambil lokasi event untuk nama file
        $lokasiEvent = $exportRequest->get('lokasi_event');
        
        // Tentukan label untuk nama file
        $lokasiLabel = ($lokasiEvent == 'semua' || $lokasiEvent == '') ? 'Semua_Lokasi' : str_replace(' ', '_', $lokasiEvent);
        
        $fileName = 'Form_Order_' . $lokasiLabel . '_' . date('Y-m-d_His') . '.xlsx';
        
        return Excel::download(new FormOrderExport($exportRequest), $fileName);
    }

    public function exportDetail(Request $request)
    {
        return Excel::download(new FormOrderDetailExport($request), 'form-order-detail-' . date('Y-m-d-H-i-s') . '.xlsx');
    }

    // public function exportExcel(Request $request)
    // {
    //     $filename = 'form-order-' . date('Y-m-d-H-i-s') . '.xlsx';
        
    //     return Excel::download(new FormOrderExport($request), $filename);
    // }

    public function getNamaSales(Request $request)
    {
        try {
            $kodeAgen = $request->kode_agen;
            $namaAgen = $request->nama_agen;
            $namaToko = $request->nama_toko;

            // Cari data toko berdasarkan kombinasi agen dan toko
            $toko = DaftarToko::where('kode_agen', $kodeAgen)
                ->where('nama_agen', $namaAgen)
                ->where('nama_toko', $namaToko)
                ->first();

            if ($toko) {
                return response()->json([
                    'success' => true,
                    'nama_sales' => $toko->nama_sales
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'nama_sales' => ''
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'nama_sales' => '',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function getKotaByKode(Request $request)
    {
        $kode = $request->get('kode');
        
        if (!$kode) {
            return response()->json([
                'success' => false,
                'nama_kota' => 'Kode kota tidak tersedia'
            ]);
        }

        $wilayah = Wilayah::where('kode', $kode)->first();
        
        if ($wilayah) {
            return response()->json([
                'success' => true,
                'nama_kota' => $wilayah->nama // Sesuaikan dengan nama kolom di tabel wilayah
            ]);
        } else {
            return response()->json([
                'success' => false,
                'nama_kota' => 'Kota tidak ditemukan'
            ]);
        }
    }

    public function create()
    {
        $masterTargets = MasterTarget::where('status', 'active')->get();
        
        $user = auth()->user();
        
        // Get ALL lokasi aktif (untuk dropdown)
        $lokasiEvents = MasterLokasiEvent::where('status', 'aktif')
            ->orderBy('nama_lokasi', 'asc')
            ->get();
        
        // Inisialisasi variabel dengan default value
        $daftarAgen = collect(); // Default empty collection
        $agen = null;
        $brands = [];
        
        // Tidak lagi filter berdasarkan default lokasi
        $daftarTokoAll = DaftarToko::orderBy('nama_toko', 'asc')
            ->get();
        
        // Group toko berdasarkan kombinasi field yang sama
        $groupedTokos = [];
        foreach ($daftarTokoAll as $toko) {
            $uniqueKey = strtolower(trim($toko->nama_toko)) . '|' . 
                        strtolower(trim($toko->pic)) . '|' . 
                        strtolower(trim($toko->nomor_pic)) . '|' . 
                        strtolower(trim($toko->kota)) . '|';
            
            if (!isset($groupedTokos[$uniqueKey])) {
                // Simpan toko pertama yang ditemukan dengan kombinasi ini
                $groupedTokos[$uniqueKey] = $toko;
            }
        }
        
        $daftarTokoUnique = collect(array_values($groupedTokos));
        
        // Cek department user
        if ($user->department == 'SLS') {
            // Jika department SLS, tampilkan semua agen tanpa filter lokasi_event
            $daftarAgen = DaftarAgen::orderBy('nama_agen', 'asc')
                ->get();
            
            // Preload brand data untuk semua agen
            foreach ($daftarAgen as $agenItem) {
                $userMerks = UsersMerks::where('id_customer', $agenItem->kode_agen)->get();
                $agenBrands = [];
                
                foreach ($userMerks as $userMerk) {
                    $merk = Merk::find($userMerk->id_merks);
                    if ($merk) {
                        $brandName = $merk->name;
                        // Cek jika brand adalah mypremier atau kaisar, ganti menjadi OCEANIA
                        if (strtolower($brandName) === 'my premier' || strtolower($brandName) === 'kaisar') {
                            $brandName = 'OCEANIA';
                        }
                        $agenBrands[] = $brandName;
                    }
                }
                
                $agenItem->brand_names = array_unique($agenBrands);
            }
        } else {
            // Jika bukan SLS, ambil data berdasarkan user login
            $agen = DaftarAgen::where('kode_agen', $user->id_customer)
                ->first();
            
            // Load brand untuk agen yang login
            if ($agen) {
                $userMerks = UsersMerks::where('id_customer', $agen->kode_agen)->get();
                
                foreach ($userMerks as $userMerk) {
                    $merk = Merk::find($userMerk->id_merks);
                    if ($merk) {
                        $brandName = $merk->name;
                        // Cek jika brand adalah mypremier atau kaisar, ganti menjadi OCEANIA
                        if (strtolower($brandName) === 'my premier' || strtolower($brandName) === 'kaisar') {
                            $brandName = 'OCEANIA';
                        }
                        $brands[] = $brandName;
                    }
                }
                
                $brands = array_unique($brands); // Hapus duplikat
            }
        }
        
        return view('form-order.create', compact(
            'masterTargets', 
            'daftarTokoUnique', 
            'agen', 
            'brands', 
            'daftarAgen', 
            'user', 
            'lokasiEvents' // Ganti defaultLokasi dengan lokasiEvents
        ));
    }

    public function checkExistingOrder(Request $request)
    {
        $request->validate([
            'kode_agen' => 'required|string',
            'nama_toko' => 'required|string',
            'lokasi_event' => 'required|string',
            'kota' => 'required|string',
            'pic_old' => 'nullable|string',
            'nomor_pic_old' => 'nullable|string',
        ]);

        // Log untuk debugging
        \Log::info('Checking existing order with params:', $request->all());

        $existingOrder = FormOrder::where('kode_agen', $request->kode_agen)
            ->where('nama_toko', $request->nama_toko)
            ->where('lokasi_event', $request->lokasi_event)
            ->where('kota', $request->kota)
            ->where(function($query) use ($request) {
                $picOld = $request->pic_old ?? '';
                $nomorPicOld = $request->nomor_pic_old ?? '';
                
                if (!empty($picOld) && !empty($nomorPicOld)) {
                    $query->where('pic', $picOld)
                        ->where('no_hp', $nomorPicOld);
                } else {
                    $query->where(function($q) use ($picOld) {
                        $q->where('pic', $picOld)
                            ->orWhereNull('pic')
                            ->orWhere('pic', '');
                    })->where(function($q) use ($nomorPicOld) {
                        $q->where('no_hp', $nomorPicOld)
                            ->orWhereNull('no_hp')
                            ->orWhere('no_hp', '');
                    });
                }
            })
            ->first();

        if ($existingOrder) {
            // Load detail orders
            $details = FormOrderDetail::where('form_order_id', $existingOrder->id)->get();
            
            // Prepare data untuk dikirim ke frontend
            $orderData = [
                'id' => $existingOrder->id,
                'kode_agen' => $existingOrder->kode_agen,
                'nama_agen' => $existingOrder->nama_agen,
                'brand' => $existingOrder->brand ?? '',
                'nama_sales' => $existingOrder->nama_sales ?? '',
                'total_point' => $existingOrder->total_point ?? 0,
                'total_kupon' => $existingOrder->total_kupon ?? 0,
                'kode_toko' => $existingOrder->kode_toko,
                'nama_toko' => $existingOrder->nama_toko,
                'lokasi_event' => $existingOrder->lokasi_event,
                'kota' => $existingOrder->kota,
                'pic' => $existingOrder->pic ?? '',
                'no_hp' => $existingOrder->no_hp ?? '',
                'pic_old' => $existingOrder->pic ?? '', // Kirim pic yang tersimpan sebagai pic_old untuk update
                'nomor_pic_old' => $existingOrder->no_hp ?? '', // Kirim no_hp yang tersimpan sebagai nomor_pic_old untuk update
                'nama_terang' => $existingOrder->nama_terang ?? '',
                'details' => []
            ];
            
            foreach ($details as $detail) {
                $orderData['details'][$detail->master_target_id] = $detail->jumlah_pengambilan;
            }
            
            return response()->json([
                'success' => true,
                'exists' => true,
                'data' => $orderData,
                'message' => 'Data order ditemukan. Silakan edit dan update.'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'exists' => false,
            'message' => 'Data order tidak ditemukan. Silakan buat order baru.'
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'nama_agen' => 'required|exists:daftar_agen,id',
            'nama_toko' => 'required', // Hapus validasi exists, kita akan proses manual
            'nama_sales' => 'nullable|string|max:255',
            'lokasi_event' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'no_hp' => 'required|string|max:255',
            'brand' => 'required|string',
            'nama_terang'     => 'nullable|string|max:255',
            'ttd_nama_terang' => 'nullable|string',
            'targets' => 'required|array',
            'targets.*.master_target_id' => 'required|exists:master_targets,id',
            'targets.*.jumlah_pengambilan' => 'required|integer|min:0',
            'pic_old' => 'nullable|string|max:255',
            'nomor_pic_old' => 'nullable|string|max:255',
            'order_id' => 'nullable|exists:form_orders,id',
            'source' => 'nullable|string|in:quick-scan,create-page',
        ]);

        try {
            DB::beginTransaction();

            $agen = DaftarAgen::findOrFail($validated['nama_agen']);
            
            // ========== PROSES NAMA_TOKO (Support ID dan String) ==========
            $toko = null;
            $isNumericToko = is_numeric($validated['nama_toko']);
            
            if ($isNumericToko) {
                // CASE 1: Create page mengirim ID
                $toko = DaftarToko::find($validated['nama_toko']);
                if (!$toko) {
                    throw new \Exception('Data toko tidak ditemukan dengan ID: ' . $validated['nama_toko']);
                }
            } else {
                // CASE 2: Quick scan mengirim nama toko (string)
                // Cari toko berdasarkan kriteria
                $tokoQuery = DaftarToko::where('nama_toko', $validated['nama_toko'])
                    ->where('lokasi_event', $validated['lokasi_event'])
                    ->where('kota', $validated['kota']);
                
                // Gunakan pic_old dan nomor_pic_old jika ada
                if (!empty($validated['pic_old']) && !empty($validated['nomor_pic_old'])) {
                    $tokoQuery->where('pic', $validated['pic_old'])
                        ->where('nomor_pic', $validated['nomor_pic_old']);
                } else {
                    // Fallback: cari berdasarkan pic dan nomor_pic dari request
                    $tokoQuery->where(function($q) use ($validated) {
                        $q->where('pic', $validated['pic'] ?? '')
                            ->orWhereNull('pic')
                            ->orWhere('pic', '');
                    })->where(function($q) use ($validated) {
                        $q->where('nomor_pic', $validated['no_hp'] ?? '')
                            ->orWhereNull('nomor_pic')
                            ->orWhere('nomor_pic', '');
                    });
                }
                
                $tokos = $tokoQuery->get();
                
                if ($tokos->isEmpty()) {
                    // Jika tidak ditemukan, coba cari berdasarkan nama_toko saja (looser match)
                    $tokos = DaftarToko::where('nama_toko', $validated['nama_toko'])
                        ->where('lokasi_event', $validated['lokasi_event'])
                        ->get();
                }
                
                if ($tokos->isEmpty()) {
                    \Log::error('Toko tidak ditemukan untuk quick-scan', [
                        'nama_toko' => $validated['nama_toko'],
                        'lokasi_event' => $validated['lokasi_event'],
                        'kota' => $validated['kota'],
                        'pic_old' => $validated['pic_old'] ?? null,
                        'nomor_pic_old' => $validated['nomor_pic_old'] ?? null,
                    ]);
                    throw new \Exception('Data toko tidak ditemukan dengan kriteria yang diberikan!');
                }
                
                $toko = $tokos->first();
            }
            
            // Validasi tambahan: pastikan toko ditemukan
            if (!$toko) {
                throw new \Exception('Data toko tidak ditemukan!');
            }
            
            // CHECK IF UPDATE OR CREATE
            $isUpdate = !empty($validated['order_id']);
            
            // UPDATE DATA TOKO (jika diperlukan)
            if ($isUpdate) {
                // Untuk update, update berdasarkan ID toko yang ditemukan
                DaftarToko::where('id', $toko->id)->update([
                    'pic' => $request->pic,
                    'nomor_pic' => $request->no_hp,
                    'nama_sales' => $validated['nama_sales']
                ]);
            } else {
                // Untuk create, update toko yang ditemukan
                DaftarToko::where('id', $toko->id)->update([
                    'pic' => $request->pic,
                    'nomor_pic' => $request->no_hp,
                    'lokasi_event' => $validated['lokasi_event'],
                    'kota' => $validated['kota'],
                ]);
                
                // Update juga toko lain yang sama (jika ada duplikat berdasarkan kode_toko)
                DaftarToko::where('kode_toko', $toko->kode_toko)
                    ->where('id', '!=', $toko->id)
                    ->update([
                        'pic' => $request->pic,
                        'nomor_pic' => $request->no_hp,
                        'lokasi_event' => $validated['lokasi_event'],
                        'kota' => $validated['kota'],
                    ]);
            }
            
            // CREATE OR UPDATE FORM ORDER
            $totalGrandPoint = 0;
            $totalKupon = 0;
            
            if ($isUpdate) {
                $formOrder = FormOrder::findOrFail($validated['order_id']);
                
                $formOrder->update([
                    'nama_sales' => $validated['nama_sales'],
                    'brand' => $request->brand,
                    'pic' => $request->pic,
                    'no_hp' => $request->no_hp,
                    'kota' => $request->kota,
                    'nama_terang'     => $validated['nama_terang'] ?? null,
                    'ttd_nama_terang' => $validated['ttd_nama_terang'] ?? null,
                ]);
                
                FormOrderDetail::where('form_order_id', $formOrder->id)->delete();
                Voucher::where('form_order_id', $formOrder->id)->delete();
                
            } else {
                $formOrder = FormOrder::create([
                    'tanggal_order' => now()->format('Y-m-d'),
                    'kode_agen' => $agen->kode_agen,
                    'nama_agen' => $agen->nama_agen,
                    'kode_toko' => $toko->kode_toko,
                    'nama_toko' => $toko->nama_toko,
                    'nama_sales' => $validated['nama_sales'],
                    'lokasi_event' => $validated['lokasi_event'],
                    'brand' => $request->brand,
                    'pic' => $request->pic,
                    'no_hp' => $request->no_hp,
                    'kota' => $request->kota,
                    'nama_terang'     => $validated['nama_terang'] ?? null,
                    'ttd_nama_terang' => $validated['ttd_nama_terang'] ?? null,
                    'total_point' => 0,
                ]);
            }
            
            // Create new details
            foreach ($validated['targets'] as $targetData) {
                $masterTarget = MasterTarget::findOrFail($targetData['master_target_id']);
                $jumlahPengambilan = $targetData['jumlah_pengambilan'] ?? 0;
                
                if ($jumlahPengambilan > 0) {
                    $totalPoint = $masterTarget->point * $jumlahPengambilan;
                    $totalGrandPoint += $totalPoint;
                    
                    $kuponPerPaket = $masterTarget->kupon ?? 0;
                    $totalKupon += $kuponPerPaket * $jumlahPengambilan;
                    
                    FormOrderDetail::create([
                        'form_order_id' => $formOrder->id,
                        'master_target_id' => $masterTarget->id,
                        'paket' => $masterTarget->target,
                        'point_per_paket' => $masterTarget->point,
                        'jumlah_pengambilan' => $jumlahPengambilan,
                        'total_point' => $totalPoint,
                        'kupon_per_paket' => $kuponPerPaket,
                        'total_kupon' => $kuponPerPaket * $jumlahPengambilan,
                    ]);
                }
            }
            
            $formOrder->update([
                'total_point' => $totalGrandPoint,
                'total_kupon' => $totalKupon
            ]);
            
            // GENERATE VOUCHER
            $kodeUnik = null;
            if ($totalKupon > 0) {
                $kodeUnik = $this->generateKodeUnik();
                
                for ($i = 0; $i < $totalKupon; $i++) {
                    $nomorVoucher = $this->generateNomorVoucher();
                    
                    DB::table('vouchers')->insert([
                        'kode_unik' => $kodeUnik,
                        'form_order_id' => $formOrder->id,
                        'nomor_voucher' => $nomorVoucher,
                        'kode_toko' => $toko->kode_toko,
                        'nama_toko' => $toko->nama_toko,
                        'nama_pic' => $request->pic,
                        'no_hp' => $request->no_hp,
                        'lokasi_event' => $validated['lokasi_event'],
                        'status' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                $formOrder->update([
                    'jumlah_voucher' => $totalKupon,
                    'kode_unik_voucher' => $kodeUnik
                ]);
            }
            
            DB::commit();

            // Simpan history
            try {
                $this->saveHistoryFormOrder($formOrder, $isUpdate ? 'update' : 'create', $request, $totalGrandPoint, $totalKupon);
            } catch (\Exception $e) {
                \Log::error('Gagal simpan history form order: ' . $e->getMessage());
            }
            
            // Log aktivitas (sama seperti sebelumnya)
            try {
                $logUsername = auth()->check()
                    ? auth()->user()->name
                    : trim(implode(' - ', array_filter([
                        $formOrder->kode_toko ?? '',
                        $formOrder->nama_toko ?? '',
                        $formOrder->pic ?? '',
                    ])));
                    
                $aksi = $isUpdate ? 'Update' : 'Tambah';
                
                LogAktivitas::create([
                    'user_id' => auth()->id() ?? null,
                    'username' => $logUsername !== '' ? $logUsername : 'guest',
                    'aksi' => $aksi,
                    'fitur' => 'Form Order',
                    'deskripsi' => ($isUpdate ? "Mengupdate" : "Menyimpan") . " form order untuk toko {$formOrder->nama_toko} dengan agen {$formOrder->nama_agen}",
                    'ip_address' => $request->ip(),
                    'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Gagal menyimpan log aktivitas: ' . $e->getMessage());
            }
            
            $successMessage = $isUpdate ? 'Form order berhasil diupdate!' : 'Form order berhasil disimpan!';
            $successMessage .= " Data toko berhasil diupdate.";
            
            if ($totalKupon > 0) {
                $successMessage .= " Toko mendapatkan $totalKupon voucher undian.";
                if (!$isUpdate) {
                    $successMessage .= " Kode Unik: $kodeUnik";
                }
            }
            
            // Tentukan redirect berdasarkan source parameter
            $source = $request->input('source', 'create-page');
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'kode_unik' => $kodeUnik ?? ($formOrder->kode_unik_voucher ?? null),
                    'jumlah_voucher' => $totalKupon ?? 0,
                    'is_update' => $isUpdate,
                    'source' => $source,
                    'redirect_url' => $source === 'quick-scan' 
                        ? route('form-order.success', [
                            'kode_unik' => $kodeUnik ?? ($formOrder->kode_unik_voucher ?? ''),
                            'jumlah_voucher' => $totalKupon ?? 0,
                        ])
                        : route('form-order.index'),
                    'form_order_id' => $formOrder->id,
                    'nama_toko' => $formOrder->nama_toko,
                    'nama_agen' => $formOrder->nama_agen,
                ]);
            }
            
            // Non-AJAX redirect
            if ($source === 'quick-scan') {
                return redirect()->route('form-order.success', [
                    'kode_unik' => $kodeUnik ?? ($formOrder->kode_unik_voucher ?? ''),
                    'jumlah_voucher' => $totalKupon ?? 0,
                ]);
            }
            
            return redirect()->route('form-order.index')
                ->with('success', $successMessage);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Store order error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Fungsi untuk generate kode unik (1 kode untuk 1 form order)
    private function generateKodeUnik()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $kodeUnik = '';
        
        do {
            $kodeUnik = '';
            for ($i = 0; $i < 6; $i++) {
                $kodeUnik .= $characters[rand(0, strlen($characters) - 1)];
            }
            
            // Cek apakah kode unik sudah ada
            $exists = DB::table('vouchers')->where('kode_unik', $kodeUnik)->exists();
        } while ($exists);
        
        return $kodeUnik;
    }

    // Fungsi untuk generate nomor voucher (format: XXXX XXXX XXXX)
    private function generateNomorVoucher()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $nomorVoucher = '';
        
        do {
            // Generate 12 karakter
            $temp = '';
            for ($i = 0; $i < 12; $i++) {
                $temp .= $characters[rand(0, strlen($characters) - 1)];
            }
            
            // Format: XXXX XXXX XXXX
            $nomorVoucher = substr($temp, 0, 4) . ' ' . substr($temp, 4, 4) . ' ' . substr($temp, 8, 4);
            
            // Cek apakah nomor voucher sudah ada
            $exists = DB::table('vouchers')->where('nomor_voucher', $nomorVoucher)->exists();
        } while ($exists);
        
        return $nomorVoucher;
    }

    // Generate dan download image voucher berisi kode unik
    public function downloadVoucherImage(Request $request)
    {
        $kodeUnik     = $request->query('kode_unik');
        $jumlahVoucher = (int) ($request->query('jumlah') ?? $request->query('jumlah_voucher') ?? 0);
        $isPreview = $request->boolean('preview');

        if (!$kodeUnik) {
            abort(400, 'Missing kode_unik parameter');
        }

        $width  = 800;
        $height = 600;
        $image  = imagecreatetruecolor($width, $height);

        // ── Color palette (clean, minimal)
        $white      = imagecolorallocate($image, 255, 255, 255);
        $bgColor    = imagecolorallocate($image, 250, 250, 250); // near-white bg
        $darkRed    = imagecolorallocate($image, 149,   0,   0); // #950000 — header & accents
        $medRed     = imagecolorallocate($image, 180,  20,  20); // text labels
        $borderGray = imagecolorallocate($image, 210, 210, 210); // card border
        $sepGray    = imagecolorallocate($image, 220, 220, 220); // separator line
        $textDark   = imagecolorallocate($image, 40,  40,  40);  // body text
        $textMuted  = imagecolorallocate($image, 130, 130, 130); // muted text
        $fontRegular = 'C:\\Windows\\Fonts\\arial.ttf';
        $fontBold    = 'C:\\Windows\\Fonts\\arialbd.ttf';
        $useTtfFonts = file_exists($fontRegular) && file_exists($fontBold) && function_exists('imagettftext');

        $drawCenteredText = function ($text, $fontPath, $fontSize, $y, $color) use ($image, $width, $useTtfFonts) {
            if ($useTtfFonts) {
                $box = imagettfbbox($fontSize, 0, $fontPath, $text);
                $textWidth = abs($box[4] - $box[0]);
                $x = (int)(($width - $textWidth) / 2);
                imagettftext($image, $fontSize, 0, $x, $y, $color, $fontPath, $text);
                return;
            }

            $font = 5;
            $x = (int)(($width - strlen($text) * imagefontwidth($font)) / 2);
            imagestring($image, $font, $x, $y - 18, $text, $color);
        };

        $drawLabelText = function ($text, $fontPath, $fontSize, $x, $y, $color) use ($image, $useTtfFonts) {
            if ($useTtfFonts) {
                imagettftext($image, $fontSize, 0, $x, $y, $color, $fontPath, $text);
                return;
            }

            imagestring($image, 2, $x, $y - 14, $text, $color);
        };

        // ── Background
        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

        // ── Header image (full width, top)
        $headerH       = 150;
        $headerImgPath = public_path('images/bg/header-new.jpeg');
        if (file_exists($headerImgPath)) {
            $headerImg = @imagecreatefromjpeg($headerImgPath);
            if ($headerImg) {
                $srcW = imagesx($headerImg);
                $srcH = imagesy($headerImg);
                imagecopyresampled($image, $headerImg, 0, 0, 0, 0, $width, $headerH, $srcW, $srcH);
                imagedestroy($headerImg);
            }
        } else {
            // Fallback: clean dark-red bar
            imagefilledrectangle($image, 0, 0, $width, $headerH, $darkRed);
            $titleText = "VOUCHER UNDIAN";
            if ($useTtfFonts) {
                $box = imagettfbbox(24, 0, $fontBold, $titleText);
                $titleX = (int)(($width - abs($box[4] - $box[0])) / 2);
                imagettftext($image, 24, 0, $titleX, 88, $white, $fontBold, $titleText);
            } else {
                $titleX    = ($width - strlen($titleText) * imagefontwidth(5)) / 2;
                imagestring($image, 5, $titleX, 60, $titleText, $white);
            }
        }

        // ── Thin separator line below header
        imagefilledrectangle($image, 0, $headerH, $width, $headerH + 2, $darkRed);

        // ── Content card
        $cardX1 = 40; $cardY1 = $headerH + 20;
        $cardX2 = $width - 40; $cardY2 = $height - 60;

        // Card shadow (subtle — 2px offset gray box behind)
        imagefilledrectangle($image, $cardX1 + 3, $cardY1 + 3, $cardX2 + 3, $cardY2 + 3,
            imagecolorallocate($image, 200, 200, 200));

        // Card background
        imagefilledrectangle($image, $cardX1, $cardY1, $cardX2, $cardY2, $white);

        // Card border — thin gray
        imagesetthickness($image, 1);
        imagerectangle($image, $cardX1, $cardY1, $cardX2, $cardY2, $borderGray);

        // Card left accent stripe (dark red, 4px wide)
        imagefilledrectangle($image, $cardX1, $cardY1, $cardX1 + 4, $cardY2, $darkRed);

        // ── Section: KODE UNIK
        $innerX = $cardX1 + 24;
        $midCard = ($cardX1 + $cardX2) / 2;

        $drawLabelText('KODE UNIK', $fontBold, 18, $innerX, $cardY1 + 42, $medRed);

        // Kode value — large, centered
        if ($useTtfFonts) {
            $codeBox = imagettfbbox(32, 0, $fontBold, $kodeUnik);
            $codeX   = (int)(($width - abs($codeBox[4] - $codeBox[0])) / 2);
            imagettftext($image, 32, 0, $codeX, $cardY1 + 95, $textDark, $fontBold, $kodeUnik);
        } else {
            $codeSize = 5;
            $codeX    = (int)(($width - strlen($kodeUnik) * imagefontwidth($codeSize)) / 2);
            imagestring($image, $codeSize, $codeX, $cardY1 + 60, $kodeUnik, $textDark);
        }

        // ── Separator
        $sepY = $cardY1 + 120;
        imagesetthickness($image, 1);
        imageline($image, $innerX, $sepY, $cardX2 - 20, $sepY, $sepGray);

        // ── Section: JUMLAH VOUCHER
        $drawLabelText('JUMLAH VOUCHER', $fontBold, 18, $innerX, $sepY + 38, $medRed);

        $voucherText = $jumlahVoucher . " Voucher";
        if ($useTtfFonts) {
            $voucherBox = imagettfbbox(28, 0, $fontBold, $voucherText);
            $voucherX   = (int)(($width - abs($voucherBox[4] - $voucherBox[0])) / 2);
            imagettftext($image, 28, 0, $voucherX, $sepY + 88, $textDark, $fontBold, $voucherText);
        } else {
            $voucherX    = (int)(($width - strlen($voucherText) * imagefontwidth(5)) / 2);
            imagestring($image, 5, $voucherX, $sepY + 50, $voucherText, $textDark);
        }

        // ── Output
        header('Content-Type: image/png');
        header('Content-Disposition: ' . ($isPreview ? 'inline' : 'attachment') . '; filename="Voucher-' . $kodeUnik . '.png"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        imagepng($image);
        imagedestroy($image);
        exit;
    }

    public function success(Request $request)
    {
        $kodeUnik = $request->query('kode_unik');
        $jumlahVoucher = (int) ($request->query('jumlah_voucher') ?? $request->query('jumlah') ?? 0);

        abort_if(empty($kodeUnik), 404);

        $downloadUrl = route('download.voucher.image', [
            'kode_unik' => $kodeUnik,
            'jumlah' => $jumlahVoucher,
            'ts' => now()->timestamp,
        ]);

        $previewUrl = route('download.voucher.image', [
            'kode_unik' => $kodeUnik,
            'jumlah' => $jumlahVoucher,
            'preview' => 1,
            'ts' => now()->timestamp,
        ]);

        return view('form-order.success', compact('kodeUnik', 'jumlahVoucher', 'downloadUrl', 'previewUrl'));
    }

    public function getTokoByAgen(Request $request)
    {
        $agenId = $request->get('agen_id');
        
        if (!$agenId) {
            return response()->json([
                'success' => false,
                'data' => []
            ]);
        }

        // Cari agen berdasarkan ID
        $agen = DaftarAgen::find($agenId);
        
        if (!$agen) {
            return response()->json([
                'success' => false,
                'data' => []
            ]);
        }

        // Get default lokasi aktif
        $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
            ->orderBy('tanggal', 'asc')
            ->first();

        // Ambil toko berdasarkan kode_agen dan lokasi_event default - TAMBAHKAN nama_sales
        $tokoList = DaftarToko::where('kode_agen', $agen->kode_agen)
            ->when($defaultLokasi, function($query) use ($defaultLokasi) {
                return $query->where('lokasi_event', $defaultLokasi->nama_lokasi);
            })
            ->orderBy('nama_toko', 'asc')
            ->get(['id', 'nama_toko', 'kota', 'pic', 'nomor_pic', 'nama_sales', 'lokasi_event']);

        return response()->json([
            'success' => true,
            'data' => $tokoList
        ]);
    }

    /**
     * Halaman cepat untuk scan kode_toko dan input mandiri user
     */
    public function scanCreate()
    {
        $masterTargets = MasterTarget::where('status', 'active')->get();

        // Ambil lokasi event aktif terbaru
        $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
            ->orderBy('tanggal', 'desc')
            ->first();

        return view('form-order.quick-create', compact('masterTargets', 'defaultLokasi'));
    }

    /**
     * Lookup toko berdasarkan kode_toko (barcode)
     */
    public function lookupByKodeToko(Request $request)
    {
        $kode = $request->get('kode');

        if (!$kode) {
            return response()->json(['success' => false, 'message' => 'Kode kosong']);
        }

        $toko = DaftarToko::where('kode_toko', $kode)->first();

        // Jika tidak ditemukan, coba cari berdasarkan kode partial
        if (!$toko) {
            $toko = DaftarToko::where('kode_toko', 'like', "%{$kode}%")->first();
        }

        $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
            ->orderBy('tanggal', 'desc')
            ->first();

        if ($toko) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $toko->id,
                    'pic' => $toko->pic,
                    'no_hp' => $toko->nomor_pic,
                    'kota' => $toko->kota,
                    'lokasi_event' => $toko->lokasi_event ?: ($defaultLokasi->nama_lokasi ?? ''),
                    'nama_toko' => $toko->nama_toko,
                    'kode_toko' => $toko->kode_toko,
                    'nama_sales' => $toko->nama_sales ?? null,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Toko tidak ditemukan',
            'default_lokasi' => $defaultLokasi->nama_lokasi ?? ''
        ]);
    }

    /**
     * Lookup agen berdasarkan kode_agen, kembalikan nama dan brand
     */
    public function lookupAgenByKode(Request $request)
    {
        $kode = $request->get('kode_agen');

        if (!$kode) {
            return response()->json(['success' => false, 'message' => 'Kode agen kosong']);
        }

        $agen = DaftarAgen::where('kode_agen', $kode)->first();

        if (!$agen) {
            return response()->json(['success' => false, 'message' => 'Agen tidak ditemukan']);
        }

        $userMerks = UsersMerks::where('id_customer', $agen->kode_agen)->get();
        $agenBrands = [];
        foreach ($userMerks as $userMerk) {
            $merk = Merk::find($userMerk->id_merks);
            if ($merk) {
                $brandName = $merk->name;
                if (strtolower($brandName) === 'my premier' || strtolower($brandName) === 'kaisar') {
                    $brandName = 'OCEANIA';
                }
                $agenBrands[] = $brandName;
            }
        }

        $agenBrands = array_unique($agenBrands);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $agen->id,
                'nama_agen' => $agen->nama_agen,
                'brands' => array_values($agenBrands),
                'kode_agen' => $agen->kode_agen,
            ]
        ]);
    }

     public function show(FormOrder $formOrder)
    {
        $formOrder->load('details.masterTarget', 'vouchers');
        
        return view('form-order.show', compact('formOrder'));
    }

    public function pdf(FormOrder $formOrder)
    {
        $formOrder->load('details.masterTarget', 'vouchers');
        $masterTargets = MasterTarget::where('status', 'active')->orderBy('id')->get();

        // Return preview view (sesuai report.blade.php)
        // User bisa klik tombol "Cetak / Save as PDF" untuk download
        return view('form-order.preview', compact('formOrder', 'masterTargets'));
    }

    // public function edit($id)
    // {
    //     $formOrder = FormOrder::with('details.masterTarget')->findOrFail($id);
    //     $masterTargets = MasterTarget::where('status', 'active')->get();
        
    //     $user = auth()->user();
        
    //     // Security check: pastikan user hanya bisa edit order miliknya (kecuali SLS)
    //     if ($user->department != 'SLS' && $formOrder->kode_agen != $user->id_customer) {
    //         abort(403, 'Unauthorized action.');
    //     }
        
    //     // Inisialisasi variabel dengan default value
    //     $daftarAgen = collect();
    //     $agen = null;
    //     $brands = [];
        
    //     // Ambil semua toko dan group by kombinasi field yang menentukan duplikat
    //     $daftarTokoAll = DaftarToko::orderBy('nama_toko', 'asc')->get();
        
    //     // Group toko berdasarkan kombinasi field yang sama
    //     $groupedTokos = [];
    //     foreach ($daftarTokoAll as $toko) {
    //         $uniqueKey = strtolower(trim($toko->nama_toko)) . '|' . 
    //                     strtolower(trim($toko->pic)) . '|' . 
    //                     strtolower(trim($toko->nomor_pic)) . '|' . 
    //                     strtolower(trim($toko->kota)) . '|' . 
    //                     strtolower(trim($toko->lokasi_event)) . '|' . 
    //                     strtolower(trim($toko->nama_sales));
            
    //         if (!isset($groupedTokos[$uniqueKey])) {
    //             // Simpan toko pertama yang ditemukan dengan kombinasi ini
    //             $groupedTokos[$uniqueKey] = $toko;
    //         }
    //     }
        
    //     $daftarTokoUnique = collect(array_values($groupedTokos));
        
    //     // Cek department user
    //     if ($user->department == 'SLS') {
    //         // Jika department SLS, tampilkan semua agen
    //         $daftarAgen = DaftarAgen::orderBy('nama_agen', 'asc')->get();
            
    //         // Preload brand data untuk semua agen
    //         foreach ($daftarAgen as $agenItem) {
    //             $userMerks = UsersMerks::where('id_customer', $agenItem->kode_agen)->get();
    //             $agenBrands = [];
                
    //             foreach ($userMerks as $userMerk) {
    //                 $merk = Merk::find($userMerk->id_merks);
    //                 if ($merk) {
    //                     $brandName = $merk->name;
    //                     if (strtolower($brandName) === 'my premier' || strtolower($brandName) === 'kaisar') {
    //                         $brandName = 'OCEANIA';
    //                     }
    //                     $agenBrands[] = $brandName;
    //                 }
    //             }
                
    //             $agenItem->brand_names = array_unique($agenBrands);
    //         }
    //     } else {
    //         // Jika bukan SLS, ambil data berdasarkan user login
    //         $agen = DaftarAgen::where('kode_agen', $user->id_customer)->first();
            
    //         // Load brand untuk agen yang login
    //         if ($agen) {
    //             $userMerks = UsersMerks::where('id_customer', $agen->kode_agen)->get();
                
    //             foreach ($userMerks as $userMerk) {
    //                 $merk = Merk::find($userMerk->id_merks);
    //                 if ($merk) {
    //                     $brandName = $merk->name;
    //                     if (strtolower($brandName) === 'my premier' || strtolower($brandName) === 'kaisar') {
    //                         $brandName = 'OCEANIA';
    //                     }
    //                     $brands[] = $brandName;
    //                 }
    //             }
                
    //             $brands = array_unique($brands);
    //         }
    //     }
        
    //     // Ambil selected agen ID untuk SLS
    //     $selectedAgenId = null;
    //     if ($user->department == 'SLS') {
    //         $selectedAgenData = DaftarAgen::where('kode_agen', $formOrder->kode_agen)->first();
    //         $selectedAgenId = $selectedAgenData ? $selectedAgenData->id : null;
    //     }
        
    //     // Ambil selected toko ID
    //     $selectedTokoId = DaftarToko::where('kode_toko', $formOrder->kode_toko)->value('id');
        
    //     return view('form-order.edit', compact(
    //         'formOrder', 
    //         'masterTargets', 
    //         'daftarTokoUnique', // Ganti daftarToko dengan daftarTokoUnique
    //         'agen', 
    //         'brands', 
    //         'daftarAgen', 
    //         'user',
    //         'selectedAgenId',
    //         'selectedTokoId'
    //     ));
    // }

    public function edit($id)
    {
        $formOrder = FormOrder::with('details.masterTarget')->findOrFail($id);
        $masterTargets = MasterTarget::where('status', 'active')->get();
        
        $user = auth()->user();
        
        // Security check: pastikan user hanya bisa edit order miliknya (kecuali SLS)
        if ($user->department != 'SLS' && $formOrder->kode_agen != $user->id_customer) {
            abort(403, 'Unauthorized action.');
        }
        
        // Inisialisasi variabel dengan default value
        $daftarAgen = collect();
        $agen = null;
        $brands = [];
        
        // Ambil semua toko dan group by kombinasi field yang menentukan duplikat
        $daftarTokoAll = DaftarToko::orderBy('nama_toko', 'asc')->get();
        
        // Group toko berdasarkan kombinasi field yang sama
        $groupedTokos = [];
        foreach ($daftarTokoAll as $toko) {
            $uniqueKey = strtolower(trim($toko->nama_toko)) . '|' . 
                        strtolower(trim($toko->pic)) . '|' . 
                        strtolower(trim($toko->nomor_pic)) . '|' . 
                        strtolower(trim($toko->kota)) . '|' ;
            
            if (!isset($groupedTokos[$uniqueKey])) {
                // Simpan toko pertama yang ditemukan dengan kombinasi ini
                $groupedTokos[$uniqueKey] = $toko;
            }
        }
        
        $daftarTokoUnique = collect(array_values($groupedTokos));
        
        // Cek department user
        if ($user->department == 'SLS') {
            // Jika department SLS, tampilkan semua agen
            $daftarAgen = DaftarAgen::orderBy('nama_agen', 'asc')->get();
            
            // Preload brand data untuk semua agen
            foreach ($daftarAgen as $agenItem) {
                $userMerks = UsersMerks::where('id_customer', $agenItem->kode_agen)->get();
                $agenBrands = [];
                
                foreach ($userMerks as $userMerk) {
                    $merk = Merk::find($userMerk->id_merks);
                    if ($merk) {
                        $brandName = $merk->name;
                        if (strtolower($brandName) === 'my premier' || strtolower($brandName) === 'kaisar') {
                            $brandName = 'OCEANIA';
                        }
                        $agenBrands[] = $brandName;
                    }
                }
                
                $agenItem->brand_names = array_unique($agenBrands);
            }
        } else {
            // Jika bukan SLS, ambil data berdasarkan user login
            $agen = DaftarAgen::where('kode_agen', $user->id_customer)->first();
            
            // Load brand untuk agen yang login
            if ($agen) {
                $userMerks = UsersMerks::where('id_customer', $agen->kode_agen)->get();
                
                foreach ($userMerks as $userMerk) {
                    $merk = Merk::find($userMerk->id_merks);
                    if ($merk) {
                        $brandName = $merk->name;
                        if (strtolower($brandName) === 'my premier' || strtolower($brandName) === 'kaisar') {
                            $brandName = 'OCEANIA';
                        }
                        $brands[] = $brandName;
                    }
                }
                
                $brands = array_unique($brands);
            }
        }
        
        // Ambil selected agen ID untuk SLS
        $selectedAgenId = null;
        if ($user->department == 'SLS') {
            $selectedAgenData = DaftarAgen::where('kode_agen', $formOrder->kode_agen)->first();
            $selectedAgenId = $selectedAgenData ? $selectedAgenData->id : null;
        }
        
        // Ambil selected toko ID berdasarkan nama toko dan kondisi lainnya
        $selectedToko = DaftarToko::where('kode_toko', $formOrder->kode_toko)
            ->first();
        
        $selectedTokoId = $selectedToko ? $selectedToko->id : null;
        
        return view('form-order.edit', compact(
            'formOrder', 
            'masterTargets', 
            'daftarTokoUnique',
            'agen', 
            'brands', 
            'daftarAgen', 
            'user',
            'selectedAgenId',
            'selectedTokoId'
        ));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $formOrder = FormOrder::findOrFail($id);
        
        // Security check: pastikan user hanya bisa update order miliknya (kecuali SLS)
        if ($user->department != 'SLS' && $formOrder->kode_agen != $user->id_customer) {
            abort(403, 'Unauthorized action.');
        }
        
        // Validasi data berdasarkan department user
        if ($user->department == 'SLS') {
            $validated = $request->validate([
                'nama_agen' => 'required|exists:daftar_agen,id',
                'nama_toko' => 'required|exists:daftar_toko,id',
                'nama_sales' => 'nullable|string|max:255',
                'lokasi_event' => 'required|string|max:255',
                'kota' => 'required|string|max:255',
                'no_hp' => 'required|string|max:255',
                'targets' => 'required|array',
                'targets.*.master_target_id' => 'required|exists:master_targets,id',
                'targets.*.jumlah_pengambilan' => 'required|integer|min:0',
                'pic_old' => 'required|string|max:255',
                'nomor_pic_old' => 'required|string|max:255',
            ]);
        } else {
            $validated = $request->validate([
                'nama_agen_id' => 'required|exists:daftar_agen,id',
                'nama_toko' => 'required|exists:daftar_toko,id',
                'nama_sales' => 'required|string|max:255',
                'lokasi_event' => 'required|string|max:255',
                'targets' => 'required|array',
                'targets.*.master_target_id' => 'required|exists:master_targets,id',
                'targets.*.jumlah_pengambilan' => 'required|integer|min:0',
                'pic_old' => 'required|string|max:255',
                'nomor_pic_old' => 'required|string|max:255',
            ]);
        }

        try {
            DB::beginTransaction();

            // Ambil data agen berdasarkan department user
            if ($user->department == 'SLS') {
                $agen = DaftarAgen::findOrFail($validated['nama_agen']);
            } else {
                $agen = DaftarAgen::findOrFail($validated['nama_agen_id']);
            }

            // Ambil data toko berdasarkan ID
            $tokoById = DaftarToko::findOrFail($validated['nama_toko']);
            
            // Cari SEMUA toko yang memiliki kombinasi yang sama berdasarkan NAMA TOKO (bukan ID)
            $tokos = DaftarToko::where('nama_toko', $tokoById->nama_toko)
                ->where('pic', $validated['pic_old'])
                ->where('nomor_pic', $validated['nomor_pic_old'])
                ->where('lokasi_event', $validated['lokasi_event'])
                ->where('kota', $validated['kota'])
                ->get();

            if ($tokos->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Data toko tidak ditemukan dengan kriteria yang diberikan!')
                    ->withInput();
            }

            // Ambil toko pertama sebagai referensi
            $toko = $tokos->first();

            // UPDATE SEMUA DATA TOKO YANG MEMILIKI KOMBINASI YANG SAMA - PIC dan Nomor PIC
            DaftarToko::where('nama_toko', $toko->nama_toko)
                ->where('pic', $validated['pic_old'])
                ->where('nomor_pic', $validated['nomor_pic_old'])
                ->where('lokasi_event', $validated['lokasi_event'])
                ->where('kota', $validated['kota'])
                ->update([
                    'pic' => $request->pic,
                    'nomor_pic' => $request->no_hp,
                ]);

            // UPDATE SEMUA DATA TOKO - Nama Sales
            DaftarToko::where('nama_toko', $toko->nama_toko)
                ->where('pic', $request->pic)
                ->where('nomor_pic', $request->no_hp)
                ->where('lokasi_event', $validated['lokasi_event'])
                ->where('kota', $validated['kota'])
                ->where('kode_agen', $agen->kode_agen)
                ->where('nama_agen', $agen->nama_agen)
                ->update([
                    'nama_sales' => $validated['nama_sales']
                ]);

            // Jika tidak ada data toko dengan kombinasi baru + kode_agen + nama_agen
            // $updatedWithAgen = DaftarToko::where('nama_toko', $toko->nama_toko)
            //     ->where('pic', $request->pic)
            //     ->where('nomor_pic', $request->no_hp)
            //     ->where('lokasi_event', $validated['lokasi_event'])
            //     ->where('kota', $validated['kota'])
            //     ->where('kode_agen', $agen->kode_agen)
            //     ->where('nama_agen', $agen->nama_agen)
            //     ->count();

            // if ($updatedWithAgen == 0) {
            //     DaftarToko::where('nama_toko', $toko->nama_toko)
            //         ->where('pic', $request->pic)
            //         ->where('nomor_pic', $request->no_hp)
            //         ->where('lokasi_event', $validated['lokasi_event'])
            //         ->where('kota', $validated['kota'])
            //         ->update([
            //             'nama_sales' => $validated['nama_sales']
            //         ]);
            // }

            // Update form order header
            $formOrder->update([
                'kode_agen' => $agen->kode_agen,
                'nama_agen' => $agen->nama_agen,
                'kode_toko' => $toko->kode_toko,
                'nama_toko' => $toko->nama_toko,
                'nama_sales' => $validated['nama_sales'],
                'lokasi_event' => $validated['lokasi_event'],
                'brand' => $request->brand,
                'pic' => $request->pic,
                'no_hp' => $request->no_hp,
                'kota' => $request->kota,
            ]);

            // Hapus detail lama
            FormOrderDetail::where('form_order_id', $formOrder->id)->delete();

            // Simpan detail form order baru dan hitung total kupon
            $totalGrandPoint = 0;
            $totalKupon = 0;
            
            foreach ($validated['targets'] as $targetData) {
                $masterTarget = MasterTarget::findOrFail($targetData['master_target_id']);
                $jumlahPengambilan = $targetData['jumlah_pengambilan'] ?? 0;
                
                if ($jumlahPengambilan > 0) {
                    $totalPoint = $masterTarget->point * $jumlahPengambilan;
                    $totalGrandPoint += $totalPoint;
                    
                    $kuponPerPaket = $masterTarget->kupon ?? 0;
                    $totalKupon += $kuponPerPaket * $jumlahPengambilan;

                    FormOrderDetail::create([
                        'form_order_id' => $formOrder->id,
                        'master_target_id' => $masterTarget->id,
                        'paket' => $masterTarget->target,
                        'point_per_paket' => $masterTarget->point,
                        'jumlah_pengambilan' => $jumlahPengambilan,
                        'total_point' => $totalPoint,
                        'kupon_per_paket' => $kuponPerPaket,
                        'total_kupon' => $kuponPerPaket * $jumlahPengambilan,
                    ]);
                }
            }

            // Update total point dan total kupon di header
            $formOrder->update([
                'total_point' => $totalGrandPoint,
                'total_kupon' => $totalKupon
            ]);

            // UPDATE VOUCHER BERDASARKAN TOTAL KUPON
            $existingVoucherCount = $formOrder->vouchers()->count();
            
            if ($totalKupon > 0) {
                if ($existingVoucherCount == 0) {
                    // Generate voucher baru jika sebelumnya tidak ada voucher
                    $kodeUnik = $this->generateKodeUnik();
                    
                    for ($i = 0; $i < $totalKupon; $i++) {
                        $nomorVoucher = $this->generateNomorVoucher();
                        
                        DB::table('vouchers')->insert([
                            'kode_unik' => $kodeUnik,
                            'nomor_voucher' => $nomorVoucher,
                            'kode_toko' => $toko->kode_toko,
                            'nama_toko' => $toko->nama_toko,
                            'nama_pic' => $request->pic,
                            'no_hp' => $request->no_hp,
                            'lokasi_event' => $validated['lokasi_event'],
                            'form_order_id' => $formOrder->id,
                            'status' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    
                    $formOrder->update([
                        'jumlah_voucher' => $totalKupon,
                        'kode_unik_voucher' => $kodeUnik
                    ]);
                    
                } elseif ($totalKupon > $existingVoucherCount) {
                    // Tambah voucher jika kupon meningkat
                    $kodeUnik = $formOrder->kode_unik_voucher;
                    $voucherToAdd = $totalKupon - $existingVoucherCount;
                    
                    for ($i = 0; $i < $voucherToAdd; $i++) {
                        $nomorVoucher = $this->generateNomorVoucher();
                        
                        DB::table('vouchers')->insert([
                            'kode_unik' => $kodeUnik,
                            'nomor_voucher' => $nomorVoucher,
                            'kode_toko' => $toko->kode_toko,
                            'nama_toko' => $toko->nama_toko,
                            'nama_pic' => $request->pic,
                            'no_hp' => $request->no_hp,
                            'lokasi_event' => $validated['lokasi_event'],
                            'form_order_id' => $formOrder->id,
                            'status' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    
                    $formOrder->update([
                        'jumlah_voucher' => $totalKupon
                    ]);
                    
                } elseif ($totalKupon < $existingVoucherCount) {
                    // Kurangi voucher jika kupon menurun (hapus yang terakhir)
                    $vouchersToDelete = $existingVoucherCount - $totalKupon;
                    $formOrder->vouchers()
                        ->orderBy('id', 'desc')
                        ->limit($vouchersToDelete)
                        ->delete();
                        
                    $formOrder->update([
                        'jumlah_voucher' => $totalKupon
                    ]);
                }

                // Update data voucher yang tersisa dengan data toko terbaru
                $formOrder->vouchers()->update([
                    'kode_toko' => $toko->kode_toko,
                    'nama_toko' => $toko->nama_toko,
                    'nama_pic' => $request->pic,
                    'no_hp' => $request->no_hp,
                    'lokasi_event' => $validated['lokasi_event'],
                ]);
            } else {
                // Hapus semua voucher jika total kupon = 0
                if ($existingVoucherCount > 0) {
                    $formOrder->vouchers()->delete();
                    $formOrder->update([
                        'jumlah_voucher' => 0,
                        'kode_unik_voucher' => null
                    ]);
                }
            }

            DB::commit();

            // Simpan history
            try {
                $this->saveHistoryFormOrder($formOrder, 'update', $request, $totalGrandPoint, $totalKupon);
            } catch (\Exception $e) {
                \Log::error('Gagal simpan history form order: ' . $e->getMessage());
            }

            // Prepare success message dengan informasi tambahan
            $successMessage = 'Form order berhasil diupdate!';
            $successMessage .= " Data toko berhasil diupdate.";
            
            if ($totalKupon > 0) {
                $voucherChange = '';
                if ($existingVoucherCount == 0) {
                    $voucherChange = "Toko mendapatkan $totalKupon voucher undian. Kode Unik: " . $formOrder->kode_unik_voucher;
                } elseif ($totalKupon > $existingVoucherCount) {
                    $voucherChange = "Jumlah voucher bertambah menjadi $totalKupon voucher.";
                } elseif ($totalKupon < $existingVoucherCount) {
                    $voucherChange = "Jumlah voucher berkurang menjadi $totalKupon voucher.";
                } else {
                    $voucherChange = "Jumlah voucher tetap $totalKupon voucher.";
                }
                $successMessage .= " " . $voucherChange;
            } else {
                if ($existingVoucherCount > 0) {
                    $successMessage .= " Voucher telah dihapus karena tidak ada kupon yang didapat.";
                }
            }

            return redirect()->route('form-order.index')
                ->with('success', $successMessage)
                ->with('kode_unik', $formOrder->kode_unik_voucher)
                ->with('jumlah_voucher', $totalKupon)
                ->with('records_updated', $tokos->count());

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function saveHistoryFormOrder(FormOrder $formOrder, string $aksi, Request $request, int $totalPoint, int $totalKupon): void
    {
        // Snapshot detail targets
        $detailTargets = FormOrderDetail::where('form_order_id', $formOrder->id)
            ->get()
            ->map(fn($d) => [
                'master_target_id' => $d->master_target_id,
                'paket'            => $d->paket,
                'point_per_paket'  => $d->point_per_paket,
                'jumlah_pengambilan' => $d->jumlah_pengambilan,
                'total_point'      => $d->total_point,
                'kupon_per_paket'  => $d->kupon_per_paket,
                'total_kupon'      => $d->total_kupon,
            ])
            ->toArray();

        HistoryFormOrder::create([
            'form_order_id'     => $formOrder->id,
            'aksi'              => $aksi,
            'kode_agen'         => $formOrder->kode_agen,
            'nama_agen'         => $formOrder->nama_agen,
            'kode_toko'         => $formOrder->kode_toko,
            'nama_toko'         => $formOrder->nama_toko,
            'nama_sales'        => $formOrder->nama_sales,
            'lokasi_event'      => $formOrder->lokasi_event,
            'brand'             => $formOrder->brand,
            'pic'               => $formOrder->pic,
            'no_hp'             => $formOrder->no_hp,
            'kota'              => $formOrder->kota,
            'total_point'       => $totalPoint,
            'total_kupon'       => $totalKupon,
            'jumlah_voucher'    => $formOrder->jumlah_voucher ?? 0,
            'kode_unik_voucher' => $formOrder->kode_unik_voucher,
            'nama_terang'       => $formOrder->nama_terang,
            'detail_targets'    => $detailTargets,
            'user_id'           => auth()->id(),
            'username'          => auth()->check() ? auth()->user()->name : ($formOrder->nama_toko ?? 'guest'),
            'ip_address'        => $request->ip(),
        ]);
    }

    public function checkDuplicate(Request $request)
    {
        $namaAgen = $request->get('nama_agen');
        $namaToko = $request->get('nama_toko');
        $lokasiEvent = $request->get('lokasi_event');
        $kota = $request->get('kota');
        $pic = $request->get('pic');
        $noHp = $request->get('no_hp');
        
        // Validasi required fields
        if (!$namaAgen || !$namaToko || !$lokasiEvent || !$kota) {
            return response()->json(['exists' => false]);
        }
        
        // Query dengan kondisi yang sama seperti store method
        $exists = FormOrder::where('nama_agen', $namaAgen)
            ->where('nama_toko', $namaToko)
            ->where('lokasi_event', $lokasiEvent)
            ->where('kota', $kota)
            ->when($pic, function($query) use ($pic) {
                return $query->where('pic', $pic);
            })
            ->when($noHp, function($query) use ($noHp) {
                return $query->where('no_hp', $noHp);
            })
            ->exists();
        
        return response()->json(['exists' => $exists]);
    }

    // public function update(Request $request, FormOrder $formOrder)
    // {
    //     $validated = $request->validate([
    //         'nama_toko' => 'required|exists:daftar_toko,id',
    //         'nama_sales' => 'required|string|max:255',
    //         'targets' => 'required|array',
    //         'targets.*.master_target_id' => 'required|exists:master_targets,id',
    //         'targets.*.jumlah_pengambilan' => 'required|integer|min:0',
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         // Update data toko jika berubah
    //         $toko = DaftarToko::findOrFail($validated['nama_toko']);
    //         if ($toko->id != $formOrder->toko_id) {
    //             $formOrder->update([
    //                 'kode_toko' => $toko->kode_toko,
    //                 'nama_toko' => $toko->nama_toko,
    //                 'pic' => $request->pic,
    //                 'no_hp' => $request->no_hp,
    //                 'kota' => $request->kota,
    //             ]);
    //         }

    //         // Update nama sales
    //         $formOrder->update([
    //             'nama_sales' => $validated['nama_sales'],
    //         ]);

    //         // Hapus semua detail yang ada
    //         $formOrder->details()->delete();

    //         // Simpan detail yang baru
    //         $totalGrandPoint = 0;
    //         foreach ($validated['targets'] as $targetData) {
    //             $masterTarget = MasterTarget::findOrFail($targetData['master_target_id']);
    //             $jumlahPengambilan = $targetData['jumlah_pengambilan'] ?? 0;
                
    //             if ($jumlahPengambilan > 0) {
    //                 $totalPoint = $masterTarget->point * $jumlahPengambilan;
    //                 $totalGrandPoint += $totalPoint;

    //                 FormOrderDetail::create([
    //                     'form_order_id' => $formOrder->id,
    //                     'master_target_id' => $masterTarget->id,
    //                     'paket' => $masterTarget->target,
    //                     'point_per_paket' => $masterTarget->point,
    //                     'jumlah_pengambilan' => $jumlahPengambilan,
    //                     'total_point' => $totalPoint,
    //                 ]);
    //             }
    //         }

    //         // Update total point
    //         $formOrder->update(['total_point' => $totalGrandPoint]);

    //         DB::commit();

    //         return redirect()->route('form-order.index')
    //             ->with('success', 'Form order berhasil diupdate!');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
            
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
    //             ->withInput();
    //     }
    // }

    // public function destroy(FormOrder $formOrder)
    // {
    //     try {
    //         $formOrder->delete();

    //         return redirect()->route('form-order.index')
    //             ->with('success', 'Form order berhasil dihapus!');

    //     } catch (\Exception $e) {
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

    public function destroy(FormOrder $formOrder)
    {
        try {
            DB::beginTransaction();

            // Hapus semua voucher yang terkait
            $formOrder->vouchers()->delete();

            // Hapus form order
            $formOrder->delete();

            DB::commit();

            return redirect()->route('form-order.index')
                ->with('success', 'Form order berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}