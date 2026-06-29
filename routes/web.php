<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KomplainController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\MasterTargetController;
use App\Http\Controllers\MasterDoorPrizeController;
use App\Http\Controllers\User\PermintaanController;
use App\Http\Controllers\User\WelcomeController;
use App\Http\Controllers\OrderGatheringController;
use App\Http\Controllers\DaftarTokoController;
use App\Http\Controllers\DaftarAgenController;
use Illuminate\Support\Facades\Route;
use App\Exports\KomplainExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\FormOrderController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\MasterLokasiEventController;
use App\Http\Controllers\PeringkatController;
use App\Http\Controllers\DoorprizeController;
use App\Http\Controllers\PemenangController;
use App\Http\Controllers\SurveyAgenController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/cek-voucher', [App\Http\Controllers\VoucherController::class, 'cekVoucherPublic'])->name('voucher.public');
Route::post('/cek-voucher', [App\Http\Controllers\VoucherController::class, 'prosesCekVoucher'])->name('voucher.proses');

// // // // Route untuk halaman undian doorprize
// Route::get('/doorprize', [App\Http\Controllers\DoorprizeController::class, 'index'])->name('doorprize.index');
// Route::post('/doorprize/start', [App\Http\Controllers\DoorprizeController::class, 'startUndian'])->name('doorprize.start');
// Route::get('/doorprize/voucher-tersedia', [App\Http\Controllers\DoorprizeController::class, 'voucherTersedia'])->name('doorprize.voucher-tersedia');
// Route::get('/doorprize/animation-vouchers', [App\Http\Controllers\DoorprizeController::class, 'getAllVouchersForAnimation'])->name('doorprize.animation-vouchers');

// // Route dengan parameter lokasi event
// Route::get('/doorprize/{lokasi}', [App\Http\Controllers\DoorprizeController::class, 'index'])->name('doorprize.index');
// Route::post('/doorprize/{lokasi}/start', [App\Http\Controllers\DoorprizeController::class, 'startUndian'])->name('doorprize.start');
// Route::get('/doorprize/{lokasi}/voucher-tersedia', [App\Http\Controllers\DoorprizeController::class, 'voucherTersedia'])->name('doorprize.voucher-tersedia');
// Route::get('/doorprize/{lokasi}/animation-vouchers', [App\Http\Controllers\DoorprizeController::class, 'getAllVouchersForAnimation'])->name('doorprize.animation-vouchers');
// Route::get('/doorprize/{lokasi}/list-pemenang', [DoorprizeController::class, 'showWinnersPage'])
//     ->name('doorprize.winners');
// Route::get('/doorprize/{lokasi}/winners', [DoorprizeController::class, 'getWinners']);

// Route::get('/welcome', function () {
//     $user = Auth::user(); // Ambil data user yang sedang login
//     return view('dashboard', compact('user'));
// })->middleware(['auth', 'verified'])->name('welcome');

Route::get('/welcome', [WelcomeController::class, 'index'])->middleware(['auth', 'verified'])->name('welcome');
Route::get('/daftartoko/export', [App\Http\Controllers\DaftarTokoController::class, 'export'])->name('daftartoko.export');
Route::get('/daftartoko/export-tracking', [DaftarTokoController::class, 'exportTracking'])
    ->name('daftartoko.exportTracking');

// Halaman cepat untuk scan kode_toko dan input mandiri
Route::get('/inputformorder', [FormOrderController::class, 'scanCreate'])->name('form-order.scan');
// API lookup untuk kode toko dan agen
Route::get('/api/lookup-toko-by-kode', [FormOrderController::class, 'lookupByKodeToko'])->name('api.lookup.toko');
Route::get('/api/lookup-agen-by-kode', [FormOrderController::class, 'lookupAgenByKode'])->name('api.lookup.agen');
// Download voucher image
Route::get('/download-voucher-image', [FormOrderController::class, 'downloadVoucherImage'])->name('download.voucher.image');
Route::get('/form-order/success', [FormOrderController::class, 'success'])->name('form-order.success');

Route::resource('form-order', FormOrderController::class);

