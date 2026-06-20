<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Ambil semua lokasi dari master
            $masterLokasi = DB::table('master_lokasi_event')
                ->select('nama_lokasi', 'tanggal', 'status')
                ->get();

            // Ambil tanggal hari ini
            $today = Carbon::today()->format('Y-m-d');

            // 1. Jumlah Toko berdasarkan lokasi_event
            $jumlahTokoRaw = DB::table('daftar_toko')
                ->select('lokasi_event', DB::raw('COUNT(*) as total'))
                ->whereNotNull('lokasi_event')
                ->where('lokasi_event', '!=', '')
                ->where('status', 1)
                ->groupBy('lokasi_event')
                ->pluck('total', 'lokasi_event');

            // 2. Jumlah Agen berdasarkan lokasi_event
            $jumlahAgenRaw = DB::table('daftar_agen')
                ->select('lokasi_event', DB::raw('COUNT(*) as total'))
                ->whereNotNull('lokasi_event')
                ->where('status', 1)
                ->where('lokasi_event', '!=', '')
                ->groupBy('lokasi_event')
                ->pluck('total', 'lokasi_event');

            // 3. Jumlah Kehadiran Toko
            $kehadiranTokoRaw = DB::table('daftar_toko')
                ->select('lokasi_event', DB::raw('COUNT(*) as total'))
                ->where('hadir', 1)
                ->where('status', 1)
                ->whereNotNull('lokasi_event')
                ->where('lokasi_event', '!=', '')
                ->groupBy('lokasi_event')
                ->pluck('total', 'lokasi_event');

            // 4. Jumlah Kehadiran Agen
            $kehadiranAgenRaw = DB::table('daftar_agen')
                ->select('lokasi_event', DB::raw('COUNT(*) as total'))
                ->where('hadir', 1)
                ->where('status', 1)
                ->whereNotNull('lokasi_event')
                ->where('lokasi_event', '!=', '')
                ->groupBy('lokasi_event')
                ->pluck('total', 'lokasi_event');

            // 5. Jumlah Order berdasarkan lokasi_event
            $jumlahOrderRaw = DB::table('form_orders')
                ->select('lokasi_event', DB::raw('COUNT(*) as total'))
                ->whereNotNull('lokasi_event')
                ->where('lokasi_event', '!=', '')
                ->groupBy('lokasi_event')
                ->pluck('total', 'lokasi_event');

            // Map data ke master lokasi
            $jumlahToko = [];
            $jumlahAgen = [];
            $jumlahAgenToko = [];
            $jumlahKehadiran = [];
            $jumlahOrder = [];
            $jumlahBelumOrder = [];

            foreach ($masterLokasi as $lokasi) {
                $namaLokasi = $lokasi->nama_lokasi;
                $isToday = $lokasi->tanggal === $today;

                // Jumlah Toko
                $totalToko = $jumlahTokoRaw[$namaLokasi] ?? 0;
                $jumlahToko[] = [
                    'lokasi_event' => $namaLokasi,
                    'total' => $totalToko,
                    'is_today' => $isToday
                ];

                // Jumlah Agen
                $totalAgen = $jumlahAgenRaw[$namaLokasi] ?? 0;
                $jumlahAgen[] = [
                    'lokasi_event' => $namaLokasi,
                    'total' => $totalAgen,
                    'is_today' => $isToday
                ];

                // Jumlah Agen + Toko
                $jumlahAgenToko[] = [
                    'lokasi_event' => $namaLokasi,
                    'total' => $totalToko + $totalAgen,
                    'is_today' => $isToday
                ];

                // Jumlah Kehadiran
                $totalKehadiranToko = $kehadiranTokoRaw[$namaLokasi] ?? 0;
                $totalKehadiranAgen = $kehadiranAgenRaw[$namaLokasi] ?? 0;
                $totalKehadiran = $totalKehadiranToko + $totalKehadiranAgen;
                $jumlahKehadiran[] = [
                    'lokasi_event' => $namaLokasi,
                    'total' => $totalKehadiran,
                    'is_today' => $isToday
                ];

                // Jumlah Order
                $totalOrder = $jumlahOrderRaw[$namaLokasi] ?? 0;
                $jumlahOrder[] = [
                    'lokasi_event' => $namaLokasi,
                    'total' => $totalOrder,
                    'is_today' => $isToday
                ];

                // Jumlah Belum Order
                $jumlahBelumOrder[] = [
                    'lokasi_event' => $namaLokasi,
                    'total' => max(0, $totalKehadiran - $totalOrder),
                    'is_today' => $isToday
                ];
            }

            // Total keseluruhan
            $totalToko = collect($jumlahToko)->sum('total');
            $totalAgen = collect($jumlahAgen)->sum('total');
            $totalAgenToko = collect($jumlahAgenToko)->sum('total');
            $totalKehadiran = collect($jumlahKehadiran)->sum('total');
            $totalOrder = collect($jumlahOrder)->sum('total');
            $totalBelumOrder = collect($jumlahBelumOrder)->sum('total');

            return view('dashboard.index', compact(
                'jumlahToko',
                'jumlahAgen',
                'jumlahAgenToko',
                'jumlahKehadiran',
                'jumlahOrder',
                'jumlahBelumOrder',
                'totalToko',
                'totalAgen',
                'totalAgenToko',
                'totalKehadiran',
                'totalOrder',
                'totalBelumOrder'
            ));

        } catch (\Exception $e) {
            return view('dashboard.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}