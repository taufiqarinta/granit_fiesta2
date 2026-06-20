<?php

namespace App\Http\Controllers;

use App\Models\DaftarToko;
use App\Models\DaftarAgen;
use App\Models\MasterLokasiEvent;
use App\Models\Wilayah;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KehadiranExport;
use Browser;

class KehadiranController extends Controller
{

    public function index()
    {
        // Ambil semua lokasi event (termasuk yang status 0)
        $lokasiEvents = MasterLokasiEvent::orderBy('tanggal', 'asc')->get();

        $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
            ->orderBy('tanggal', 'asc')
            ->first();
        
        // Ambil parameter lokasi_event dari request, jika tidak ada gunakan default
        $lokasiEvent = request('lokasi_event', $defaultLokasi ? $defaultLokasi->nama_lokasi : 'semua');
        
        // Query untuk data toko dan agen
        $daftarTokos = DaftarToko::where('status', 1)
                        ->when($lokasiEvent != 'semua', function($query) use ($lokasiEvent) {
                            return $query->where('lokasi_event', $lokasiEvent);
                        })
                        ->orderBy('id', 'asc')
                        ->get();

        $daftarAgens = DaftarAgen::where('status', 1)
                        ->when($lokasiEvent != 'semua', function($query) use ($lokasiEvent) {
                            return $query->where('lokasi_event', $lokasiEvent);
                        })
                        ->orderBy('id', 'asc')
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
                    'waktu_kehadiran' => $item->waktu_kehadiran,
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
                'waktu_kehadiran' => $item->waktu_kehadiran,
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
        
        return view('kehadiran.index', compact('gabunganData', 'lokasiEvent', 'wilayahData', 'lokasiEvents', 'defaultLokasi'));
    }

    // Paling fix sebelum ada filter lokasi event
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
    //         $idParts = explode('_', $request->id);
    //         $type = $idParts[0];
    //         $originalId = $idParts[1];

    //         if ($type === 'toko') {
    //             $peserta = DaftarToko::findOrFail($originalId);
                
    //             // Simpan nilai lama SEBELUM mengambil data request
    //             $oldNamaToko = $peserta->nama_toko;
    //             $oldPic = $peserta->pic;
    //             $oldNomorPic = $peserta->nomor_pic;
    //             $oldKota = $peserta->kota;
                
    //             $updateData = [];
    //             $fields = ['hadir', 'jumlah_kehadiran', 'nama_toko', 'pic', 'nomor_pic', 'alamat', 'kota'];
                
    //             foreach ($fields as $field) {
    //                 if ($request->has($field)) {
    //                     // Ubah ke huruf besar untuk field teks
    //                     if (in_array($field, ['nama_toko', 'pic', 'nomor_pic', 'alamat', 'kota'])) {
    //                         $updateData[$field] = strtoupper($request->$field);
    //                     } else {
    //                         $updateData[$field] = $request->$field;
    //                     }
    //                 }
    //             }
                
    //             // TAMBAHKAN LOGIKA UNTUK WAKTU_KEHADIRAN
    //             if ($request->has('hadir') && $request->hadir == 1) {
    //                 $updateData['waktu_kehadiran'] = now()->format('H:i:s');
    //             } elseif ($request->has('hadir') && $request->hadir == 0) {
    //                 $updateData['waktu_kehadiran'] = null;
    //             }
                
    //             // Update semua record dengan kombinasi LAMA
    //             $affectedRows = DaftarToko::where('nama_toko', $oldNamaToko)
    //                 ->where('pic', $oldPic)
    //                 ->where('nomor_pic', $oldNomorPic)
    //                 ->where('kota', $oldKota)
    //                 ->update($updateData);
                
    //             // Ambil data TERBARU untuk dikirim ke socket
    //             $latestData = DaftarToko::find($originalId);

    //         } else if ($type === 'agen') {
    //             $peserta = DaftarAgen::findOrFail($originalId);
                
