<?php

namespace App\Http\Controllers;

use App\Models\DoorprizeKehadiranPemenang;
use App\Models\MasterLokasiEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PemenangKehadiranController extends Controller
{
    /**
     * Menampilkan halaman list pemenang dengan filter lokasi
     */
    public function showPemenangPage(Request $request)
    {
        // Ambil semua lokasi event yang tersedia
        $lokasiEvents = MasterLokasiEvent::all();

        // Ambil lokasi aktif pertama berdasarkan tanggal (yang paling awal)
        $defaultLokasi = MasterLokasiEvent::where('status', 'aktif')
            ->orderBy('tanggal', 'asc')
            ->first();

        // Ambil lokasi dari request atau default ke lokasi aktif pertama
        $selectedLokasi = $request->get('lokasi', $defaultLokasi->nama_lokasi ?? '');

        return view('pemenangkehadiran.list', [
            'lokasiEvents' => $lokasiEvents,
            'selectedLokasi' => $selectedLokasi,
            'defaultLokasi' => $defaultLokasi
        ]);
    }

    /**
     * Get data pemenang untuk datatable
     */
    public function getPemenangData($lokasi)
    {
        try {
            $lokasi = strtoupper($lokasi);

            $perPage = request('per_page', 100);

            $winners = DoorprizeKehadiranPemenang::where('lokasi_event', $lokasi)
                ->whereNotNull('hadiah')
                ->where('hadiah', '!=', '')
                ->orderBy('sudah_ditukarkan', 'asc')
                ->orderBy('ditukarkan_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'winners' => $winners->map(function($pemenang) {
                    $ditukarkanAt = null;
                    if ($pemenang->ditukarkan_at) {
                        try {
                            $ditukarkanAt = Carbon::parse($pemenang->ditukarkan_at)->format('d-m-Y H:i:s');
                        } catch (\Exception $e) {
                            $ditukarkanAt = $pemenang->ditukarkan_at;
                        }
                    }

                    $updatedAt = null;
                    if ($pemenang->updated_at) {
                        try {
                            $updatedAt = Carbon::parse($pemenang->updated_at)->format('d-m-Y H:i:s');
                        } catch (\Exception $e) {
                            $updatedAt = $pemenang->updated_at;
                        }
                    }

                    return [
                        'id' => $pemenang->id,
                        'kode_toko' => $pemenang->kode_toko,
                        'nama_toko' => $pemenang->nama_toko,
                        'nama_pic' => $pemenang->nama_pic,
                        'hadiah' => $pemenang->hadiah,
                        'sudah_ditukarkan' => $pemenang->sudah_ditukarkan,
                        'ditukarkan_at' => $ditukarkanAt,
                        'updated_at' => $updatedAt
                    ];
                }),
                'total' => $winners->total(),
                'current_page' => $winners->currentPage(),
                'last_page' => $winners->lastPage(),
                'per_page' => $winners->perPage()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting pemenang kehadiran data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data pemenang'
            ], 500);
        }
    }

    /**
     * Update status penukaran pemenang
     */
    public function updateStatusPenukaran(Request $request, $pemenangId)
    {
        try {
            DB::beginTransaction();

            $pemenang = DoorprizeKehadiranPemenang::findOrFail($pemenangId);

            $pemenang->sudah_ditukarkan = $request->status;

            if ($request->status == 1) {
                $pemenang->ditukarkan_at = now();
            } else {
                $pemenang->ditukarkan_at = null;
            }

            $pemenang->save();

            DB::commit();

            $ditukarkanAtFormatted = null;
            if ($pemenang->ditukarkan_at) {
                try {
                    $ditukarkanAtFormatted = Carbon::parse($pemenang->ditukarkan_at)->format('d-m-Y H:i:s');
                } catch (\Exception $e) {
                    $ditukarkanAtFormatted = $pemenang->ditukarkan_at;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Status penukaran berhasil diupdate',
                'data' => [
                    'id' => $pemenang->id,
                    'sudah_ditukarkan' => $pemenang->sudah_ditukarkan,
                    'ditukarkan_at' => $ditukarkanAtFormatted
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating pemenang status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status penukaran'
            ], 500);
        }
    }
}