Route::get('/inputkehadiran', [KehadiranController::class, 'inputKehadiran'])->name('kehadiran.input');
Route::post('/kehadiran/submit', [KehadiranController::class, 'submitKehadiran'])->name('kehadiran.submit');
Route::get('/api/kehadiran/get-toko/{kode_toko}', [KehadiranController::class, 'getTokoByKode'])->name('api.kehadiran.get-toko');


// Fitur generate QR dan export PDF QR
Route::get('/daftartoko/generate-qr', [DaftarTokoController::class, 'generateQR'])->name('daftartoko.generate-qr');
Route::get('/daftartoko/export-qr-pdf', [DaftarTokoController::class, 'exportQRPDF'])->name('daftartoko.export-qr-pdf');

// Fitur generate QR dan export PDF QR untuk Agen
Route::get('/daftaragen/generate-qr', [DaftarAgenController::class, 'generateQR'])->name('daftaragen.generate-qr');
Route::get('/daftaragen/export-qr-pdf', [DaftarAgenController::class, 'exportQRPDF'])->name('daftaragen.export-qr-pdf');

Route::get('/daftartoko/export-tracking-csv', [DaftarTokoController::class, 'exportTrackingCSV'])
    ->name('daftartoko.exportTrackingCSV');

Route::get('/export-tracking-excel', [DaftarTokoController::class, 'exportTrackingExcel'])->name('daftartoko.exportTrackingExcel');
Route::get('/export-agen-excel', [DaftarAgenController::class, 'exportAgenExcel'])->name('daftaragen.exportAgenExcel');
Route::get('/daftartoko/export-rekapan-gabungan', [DaftarTokoController::class, 'exportRekapanGabunganExcel'])
    ->name('daftartoko.exportRekapanGabungan');

