<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PemenangController extends Controller
{
    /**
     * Menampilkan halaman list pemenang
     */
    public function showPemenangPage($lokasi)
    {
        return view('pemenang.list', [
            'lokasi' => $lokasi
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
            
            $winners = Voucher::where('status', 1)
                ->where('lokasi_event', $lokasi)
                ->whereNotNull('hadiah')
                ->where('hadiah', '!=', '')
                ->orderBy('sudah_ditukarkan', 'asc') // Yang belum ditukarkan di atas
                ->orderBy('ditukarkan_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'winners' => $winners->map(function($voucher) {
                    // Format tanggal dengan aman
                    $ditukarkanAt = null;
                    if ($voucher->ditukarkan_at) {
                        try {
                            // Coba parse sebagai Carbon object
                            $ditukarkanAt = Carbon::parse($voucher->ditukarkan_at)->format('d-m-Y H:i:s');
                        } catch (\Exception $e) {
                            // Jika gagal, gunakan value asli
                            $ditukarkanAt = $voucher->ditukarkan_at;
                        }
                    }

                    $updatedAt = null;
                    if ($voucher->updated_at) {
                        try {
                            $updatedAt = Carbon::parse($voucher->updated_at)->format('d-m-Y H:i:s');
                        } catch (\Exception $e) {
                            $updatedAt = $voucher->updated_at;
                        }
                    }

                    return [
                        'id' => $voucher->id,
                        'nama_toko' => $voucher->nama_toko,
                        'nama_pic' => $voucher->nama_pic,
                        'nomor_voucher' => $voucher->nomor_voucher,
                        'hadiah' => $voucher->hadiah,
                        'sudah_ditukarkan' => $voucher->sudah_ditukarkan,
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
            \Log::error('Error getting winners data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data pemenang'
            ], 500);
        }
    }

    /**
     * Update status penukaran voucher
     */
    public function updateStatusPenukaran(Request $request, $voucherId)
    {
        try {
            DB::beginTransaction();

            $voucher = Voucher::findOrFail($voucherId);
            
            $voucher->sudah_ditukarkan = $request->status;
            
            if ($request->status == 1) {
                // Jika status menjadi 1 (sudah ditukarkan), set timestamp sekarang
                $voucher->ditukarkan_at = now();
            } else {
                // Jika status menjadi 0 (belum ditukarkan), set menjadi null
                $voucher->ditukarkan_at = null;
            }
            
            $voucher->save();

            DB::commit();

            // Format response dengan aman
            $ditukarkanAtFormatted = null;
            if ($voucher->ditukarkan_at) {
                try {
                    $ditukarkanAtFormatted = Carbon::parse($voucher->ditukarkan_at)->format('d-m-Y H:i:s');
                } catch (\Exception $e) {
                    $ditukarkanAtFormatted = $voucher->ditukarkan_at;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Status penukaran berhasil diupdate',
                'data' => [
                    'id' => $voucher->id,
                    'sudah_ditukarkan' => $voucher->sudah_ditukarkan,
                    'ditukarkan_at' => $ditukarkanAtFormatted
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating voucher status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status penukaran'
            ], 500);
        }
    }
}