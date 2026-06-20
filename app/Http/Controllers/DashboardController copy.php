<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // 1. Jumlah Toko berdasarkan lokasi_event
            $jumlahToko = DB::table('daftar_toko')
                ->select('lokasi_event', DB::raw('COUNT(*) as total'))
                ->whereNotNull('lokasi_event')
                ->where('lokasi_event', '!=', '')
                ->groupBy('lokasi_event')
                ->get();

            // 2. Jumlah Agen berdasarkan lokasi_event
            $jumlahAgen = DB::table('daftar_agen')
                ->select('lokasi_event', DB::raw('COUNT(*) as total'))
                ->whereNotNull('lokasi_event')
                ->where('lokasi_event', '!=', '')
                ->groupBy('lokasi_event')
                ->get();

            // 3. Jumlah Agen + Toko per lokasi_event
            $jumlahAgenToko = $this->combineAgenToko($jumlahToko, $jumlahAgen);

            // 4. Jumlah Kehadiran (toko + agen) berdasarkan lokasi_event
            $kehadiranToko = DB::table('daftar_toko')
                ->select('lokasi_event', DB::raw('COUNT(*) as total'))
                ->where('hadir', 1)
                ->whereNotNull('lokasi_event')
                ->where('lokasi_event', '!=', '')
                ->groupBy('lokasi_event')
                ->get();

            $kehadiranAgen = DB::table('daftar_agen')
                ->select('lokasi_event', DB::raw('COUNT(*) as total'))
                ->where('hadir', 1)
                ->whereNotNull('lokasi_event')
                ->where('lokasi_event', '!=', '')
                ->groupBy('lokasi_event')
                ->get();

            // Gabungkan kehadiran toko dan agen
            $jumlahKehadiran = $this->combineData($kehadiranToko, $kehadiranAgen);

            // 5. Jumlah Order berdasarkan lokasi_event
            $jumlahOrder = DB::table('form_orders')
                ->select('lokasi_event', DB::raw('COUNT(*) as total'))
                ->whereNotNull('lokasi_event')
                ->where('lokasi_event', '!=', '')
                ->groupBy('lokasi_event')
                ->get();

            // 6. Jumlah Belum Order (Kehadiran - Order)
            $jumlahBelumOrder = $this->calculateBelumOrder($jumlahKehadiran, $jumlahOrder);

            // Total keseluruhan
            $totalToko = $jumlahToko->sum('total');
            $totalAgen = $jumlahAgen->sum('total');
            $totalAgenToko = $totalToko + $totalAgen;
            $totalKehadiran = collect($jumlahKehadiran)->sum('total');
            $totalOrder = $jumlahOrder->sum('total');
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

    private function combineAgenToko($toko, $agen)
    {
        $combined = [];

        // Gabungkan semua lokasi event dari kedua data
        $allLocations = collect()
            ->merge($toko->pluck('lokasi_event'))
            ->merge($agen->pluck('lokasi_event'))
            ->unique()
            ->filter()
            ->values();

        foreach ($allLocations as $location) {
            $totalToko = $toko->where('lokasi_event', $location)->first()->total ?? 0;
            $totalAgen = $agen->where('lokasi_event', $location)->first()->total ?? 0;
            
            $combined[] = [
                'lokasi_event' => $location,
                'total' => $totalToko + $totalAgen
            ];
        }

        return $combined;
    }

    private function combineData($data1, $data2)
    {
        $combined = [];

        // Gabungkan semua lokasi event dari kedua data
        $allLocations = collect()
            ->merge($data1->pluck('lokasi_event'))
            ->merge($data2->pluck('lokasi_event'))
            ->unique()
            ->filter()
            ->values();

        foreach ($allLocations as $location) {
            $total1 = $data1->where('lokasi_event', $location)->first()->total ?? 0;
            $total2 = $data2->where('lokasi_event', $location)->first()->total ?? 0;
            
            $combined[] = [
                'lokasi_event' => $location,
                'total' => $total1 + $total2
            ];
        }

        return $combined;
    }

    private function calculateBelumOrder($kehadiran, $order)
    {
        $belumOrder = [];

        // Gabungkan semua lokasi event dari kehadiran dan order
        $allLocations = collect()
            ->merge(collect($kehadiran)->pluck('lokasi_event'))
            ->merge($order->pluck('lokasi_event'))
            ->unique()
            ->filter()
            ->values();

        foreach ($allLocations as $location) {
            // Cari total kehadiran untuk lokasi ini
            $kehadiranItem = collect($kehadiran)->firstWhere('lokasi_event', $location);
            $totalKehadiran = $kehadiranItem ? $kehadiranItem['total'] : 0;
            
            // Cari total order untuk lokasi ini
            $orderItem = $order->firstWhere('lokasi_event', $location);
            $totalOrder = $orderItem ? $orderItem->total : 0;
            
            $belumOrder[] = [
                'lokasi_event' => $location,
                'total' => max(0, $totalKehadiran - $totalOrder)
            ];
        }

        return $belumOrder;
    }
}