    //             // Simpan nilai lama SEBELUM mengambil data request
    //             $oldNamaAgen = $peserta->nama_agen;
    //             $oldPic = $peserta->pic;
    //             $oldNomorPic = $peserta->nomor_pic;
    //             $oldKota = $peserta->kota;
                
    //             $updateData = [];
    //             $fields = ['hadir', 'jumlah_kehadiran', 'pic', 'nomor_pic', 'alamat', 'kota'];
                
    //             foreach ($fields as $field) {
    //                 if ($request->has($field)) {
    //                     if (in_array($field, ['nama_toko', 'pic', 'nomor_pic', 'alamat', 'kota'])) {
    //                         $updateData[$field] = strtoupper($request->$field);
    //                     } else {
    //                         $updateData[$field] = $request->$field;
    //                     }
    //                 }
    //             }

    //             if ($request->has('nama_toko')) {
    //                 $updateData['nama_agen'] = strtoupper($request->nama_toko);
    //             }
                
    //             // TAMBAHKAN LOGIKA UNTUK WAKTU_KEHADIRAN
    //             if ($request->has('hadir') && $request->hadir == 1) {
    //                 $updateData['waktu_kehadiran'] = now()->format('H:i:s');
    //             } elseif ($request->has('hadir') && $request->hadir == 0) {
    //                 $updateData['waktu_kehadiran'] = null;
    //             }
                
    //             // Update semua record dengan kombinasi LAMA
    //             $affectedRows = DaftarAgen::where('nama_agen', $oldNamaAgen)
    //                 ->where('pic', $oldPic)
    //                 ->where('nomor_pic', $oldNomorPic)
    //                 ->where('kota', $oldKota)
    //                 ->update($updateData);
                
    //             // Ambil data TERBARU untuk dikirim ke socket
    //             $latestData = DaftarAgen::find($originalId);
                
    //         } else {
    //             return response()->json(['success' => false, 'message' => 'Type tidak valid'], 400);
    //         }

