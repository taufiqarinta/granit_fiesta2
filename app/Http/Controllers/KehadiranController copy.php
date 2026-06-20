<?php

namespace App\Http\Controllers;

use App\Models\DaftarToko;
use App\Models\DaftarAgen;
use App\Models\MasterLokasiEvent;
use App\Models\Wilayah;
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
                        ->orderBy('id', 'desc')
                        ->get();

        $daftarAgens = DaftarAgen::where('lokasi_event', $lokasiEvent)
                        ->where('status', 1)
                        ->orderBy('id', 'desc')
                        ->get();
        
        // Gabungkan data dengan grouping untuk duplikat
        $gabunganData = [];
        $groupedData = [];
        
        // Proses data daftar toko
        foreach ($daftarTokos as $item) {
            $uniqueKey = strtolower(trim($item->nama_toko)) . '|' . 
                        strtolower(trim($item->pic)) . '|' . 
                        strtolower(trim($item->nomor_pic)) . '|' . 
                        strtolower(trim($item->kota)) . '|';
            
            if (isset($groupedData[$uniqueKey])) {
                $groupedData[$uniqueKey]['agen_info'][] = [
                    'kode_agen' => $item->kode_agen,
                    'nama_agen' => $item->nama_agen,
                    'nama_sales' => $item->nama_sales
                ];
                $groupedData[$uniqueKey]['all_ids'][] = 'toko_' . $item->id;
            } else {
                $itemData = [
                    'id' => 'toko_' . $item->id,
                    'type' => 'toko',
                    'kode_toko' => $item->kode_toko,
                    'nama_toko' => $item->nama_toko,
                    'pic' => $item->pic,
                    'nomor_pic' => $item->nomor_pic,
                    'alamat' => $item->alamat,
                    'provinsi' => $item->provinsi,
                    'kota' => $item->kota,
                    'kode_agen' => $item->kode_agen,
                    'nama_agen' => $item->nama_agen,
                    'nama_sales' => $item->nama_sales,
                    'hadir' => $item->hadir,
                    'jumlah_kehadiran' => $item->jumlah_kehadiran,
                    'original_id' => $item->id,
                    'unique_key' => $uniqueKey,
                    'agen_info' => [[
                        'kode_agen' => $item->kode_agen,
                        'nama_agen' => $item->nama_agen,
                        'nama_sales' => $item->nama_sales
                    ]],
                    'all_ids' => ['toko_' . $item->id],
                    'main_id' => 'toko_' . $item->id
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
                'main_id' => 'agen_' . $item->id
            ];
            
            if (isset($groupedData[$uniqueKey])) {
                $groupedData[$uniqueKey]['all_ids'][] = 'agen_' . $item->id;
            } else {
                $groupedData[$uniqueKey] = $itemData;
            }
        }
        
        $gabunganData = array_values($groupedData);
        
        usort($gabunganData, function($a, $b) {
            return strcasecmp($a['nama_toko'], $b['nama_toko']);
        });
        
        $wilayahData = [];
        foreach ($gabunganData as $item) {
            $provinsi = Wilayah::where('kode', $item['provinsi'])->first();
            $kota = Wilayah::where('kode', $item['kota'])->first();
            
            $wilayahData[$item['id']] = [
                'kota' => $item['kota']
            ];
        }
        
        return view('kehadiran.index', compact('gabunganData', 'lokasiEvent', 'wilayahData', 'lokasiEvents'));
    }

    // public function update(Request $request)
    // {
    //     $request->validate([
    //         'id' => 'required|string',
    //         'hadir' => 'sometimes|boolean',
    //         'jumlah_kehadiran' => 'sometimes|integer|min:0',
    //         'nama_toko' => 'sometimes|string|max:255',
    //         'pic' => 'sometimes|string|max:255',
    //         'nomor_pic' => 'sometimes|string|max:20',
    //         'alamat' => 'sometimes|string',
    //         'kota' => 'sometimes|string|max:100'
    //     ]);

    //     try {
    //         // Pisahkan type dan original_id dari ID yang dikirim
    //         $idParts = explode('_', $request->id);
    //         $type = $idParts[0];
    //         $originalId = $idParts[1];

    //         if ($type === 'toko') {
    //             $peserta = DaftarToko::findOrFail($originalId);
    //         } else if ($type === 'agen') {
    //             $peserta = DaftarAgen::findOrFail($originalId);
    //         } else {
    //             return response()->json(['success' => false, 'message' => 'Type tidak valid'], 400);
    //         }

    //         $updateData = [];
            
    //         // Update semua field yang dikirim
    //         $fields = ['hadir', 'jumlah_kehadiran', 'nama_toko', 'pic', 'nomor_pic', 'alamat', 'kota'];
    //         foreach ($fields as $field) {
    //             if ($request->has($field)) {
    //                 // Untuk agen, field 'nama_toko' sebenarnya adalah 'nama_agen'
    //                 if ($type === 'agen' && $field === 'nama_toko') {
    //                     $updateData['nama_agen'] = $request->$field;
    //                 } else {
    //                     $updateData[$field] = $request->$field;
    //                 }
    //             }
    //         }
            
    //         $peserta->update($updateData);

    //         // Notify Node.js server dengan semua data yang di-update
    //         $this->notifyNodeJS($request->id, $peserta, $updateData);

    //         return response()->json(['success' => true]);

    //     } catch (\Exception $e) {
    //         \Log::error('Error updating kehadiran: ' . $e->getMessage());
    //         return response()->json(['success' => false, 'message' => 'Terjadi kesalahan'], 500);
    //     }
    // }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'hadir' => 'sometimes|boolean',
            'jumlah_kehadiran' => 'sometimes|integer|min:0',
            'nama_toko' => 'sometimes|string|max:255',
            'pic' => 'sometimes|string|max:255',
            'nomor_pic' => 'sometimes|string|max:20',
            'alamat' => 'sometimes|string',
            'kota' => 'sometimes|string|max:100'
        ]);

        try {
            // Pisahkan type dan original_id dari ID yang dikirim
            $idParts = explode('_', $request->id);
            $type = $idParts[0];
            $originalId = $idParts[1];

            if ($type === 'toko') {
                // Ambil data toko yang akan diupdate untuk mendapatkan nilai lama
                $peserta = DaftarToko::findOrFail($originalId);
                
                // Simpan nilai lama sebelum update
                $oldNamaToko = $peserta->nama_toko;
                $oldPic = $peserta->pic;
                $oldNomorPic = $peserta->nomor_pic;
                $oldKota = $peserta->kota;
                
                $updateData = [];
                
                // Update semua field yang dikirim
                $fields = ['hadir', 'jumlah_kehadiran', 'nama_toko', 'pic', 'nomor_pic', 'alamat', 'kota'];
                foreach ($fields as $field) {
                    if ($request->has($field)) {
                        $updateData[$field] = $request->$field;
                    }
                }
                
                // SELALU update SEMUA record yang memiliki kombinasi LAMA yang sama
                // Tidak peduli field apa yang diubah (hadir, jumlah_kehadiran, atau field grouping)
                $affectedRows = DaftarToko::where('nama_toko', $oldNamaToko)
                    ->where('pic', $oldPic)
                    ->where('nomor_pic', $oldNomorPic)
                    ->where('kota', $oldKota)
                    ->update($updateData);
                
                \Log::info("Updated {$affectedRows} toko records for combination: {$oldNamaToko}, {$oldPic}, {$oldNomorPic}, {$oldKota}");
                
                // Notify Node.js server dengan data yang di-update
                $this->notifyNodeJS($request->id, $peserta, $updateData);

            } else if ($type === 'agen') {
                $peserta = DaftarAgen::findOrFail($originalId);
                
                // Simpan nilai lama sebelum update
                $oldNamaAgen = $peserta->nama_agen;
                $oldPic = $peserta->pic;
                $oldNomorPic = $peserta->nomor_pic;
                $oldKota = $peserta->kota;
                
                $updateData = [];
                
                // Update semua field yang dikirim
                $fields = ['hadir', 'jumlah_kehadiran', 'pic', 'nomor_pic', 'alamat', 'kota'];
                foreach ($fields as $field) {
                    if ($request->has($field)) {
                        $updateData[$field] = $request->$field;
                    }
                }
                
                // Untuk agen, field 'nama_toko' sebenarnya adalah 'nama_agen'
                if ($request->has('nama_toko')) {
                    $updateData['nama_agen'] = $request->nama_toko;
                }
                
                // SELALU update SEMUA record yang memiliki kombinasi LAMA yang sama
                $affectedRows = DaftarAgen::where('nama_agen', $oldNamaAgen)
                    ->where('pic', $oldPic)
                    ->where('nomor_pic', $oldNomorPic)
                    ->where('kota', $oldKota)
                    ->update($updateData);
                
                \Log::info("Updated {$affectedRows} agen records for combination: {$oldNamaAgen}, {$oldPic}, {$oldNomorPic}, {$oldKota}");
                
                // Notify Node.js server dengan data yang di-update
                $this->notifyNodeJS($request->id, $peserta, $updateData);
                
            } else {
                return response()->json(['success' => false, 'message' => 'Type tidak valid'], 400);
            }

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
    
    private function notifyNodeJS($id, $peserta, $updateData = [])
    {
        $postData = [
            "id" => $id,
            "hadir" => $peserta->hadir,
            "jumlah_kehadiran" => $peserta->jumlah_kehadiran,
            "nama_toko" => $peserta->nama_toko ?? $peserta->nama_agen, // Untuk agen, gunakan nama_agen
            "pic" => $peserta->pic,
            "nomor_pic" => $peserta->nomor_pic,
            "alamat" => $peserta->alamat,
            "kota" => $peserta->kota
        ];

        // Gabungkan dengan data yang di-update
        $postData = array_merge($postData, $updateData);

        $ch = curl_init("https://web.kobin.co.id:3001/notify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
    }
}