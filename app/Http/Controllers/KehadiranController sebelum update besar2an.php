<?php

namespace App\Http\Controllers;

use App\Models\DaftarToko;
use App\Models\DaftarAgen;
use App\Models\MasterLokasiEvent;
use App\Models\Wilayah;
use App\Models\FormOrder; // Tambahkan model FormOrder
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KehadiranExport;

class KehadiranController extends Controller
{
    public function index()
    {
        $lokasiEvents = MasterLokasiEvent::where('status', 'Aktif')
            ->orderBy('tanggal', 'asc')
            ->get();

        $defaultLokasi = MasterLokasiEvent::where('status', 'Aktif')
            ->orderBy('tanggal', 'asc')
            ->first();
        
        $lokasiEvent = request('lokasi_event', $defaultLokasi ? $defaultLokasi->nama_lokasi : 'Semarang');
        
        // Ambil data dari kedua tabel
        $daftarTokos = DaftarToko::where('lokasi_event', $lokasiEvent)
                        ->where('status', 1)
                        ->orderBy('id', 'desc') // Urutkan ID descending untuk ambil yang terbesar
                        ->get();

        $daftarAgens = DaftarAgen::where('lokasi_event', $lokasiEvent)
                        ->where('status', 1)
                        ->orderBy('id', 'desc') // Urutkan ID descending untuk ambil yang terbesar
                        ->get();
        
        // Gabungkan data dengan grouping untuk duplikat
        $gabunganData = [];
        $groupedData = []; // Untuk menyimpan ID yang dikelompokkan
        
        // Proses data daftar toko
        foreach ($daftarTokos as $item) {
            // Buat key unik berdasarkan kombinasi field yang menentukan duplikat (TANPA agen dan sales)
            $uniqueKey = strtolower(trim($item->nama_toko)) . '|' . 
                        strtolower(trim($item->pic)) . '|' . 
                        strtolower(trim($item->nomor_pic)) . '|' . 
                        strtolower(trim($item->kota)) . '|';
            
            // Cek apakah sudah ada data dengan key yang sama
            if (isset($groupedData[$uniqueKey])) {
                // Tambahkan informasi agen ke data yang sudah ada
                $groupedData[$uniqueKey]['agen_info'][] = [
                    'kode_agen' => $item->kode_agen,
                    'nama_agen' => $item->nama_agen,
                    'nama_sales' => $item->nama_sales
                ];
                // Tambahkan ID ke grup yang sudah ada
                $groupedData[$uniqueKey]['all_ids'][] = 'toko_' . $item->id;
            } else {
                // Buat grup baru - ambil data dengan ID terbesar
                $itemData = [
                    'id' => 'toko_' . $item->id, // Gunakan ID terbesar sebagai ID utama
                    'type' => 'toko',
                    'kode_toko' => $item->kode_toko,
                    'nama_toko' => $item->nama_toko,
                    'pic' => $item->pic,
                    'nomor_pic' => $item->nomor_pic,
                    'alamat' => $item->alamat,
                    'provinsi' => $item->provinsi,
                    'kota' => $item->kota,
                    'kode_agen' => $item->kode_agen, // Tetap simpan agen pertama
                    'nama_agen' => $item->nama_agen, // Tetap simpan agen pertama
                    'nama_sales' => $item->nama_sales, // Tetap simpan sales pertama
                    'hadir' => $item->hadir,
                    'jumlah_kehadiran' => $item->jumlah_kehadiran,
                    'original_id' => $item->id,
                    'unique_key' => $uniqueKey,
                    'agen_info' => [[
                        'kode_agen' => $item->kode_agen,
                        'nama_agen' => $item->nama_agen,
                        'nama_sales' => $item->nama_sales
                    ]],
                    'all_ids' => ['toko_' . $item->id], // Simpan semua ID untuk reference
                    'main_id' => 'toko_' . $item->id // ID utama untuk update
                ];
                $groupedData[$uniqueKey] = $itemData;
            }
        }
        
        // Proses data daftar agen
        foreach ($daftarAgens as $item) {
            $uniqueKey = strtolower(trim($item->nama_agen)) . '|' . 
                        strtolower(trim($item->pic)) . '|' . 
                        strtolower(trim($item->nomor_pic)) . '|' . 
                        strtolower(trim($item->kota)) . '|';
        
            $itemData = [
                'id' => 'agen_' . $item->id,
                'type' => 'agen',
                'kode_toko' => $item->kode_agen,
                'nama_toko' => $item->nama_agen,
                'pic' => $item->pic,
                'nomor_pic' => $item->nomor_pic,
                'alamat' => $item->alamat,
                'provinsi' => $item->provinsi,
                'kota' => $item->kota,
                'kode_agen' => '-',
                'nama_agen' => '-',
                'nama_sales' => '-',
                'hadir' => $item->hadir,
                'jumlah_kehadiran' => $item->jumlah_kehadiran,
                'original_id' => $item->id,
                'unique_key' => $uniqueKey,
                'agen_info' => [[
                    'kode_agen' => '-',
                    'nama_agen' => '-',
                    'nama_sales' => '-'
                ]],
                'all_ids' => ['agen_' . $item->id],
                'main_id' => 'agen_' . $item->id // ID utama untuk update
            ];
            
            if (isset($groupedData[$uniqueKey])) {
                $groupedData[$uniqueKey]['all_ids'][] = 'agen_' . $item->id;
            } else {
                $groupedData[$uniqueKey] = $itemData;
            }
        }
        
        // Konversi groupedData menjadi array untuk gabunganData
        $gabunganData = array_values($groupedData);
        
        // Urutkan berdasarkan nama (case insensitive)
        usort($gabunganData, function($a, $b) {
            return strcasecmp($a['nama_toko'], $b['nama_toko']);
        });
        
        // Ambil data wilayah untuk provinsi dan kota
        $wilayahData = [];
        foreach ($gabunganData as $item) {
            $provinsi = Wilayah::where('kode', $item['provinsi'])->first();
            $kota = Wilayah::where('kode', $item['kota'])->first();
            
            $wilayahData[$item['id']] = [
                // 'provinsi' => $provinsi->nama ?? $item['provinsi'],
                'kota' => $item['kota']
            ];
        }
        
        return view('kehadiran.index', compact('gabunganData', 'lokasiEvent', 'wilayahData', 'lokasiEvents'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'hadir' => 'sometimes|boolean',
            'jumlah_kehadiran' => 'sometimes|integer|min:0'
        ]);

        try {
            // Pisahkan type dan original_id dari ID yang dikirim
            $idParts = explode('_', $request->id);
            $type = $idParts[0];
            $originalId = $idParts[1];

            if ($type === 'toko') {
                $peserta = DaftarToko::findOrFail($originalId);
            } else if ($type === 'agen') {
                $peserta = DaftarAgen::findOrFail($originalId);
            } else {
                return response()->json(['success' => false, 'message' => 'Type tidak valid'], 400);
            }

            $updateData = [];
            if ($request->has('hadir')) {
                $updateData['hadir'] = $request->hadir;
            }
            if ($request->has('jumlah_kehadiran')) {
                $updateData['jumlah_kehadiran'] = $request->jumlah_kehadiran;
            }
            
            // Hanya update 1 data saja (data yang sedang di-edit)
            $peserta->update($updateData);

            // Notify Node.js server hanya untuk ID yang di-update
            $this->notifyNodeJS($request->id, $peserta);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('Error updating kehadiran: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan'], 500);
        }
    }
    
    public function export(Request $request)
    {
        $lokasiEvent = $request->get('lokasi_event', 'Semarang');
        $fileName = 'Kehadiran_' . str_replace(' ', '_', $lokasiEvent) . '_' . date('Y-m-d_His') . '.xlsx';
        
        return Excel::download(new KehadiranExport($lokasiEvent), $fileName);
    }
    
    private function notifyNodeJS($id, $peserta)
    {
        $postData = [
            "id" => $id,
            "hadir" => $peserta->hadir,
            "jumlah_kehadiran" => $peserta->jumlah_kehadiran
        ];

        $ch = curl_init("https://web.kobin.co.id:3001/notify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
    }
}