    //         // Kirim data TERBARU ke socket, bukan data request
    //         $this->notifyNodeJS($request->id, $latestData, $updateData);

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
            'kota' => 'sometimes|string|max:100',
            'lokasi_event' => 'sometimes|string', // ← tambah ini
        ]);

        try {
            $idParts = explode('_', $request->id);
            $type = $idParts[0];
            $originalId = $idParts[1];

            if ($type === 'toko') {
                $peserta = DaftarToko::findOrFail($originalId);
                
                $oldNamaToko = $peserta->nama_toko;
                $oldPic = $peserta->pic;
                $oldNomorPic = $peserta->nomor_pic;
                $oldKota = $peserta->kota;
                $lokasiEvent = $peserta->lokasi_event; // ← ambil dari record

                $updateData = [];
                $fields = ['hadir', 'jumlah_kehadiran', 'nama_toko', 'pic', 'nomor_pic', 'alamat', 'kota'];
                
                foreach ($fields as $field) {
                    if ($request->has($field)) {
                        if (in_array($field, ['nama_toko', 'pic', 'nomor_pic', 'alamat', 'kota'])) {
                            $updateData[$field] = strtoupper($request->$field);
                        } else {
                            $updateData[$field] = $request->$field;
                        }
                    }
                }
                
                if ($request->has('hadir') && $request->hadir == 1) {
                    $updateData['waktu_kehadiran'] = now()->format('H:i:s');
                } elseif ($request->has('hadir') && $request->hadir == 0) {
                    $updateData['waktu_kehadiran'] = null;
                }
                
                // ← tambahkan where lokasi_event
                $affectedRows = DaftarToko::where('nama_toko', $oldNamaToko)
                    ->where('pic', $oldPic)
                    ->where('nomor_pic', $oldNomorPic)
                    ->where('kota', $oldKota)
                    ->where('lokasi_event', $lokasiEvent) // ← ini
                    ->update($updateData);
                
                $latestData = DaftarToko::find($originalId);

                try {
                    LogAktivitas::create([
                        'user_id' => auth()->id() ?? null,
                        'username' => auth()->check() ? auth()->user()->name : ($latestData->nama_toko ?? 'guest'),
                        'aksi' => 'Ubah',
                        'fitur' => 'Kehadiran',
                        'deskripsi' => "Memperbarui kehadiran toko {$latestData->kode_toko} - {$latestData->nama_toko} | status: " . ($latestData->hadir ? 'Hadir' : 'Tidak Hadir') . " | jumlah: {$latestData->jumlah_kehadiran}",
                        'ip_address' => $request->ip(),
                        'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                        'created_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Gagal menyimpan log aktivitas update kehadiran toko: ' . $e->getMessage());
                }

            } else if ($type === 'agen') {
                $peserta = DaftarAgen::findOrFail($originalId);
                
                $oldNamaAgen = $peserta->nama_agen;
                $oldPic = $peserta->pic;
                $oldNomorPic = $peserta->nomor_pic;
                $oldKota = $peserta->kota;
                $lokasiEvent = $peserta->lokasi_event; // ← ambil dari record

                $updateData = [];
                $fields = ['hadir', 'jumlah_kehadiran', 'pic', 'nomor_pic', 'alamat', 'kota'];
                
                foreach ($fields as $field) {
                    if ($request->has($field)) {
                        if (in_array($field, ['nama_toko', 'pic', 'nomor_pic', 'alamat', 'kota'])) {
                            $updateData[$field] = strtoupper($request->$field);
                        } else {
                            $updateData[$field] = $request->$field;
                        }
                    }
                }

                if ($request->has('nama_toko')) {
                    $updateData['nama_agen'] = strtoupper($request->nama_toko);
                }
                
                if ($request->has('hadir') && $request->hadir == 1) {
                    $updateData['waktu_kehadiran'] = now()->format('H:i:s');
                } elseif ($request->has('hadir') && $request->hadir == 0) {
                    $updateData['waktu_kehadiran'] = null;
                }
                
                // ← tambahkan where lokasi_event
                $affectedRows = DaftarAgen::where('nama_agen', $oldNamaAgen)
                    ->where('pic', $oldPic)
                    ->where('nomor_pic', $oldNomorPic)
                    ->where('kota', $oldKota)
                    ->where('lokasi_event', $lokasiEvent) // ← ini
                    ->update($updateData);
                
                $latestData = DaftarAgen::find($originalId);

                try {
                    LogAktivitas::create([
                        'user_id' => auth()->id() ?? null,
                        'username' => auth()->check() ? auth()->user()->name : ($latestData->nama_agen ?? 'guest'),
                        'aksi' => 'Ubah',
                        'fitur' => 'Kehadiran',
                        'deskripsi' => "Memperbarui kehadiran agen {$latestData->kode_agen} - {$latestData->nama_agen} | status: " . ($latestData->hadir ? 'Hadir' : 'Tidak Hadir') . " | jumlah: {$latestData->jumlah_kehadiran}",
                        'ip_address' => $request->ip(),
                        'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                        'created_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Gagal menyimpan log aktivitas update kehadiran agen: ' . $e->getMessage());
                }
            }

            $this->notifyNodeJS($request->id, $latestData, $updateData);
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

    private function notifyNodeJS($id, $latestData, $updateData = [], $oldData = [], $allIds = [])
    {
        $postData = [
            "id"               => $id,
            "all_ids"          => implode(',', $allIds ?: [$id]), // ← kirim semua ID
            "hadir"            => $latestData->hadir,
            "jumlah_kehadiran" => $latestData->jumlah_kehadiran,
            "waktu_kehadiran"  => $latestData->waktu_kehadiran,
            "nama_toko"        => $latestData->nama_toko ?? $latestData->nama_agen,
            "pic"              => $latestData->pic,
            "nomor_pic"        => $latestData->nomor_pic,
            "alamat"           => $latestData->alamat,
            "kota"             => $latestData->kota,
        ];

        if (!empty($oldData)) {
            $postData = array_merge($postData, $oldData);
        }
        $postData = array_merge($postData, $updateData);

        $ch = curl_init("https://nodejs.kobin.co.id:443/notify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Menampilkan form input kehadiran dengan scan QR atau manual input kode toko
     */
    public function inputKehadiran()
    {
        $lokasiEvents = MasterLokasiEvent::where('status', 'aktif')->orderBy('tanggal', 'asc')->get();
        return view('kehadiran.input', compact('lokasiEvents'));
    }

    /**
     * API endpoint untuk fetch data toko berdasarkan kode_toko
     */
    public function getTokoByKode($kodeToko)
    {
        try {
            $kodeToko = strtoupper(trim($kodeToko));

            // Tidak filter lokasi_event — biarkan data toko sendiri yang menentukan
            $toko = DaftarToko::where('kode_toko', $kodeToko)
                ->where('status', 1)
                ->first();

            if ($toko) {
                $tokoGroup = DaftarToko::where('status', 1)
                    ->where('nama_toko', $toko->nama_toko)
                    ->where('pic', $toko->pic)
                    ->where('nomor_pic', $toko->nomor_pic)
                    ->where('kota', $toko->kota)
                    ->where('lokasi_event', $toko->lokasi_event)
                    ->get(['kode_agen', 'nama_agen', 'nama_sales']);

                $agenInfo = $tokoGroup
                    ->unique(function ($item) {
                        return strtolower(trim($item->kode_agen)) . '|' . strtolower(trim($item->nama_agen));
                    })
                    ->map(function ($item) {
                        return [
                            'kode_agen' => $item->kode_agen,
                            'nama_agen' => $item->nama_agen,
                            'nama_sales' => $item->nama_sales,
                        ];
                    })
                    ->values();

                return response()->json([
                    'success' => true,
                    'type' => 'toko',
                    'data' => [
                        'tipe' => 'TOKO',
                        'id' => 'toko_' . $toko->id,
                        'kode_toko' => $toko->kode_toko,
                        'nama_toko' => $toko->nama_toko,
                        'pic' => $toko->pic,
                        'nomor_pic' => $toko->nomor_pic,
                        'alamat' => $toko->alamat,
                        'kota' => $toko->kota,
                        'provinsi' => $toko->provinsi,
                        'kode_agen' => $toko->kode_agen,
                        'nama_agen' => $toko->nama_agen,
                        'nama_sales' => $toko->nama_sales,
                        'lokasi_event' => $toko->lokasi_event,
                        'hadir' => $toko->hadir,
                        'jumlah_kehadiran' => $toko->jumlah_kehadiran,
                        'waktu_kehadiran' => $toko->waktu_kehadiran,
                        'agen_info' => $agenInfo,
                    ]
                ]);
            }

            // Agen — pakai filter lokasi aktif
            $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
                ->orderBy('tanggal', 'asc')
                ->first();

            $agen = DaftarAgen::where('kode_agen', $kodeToko)
                ->where('status', 1)
                ->when($defaultLokasi, fn($q) => $q->where('lokasi_event', $defaultLokasi->nama_lokasi))
                ->first();

            if ($agen) {
                return response()->json([
                    'success' => true,
                    'type' => 'agen',
                    'data' => [
                        'tipe' => 'AGEN',
                        'id' => 'agen_' . $agen->id,
                        'kode_toko' => $agen->kode_agen,
                        'nama_toko' => $agen->nama_agen,
                        'pic' => $agen->pic,
                        'nomor_pic' => $agen->nomor_pic,
                        'alamat' => $agen->alamat,
                        'kota' => $agen->kota,
                        'provinsi' => $agen->provinsi,
                        'kode_agen' => '-',
                        'nama_agen' => '-',
                        'nama_sales' => '-',
                        'lokasi_event' => $agen->lokasi_event,
                        'hadir' => $agen->hadir,
                        'jumlah_kehadiran' => $agen->jumlah_kehadiran,
                        'waktu_kehadiran' => $agen->waktu_kehadiran,
                        'agen_info' => [[
                            'kode_agen' => $agen->kode_agen,
                            'nama_agen' => $agen->nama_agen,
                            'nama_sales' => '-',
                        ]],
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Kode toko tidak ditemukan'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error fetching toko by kode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data'
            ], 500);
        }
    }

    /**
     * Submit form input kehadiran
     */
    public function submitKehadiran(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'hadir' => 'required|boolean',
            'jumlah_kehadiran' => 'required|integer|min:0',
            'nama_toko' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'nomor_pic' => 'required|string|max:20',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100'
        ]);

        try {
            $idParts = explode('_', $request->id);
            $type = $idParts[0];
            $originalId = $idParts[1];

            if ($type === 'toko') {
                $updateData = [
                    'hadir' => $request->hadir ? 1 : 0,
                    'jumlah_kehadiran' => $request->jumlah_kehadiran,
                    'nama_toko' => strtoupper($request->nama_toko),
                    'pic' => strtoupper($request->pic),
                    'nomor_pic' => strtoupper($request->nomor_pic),
                    'alamat' => strtoupper($request->alamat),
                    'kota' => strtoupper($request->kota),
                ];

                if ($request->hadir == 1) {
                    $updateData['waktu_kehadiran'] = now()->format('H:i:s');
                } else {
                    $updateData['waktu_kehadiran'] = null;
                }

                $peserta = DaftarToko::findOrFail($originalId);
                $oldNamaToko = $peserta->nama_toko;
                $oldPic = $peserta->pic;
                $oldNomorPic = $peserta->nomor_pic;
                $oldKota = $peserta->kota;
                $lokasiEvent = $peserta->lokasi_event;

                $oldData = [
                    'old_nama_toko' => $oldNamaToko,
                    'old_pic' => $oldPic,
                    'old_nomor_pic' => $oldNomorPic,
                    'old_kota' => $oldKota
                ];

                DaftarToko::where('nama_toko', $oldNamaToko)
                    ->where('pic', $oldPic)
                    ->where('nomor_pic', $oldNomorPic)
                    ->where('kota', $oldKota)
                    ->where('lokasi_event', $lokasiEvent)
                    ->update($updateData);

                $latestData = DaftarToko::find($originalId);

                // Ambil SEMUA record yang ter-update untuk dapat semua ID-nya
                $affectedRecords = DaftarToko::where('nama_toko', $updateData['nama_toko'])
                    ->where('pic', $updateData['pic'])
                    ->where('nomor_pic', $updateData['nomor_pic'])
                    ->where('kota', $updateData['kota'])
                    ->where('lokasi_event', $lokasiEvent)
                    ->where('status', 1)
                    ->pluck('id')
                    ->map(fn($id) => 'toko_' . $id)
                    ->toArray();

                $latestData = DaftarToko::find($originalId);
                $notifyId = 'toko_' . $originalId;

                try {
                    LogAktivitas::create([
                        'user_id' => auth()->id() ?? null,
                        'username' => auth()->check() ? auth()->user()->name : ($latestData->nama_toko ?? 'guest'),
                        'aksi' => 'Ubah',
                        'fitur' => 'Kehadiran (Tidak Login)',
                        'deskripsi' => "Memperbarui kehadiran toko {$latestData->kode_toko} - {$latestData->nama_toko} | status: " . ($latestData->hadir ? 'Hadir' : 'Tidak Hadir') . " | jumlah: {$latestData->jumlah_kehadiran}",
                        'ip_address' => $request->ip(),
                        'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                        'created_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Gagal menyimpan log aktivitas kehadiran toko: ' . $e->getMessage());
                }

                $this->notifyNodeJS($notifyId, $latestData, $updateData, $oldData ?? [], $affectedRecords);

            } else if ($type === 'agen') {
                $updateData = [
                    'hadir' => $request->hadir ? 1 : 0,
                    'jumlah_kehadiran' => $request->jumlah_kehadiran,
                    'nama_agen' => strtoupper($request->nama_toko),
                    'pic' => strtoupper($request->pic),
                    'nomor_pic' => strtoupper($request->nomor_pic),
                    'alamat' => strtoupper($request->alamat),
                    'kota' => strtoupper($request->kota),
                ];

                if ($request->hadir == 1) {
                    $updateData['waktu_kehadiran'] = now()->format('H:i:s');
                } else {
                    $updateData['waktu_kehadiran'] = null;
                }

                $peserta = DaftarAgen::findOrFail($originalId);
                $oldNamaAgen = $peserta->nama_agen;
                $oldPic = $peserta->pic;
                $oldNomorPic = $peserta->nomor_pic;
                $oldKota = $peserta->kota;
                $lokasiEvent = $peserta->lokasi_event;

                $oldData = [
                    'old_nama_toko' => $oldNamaAgen,
                    'old_pic' => $oldPic,
                    'old_nomor_pic' => $oldNomorPic,
                    'old_kota' => $oldKota
                ];

                DaftarAgen::where('nama_agen', $oldNamaAgen)
                    ->where('pic', $oldPic)
                    ->where('nomor_pic', $oldNomorPic)
                    ->where('kota', $oldKota)
                    ->where('lokasi_event', $lokasiEvent)
                    ->update($updateData);

                $latestData = DaftarAgen::find($originalId);

                $affectedRecords = DaftarAgen::where('nama_agen', $updateData['nama_agen'])
                    ->where('pic', $updateData['pic'])
                    ->where('nomor_pic', $updateData['nomor_pic'])
                    ->where('kota', $updateData['kota'])
                    ->where('lokasi_event', $lokasiEvent)
                    ->where('status', 1)
                    ->pluck('id')
                    ->map(fn($id) => 'agen_' . $id)
                    ->toArray();

                $latestData = DaftarAgen::find($originalId);
                $notifyId = 'agen_' . $originalId;

                try {
                    LogAktivitas::create([
                        'user_id' => auth()->id() ?? null,
                        'username' => auth()->check() ? auth()->user()->name : ($latestData->nama_agen ?? 'guest'),
                        'aksi' => 'Ubah',
                        'fitur' => 'Kehadiran',
                        'deskripsi' => "Memperbarui kehadiran agen {$latestData->kode_agen} - {$latestData->nama_agen} | status: " . ($latestData->hadir ? 'Hadir' : 'Tidak Hadir') . " | jumlah: {$latestData->jumlah_kehadiran}",
                        'ip_address' => $request->ip(),
                        'device' => Browser::browserName() . ' on ' . Browser::platformName(),
                        'created_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Gagal menyimpan log aktivitas kehadiran agen: ' . $e->getMessage());
                }

                $this->notifyNodeJS($notifyId, $latestData, $updateData, $oldData ?? [], $affectedRecords);

            } else {
                return response()->json(['success' => false, 'message' => 'Type tidak valid'], 400);
            }

            $this->notifyNodeJS($notifyId, $latestData, $updateData, $oldData ?? []);

            return response()->json([
                'success' => true,
                'message' => 'Data kehadiran berhasil disimpan',
                'data' => [
                    'id' => $notifyId,
                    'nama_toko' => $latestData->nama_toko ?? $latestData->nama_agen,
                    'pic' => $latestData->pic,
                    'nomor_pic' => $latestData->nomor_pic,
                    'kota' => $latestData->kota,
                    'alamat' => $latestData->alamat,
                    'hadir' => $latestData->hadir,
                    'jumlah_kehadiran' => $latestData->jumlah_kehadiran,
                    'waktu_kehadiran' => $latestData->waktu_kehadiran
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error submitting kehadiran: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}