Route::get('/test-export', function() {
    try {
        $query = \App\Models\DaftarToko::query();
        
        $tokos = $query->selectRaw('
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
            ->limit(5)
            ->get();
            
        return response()->json([
            'success' => true,
            'count' => $tokos->count(),
            'data' => $tokos
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
Route::get('/daftaragen/export', [App\Http\Controllers\DaftarAgenController::class, 'export'])->name('daftaragen.export');

Route::get('/peringkat/export', [PeringkatController::class, 'exportExcel'])->name('peringkat.export');

// Cek duplikat nama toko dan agen
Route::get('/check-duplicate-order', [FormOrderController::class, 'checkDuplicate'])->name('check-duplicate-order');
Route::get('/api/check-existing-order', [FormOrderController::class, 'checkExistingOrder'])->name('api.check-existing-order');

// // Kehadiran
// Route::get('/kehadiran', [KehadiranController::class, 'index'])->name('kehadiran.index');
Route::post('/kehadiran/update', [KehadiranController::class, 'update'])->name('kehadiran.update');
Route::get('/kehadiran/export', [KehadiranController::class, 'export'])->name('kehadiran.export');

Route::get('/get-kota-by-kode', [FormOrderController::class, 'getKotaByKode'])->name('get-kota-by-kode');
Route::get('/get-toko-by-agen', [FormOrderController::class, 'getTokoByAgen'])->name('get-toko-by-agen');
// Route::get('/peringkat/data', [PeringkatController::class, 'getData'])->name('peringkat.data');


// Card peringkat (public)
Route::get('/ranking/{lokasi}', [App\Http\Controllers\RankingCardController::class, 'index'])->name('peringkat.lokasi');
Route::get('/ranking/{lokasi}/data', [App\Http\Controllers\RankingCardController::class, 'getData']);

// Form order (ambil nama sales)
Route::get('/get-nama-sales', [FormOrderController::class, 'getNamaSales'])->name('get-nama-sales');

// Route tambahan untuk trash dan restore
Route::get('/mastertarget/trash', [MasterTargetController::class, 'trash'])->name('mastertarget.trash');
Route::put('/mastertarget/{id}/restore', [MasterTargetController::class, 'restore'])->name('mastertarget.restore');

Route::get('/masterdoorprize/trash', [MasterDoorPrizeController::class, 'trash'])->name('masterdoorprize.trash');
Route::put('/masterdoorprize/{id}/restore', [MasterDoorPrizeController::class, 'restore'])->name('masterdoorprize.restore');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // History Form Order
    Route::get('/history-form-order', [App\Http\Controllers\HistoryFormOrderController::class, 'index'])->name('history-form-order.index');
    Route::get('/history-form-order/{id}', [App\Http\Controllers\HistoryFormOrderController::class, 'show'])->name('history-form-order.show');
    
    // Route untuk halaman list pemenang dengan status penukaran
    // Route::get('/pemenang/{lokasi}/list', [PemenangController::class, 'showPemenangPage'])->name('pemenang.list');
    // Route::get('doorprize/{lokasi}/list-klaim', [PemenangController::class, 'showPemenangPage'])->name('pemenang.list');
    // Route::get('/pemenang/{lokasi}/data', [PemenangController::class, 'getPemenangData'])->name('pemenang.data');
    // Route::post('/pemenang/{voucher}/update-status', [PemenangController::class, 'updateStatusPenukaran'])->name('pemenang.update-status');

    Route::get('/pemenang/list-klaim', [PemenangController::class, 'showPemenangPage'])->name('pemenang.list');
    Route::get('/pemenang/{lokasi}/data', [PemenangController::class, 'getPemenangData'])->name('pemenang.data');
    Route::post('/pemenang/{voucherId}/update-status', [PemenangController::class, 'updateStatusPenukaran'])->name('pemenang.update-status');

    Route::get('/peringkat', [PeringkatController::class, 'index'])->name('peringkat.index');
    Route::get('/api/peringkat/data', [App\Http\Controllers\PeringkatController::class, 'getData'])->name('api.peringkat.data');
    Route::get('/form-order/export-detail', [FormOrderController::class, 'exportDetail'])->name('form-order.export-detail');
    Route::get('/form-order/export', [FormOrderController::class, 'exportExcel'])->name('form-order.export');
    Route::get('/form-order/{formOrder}/pdf', [FormOrderController::class, 'pdf'])->name('form-order.pdf');

    // Aktifkan jika harus login
    // Route::get('/doorprize', [App\Http\Controllers\DoorprizeController::class, 'index'])->name('doorprize.index');
    // Route::post('/doorprize/start', [App\Http\Controllers\DoorprizeController::class, 'startUndian'])->name('doorprize.start');
    // Route::get('/doorprize/voucher-tersedia', [App\Http\Controllers\DoorprizeController::class, 'voucherTersedia'])->name('doorprize.voucher-tersedia');
    // Route::get('/doorprize/animation-vouchers', [App\Http\Controllers\DoorprizeController::class, 'getAllVouchersForAnimation'])->name('doorprize.animation-vouchers');

    // Route dengan parameter lokasi event
    Route::get('/doorprize/{lokasi}', [App\Http\Controllers\DoorprizeController::class, 'index'])->name('doorprize.index');
    Route::post('/doorprize/{lokasi}/start', [App\Http\Controllers\DoorprizeController::class, 'startUndian'])->name('doorprize.start');
    Route::get('/doorprize/{lokasi}/voucher-tersedia', [App\Http\Controllers\DoorprizeController::class, 'voucherTersedia'])->name('doorprize.voucher-tersedia');
    Route::get('/doorprize/{lokasi}/animation-vouchers', [App\Http\Controllers\DoorprizeController::class, 'getAllVouchersForAnimation'])->name('doorprize.animation-vouchers');
    Route::get('/doorprize/{lokasi}/list-pemenang', [DoorprizeController::class, 'showWinnersPage'])
        ->name('doorprize.winners');
    Route::get('/doorprize/{lokasi}/winners', [DoorprizeController::class, 'getWinners']);
    Route::get('/doorprize/{lokasi}/winners-by-doorprize/{doorprizeId}', [DoorprizeController::class, 'getWinnersByDoorprize']);

    // Route untuk undian doorprize per item
    Route::get('/doorprize/{lokasi}/{doorprizeId}', [DoorprizeController::class, 'singleDoorprize'])->name('doorprize.single');

    // Kehadiran
    Route::get('/kehadiran', [KehadiranController::class, 'index'])->name('kehadiran.index');

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');

    Route::resource('master-lokasi-event', MasterLokasiEventController::class);

    Route::controller(PermintaanController::class)->group(function () {
        route::get('/permintaan', 'indexPermintaan')->name('permintaan');
        route::post('/permintaan', 'storePermintaan')->name('permintaan.store');

        // Edit dan Hapus
        Route::get('/permintaan/{order}/edit', 'editOrder')->name('permintaan.edit');
        // Route::get('/permintaan/{order}/edit', [PermintaanController::class, 'editOrder']);
        Route::get('/permintaan/{id}/edit-html', 'editHtml');
        Route::put('/permintaan/{order}/update', 'updateOrder')->name('permintaan.update');
        Route::delete('/permintaan/{order}/delete', 'deleteOrder')->name('permintaan.delete');

        // Show
        Route::get('/permintaan/{order}/view', 'showOrder');

        // Route::post('/permintaan/{order}/update', 'updateOrder')->name('permintaan.update');
        Route::get('/permintaan/{order}/edit', 'editOrder')->name('permintaan.edit');


        // AJAX untuk pemilihan ukuran dan motif
        Route::get('/get-ukurans/{merkId}', 'getUkurans');
        Route::get('/get-motifs/{ukuranId}', 'getMotifs');

        // Generate PDF
        Route::get('/permintaan/{order}/pdf', 'generatePDF')->name('permintaan.pdf');

        // send WA
        Route::get('/send-wa/{order}', 'sendWA')->name('permintaan.sendWA');

        // send Email
        Route::get('/send-email/{order}', 'sendEmail')->name('permintaan.sendEmail');

        Route::resource('mastertarget', MasterTargetController::class);

        Route::resource('masterdoorprize', MasterDoorPrizeController::class);
    });

    // Master Data Toko
    Route::get('/daftartoko/rekapan-gabungan', [DaftarTokoController::class, 'rekapanGabungan'])
        ->name('daftartoko.rekapan-gabungan');
    Route::post('/daftartoko/update-hotel', [DaftarTokoController::class, 'updateHotel'])
        ->name('daftartoko.update-hotel');
    Route::post('/daftartoko/update-checkin', [DaftarTokoController::class, 'updateCheckin'])
        ->name('daftartoko.update-checkin');
    Route::resource('daftartoko', DaftarTokoController::class);
    Route::get('/daftaragen/import', [DaftarAgenController::class, 'showImportForm'])->name('daftaragen.import.form');
    Route::post('/daftaragen/import', [DaftarAgenController::class, 'import'])->name('daftaragen.import');
    Route::get('/daftaragen/template', [DaftarAgenController::class, 'downloadTemplate'])->name('daftaragen.template');
    Route::resource('daftaragen', DaftarAgenController::class);

    // Survey Agen (dengan login)
    Route::get('/form-survey/manage', [SurveyAgenController::class, 'index'])->name('form-survey.index');
    Route::get('/form-survey/scan-qr', [SurveyAgenController::class, 'scanQrPage'])->name('form-survey.scan-qr');
    Route::get('/form-survey/search', [SurveyAgenController::class, 'searchSurvey'])->name('form-survey.search');
    Route::get('/form-survey/{kodeSurvey}/detail', [SurveyAgenController::class, 'detail'])->name('form-survey.detail');
    Route::post('/form-survey/claim-reward', [SurveyAgenController::class, 'claimReward'])->name('form-survey.claim-reward');

    Route::resource('order-gathering', OrderGatheringController::class);

    Route::controller(KomplainController::class)->group(function () {
        // Komplain
        route::get('/komplain', 'indexKomplain')->name('komplain.index');
        route::get('/komplain-create', 'createKomplain')->name('komplain.create');
        route::post('/komplain-store', 'storeKomplain')->name('komplain.store');
        Route::get('/komplain/{id}/show', 'showKomplain')->name('komplain.show');
        Route::put('/komplain/{id}/update', 'updateKomplain')->name('komplain.update');

    });
    // Komplain Export
    Route::get('/komplain/export/pdf', [KomplainController::class, 'exportPdf'])->name('komplain.export.pdf');
    Route::get('/komplain/export/excel', [KomplainController::class, 'exportExcel'])->name('komplain.export.excel');

    // Send email dan wa Komplain
    Route::get('/send-wa-komplain/{id}', [KomplainController::class, 'sendWa']);
    Route::get('/send-email-komplain/{id}', [KomplainController::class, 'sendEmail']);



    // Wilayah
    Route::get('/kabupaten', [WilayahController::class, 'getKabupaten']);
    
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::controller(AdminController::class)->group(function () {
        Route::get('welcome', 'index');

        // Customer
        Route::get('customer', 'indexCustomer')->name('admin.customer');
        Route::post('customer', 'storeCustomer')->name('admin.customer.store');
        Route::get('api/customers/{id}', function ($id) {
            return \App\Models\User::findOrFail($id);
        });
        Route::put('customer/{id}', 'updateCustomer')->name('admin.customer.update');
        Route::delete('customer/{id}', 'deleteCustomer')->name('admin.customer.delete');

        // Merk
        Route::get('merk', 'indexMerk')->name('admin.merk');
        Route::post('merk', 'storeMerk')->name('admin.merk.store');
        Route::put('merk/{id}', 'updateMerk')->name('admin.merk.update');
        Route::delete('merk/{id}', 'deleteMerk')->name('admin.merk.delete');

        // Ukuran
        Route::get('ukuran', 'indexUkuran')->name('admin.ukuran');
        Route::post('ukuran', 'storeUkuran')->name('admin.ukuran.store');
        Route::put('ukuran/{id}', 'updateUkuran')->name('admin.ukuran.update');
        Route::delete('ukuran/{id}', 'deleteUkuran')->name('admin.ukuran.delete');

        // Motif
        Route::get('motif', 'indexMotif')->name('admin.motif');
        Route::post('motif', 'storeMotif')->name('admin.motif.store');
        Route::put('motif/{id}', 'updateMotif')->name('admin.motif.update');
        Route::delete('motif/{id}', 'deleteMotif')->name('admin.motif.delete');

        Route::get('/filter-motif', 'filterMotif')->name('motif.filter');

        // Tranksaksi
        Route::get('transaksi', 'indexTransaksi')->name('admin.transaksi');
        Route::get('/transaksi/{order}/edit', 'editTransaksi')->name('transaksi.edit');
        Route::put('/transaksi/{order}/update', 'updateTransaksi')->name('transaksi.update');
        Route::delete('/transaksi/{order}/delete', 'deleteTransaksi')->name('transaksi.delete');

        // AJAX untuk pemilihan ukuran dan motif
        Route::get('/get-ukurans/{merkId}', 'getUkurans')->name('get-ukurans');
        Route::get('/get-motifs/{ukuranId}', 'getMotifs')->name('get-motifs');

        Route::put('/transaksi/{order}/mark-paid', 'markPaid')->name('order.markPaid');

        // Show
        Route::get('/transaksi/{order}/view', 'showTransaksi');

        // PDF
        Route::get('/transaksi/{order}/pdf', 'generatePDF')->name('transaksi.pdf');

        // Send WA
        Route::post('/send-wa/transaksi/{order}',  'markPaid')->name('transaksi.markPaid');

        // Send Email
        Route::post('/send-email/transaksi/{order}', 'markPaidEmail')->name('transaksi.markPaidEmail');

        // Log
        Route::get('log', 'indexLog')->name('admin.log');

        // Download Forecast Period
        Route::get('/transaksi/generate-forecast', 'generateForecast')->name('admin.generateForecast');

        // Total Forecast
        Route::get('/transaksi/generate-totalforecast', 'generateTotalForecast')->name('admin.generateTotalForecast');
    });
});

// Survey Agen (tanpa login)
Route::get('/form-survey', [SurveyAgenController::class, 'create'])->name('form-survey.form');
Route::get('/form-survey/lookup-agen', [SurveyAgenController::class, 'lookupAgen'])->name('form-survey.lookup-agen');
Route::post('/form-survey', [SurveyAgenController::class, 'store'])->name('form-survey.store');
Route::get('/form-survey/{kodeSurvey}', [SurveyAgenController::class, 'show'])->name('form-survey.show');
Route::get('/form-survey/{kodeSurvey}/download-image', [SurveyAgenController::class, 'downloadImage'])->name('form-survey.download-image');

Route::fallback(function () {
    return response()->view('errors.custom-404', [], 404);
});

require __DIR__ . '/auth.php';
