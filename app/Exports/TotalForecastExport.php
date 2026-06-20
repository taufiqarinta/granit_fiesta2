<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TotalForecastExport implements FromArray, WithHeadings
{
    protected $bulanDipilih;
    protected $data;
    protected $headers;
    protected $target;

    public function __construct($bulanDipilih, $target = 'ppic')
    {
        $this->bulanDipilih = $bulanDipilih;
        $this->target = $target;
        $this->prepareData();
    }

    private function getForecastBulan($tanggal)
    {
        $tanggal = Carbon::parse($tanggal);
        $year = $tanggal->year;

        $mapping = [
            ['start' => '06-26', 'end' => '07-25', 'hasil' => 'Agustus'],
            ['start' => '07-26', 'end' => '08-25', 'hasil' => 'September'],
            ['start' => '08-26', 'end' => '09-25', 'hasil' => 'Oktober'],
            ['start' => '09-26', 'end' => '10-25', 'hasil' => 'November'],
            ['start' => '10-26', 'end' => '11-25', 'hasil' => 'Desember'],
            ['start' => '11-26', 'end' => '12-25', 'hasil' => 'Januari'],
            ['start' => '12-26', 'end' => '01-25', 'hasil' => 'Februari'],
            ['start' => '01-26', 'end' => '02-25', 'hasil' => 'Maret'],
            ['start' => '02-26', 'end' => '03-25', 'hasil' => 'April'],
            ['start' => '03-26', 'end' => '04-25', 'hasil' => 'Mei'],
            ['start' => '04-26', 'end' => '05-25', 'hasil' => 'Juni'],
            ['start' => '05-26', 'end' => '06-25', 'hasil' => 'Juli'],
        ];

        foreach ($mapping as $item) {
            $startMonth = (int)substr($item['start'], 0, 2);
            $startDay = (int)substr($item['start'], 3, 2);
            $endMonth = (int)substr($item['end'], 0, 2);
            $endDay = (int)substr($item['end'], 3, 2);

            // Deteksi lintas tahun
            if ($startMonth > $endMonth) {
                $start = Carbon::create($year - 1, $startMonth, $startDay); // tahun sebelumnya
                $end = Carbon::create($year, $endMonth, $endDay);
            } else {
                $start = Carbon::create($year, $startMonth, $startDay);
                $end = Carbon::create($year, $endMonth, $endDay);
            }

            if ($tanggal->between($start, $end)) {
                return $item['hasil'];
            }
        }

        return 'N/A';
    }

    private function prepareData()
    {
        $totalPerAgen = [];
        $totalPerUkuran = [];

        // Ambil data dari DB
        $permintaans = DB::table('permintaans')
            ->leftJoin('orders', 'permintaans.order_id', '=', 'orders.id')
            ->leftJoin('merks', 'permintaans.merk_id', '=', 'merks.id')
            ->leftJoin('ukurans', 'permintaans.ukuran_id', '=', 'ukurans.id')
            ->leftJoin('prioritas', 'permintaans.prioritas', '=', 'prioritas.id_prioritas')
            ->select(
                'orders.forecast',
                'permintaans.name',
                'permintaans.motif',
                'permintaans.estimasi',
                'prioritas.id_prioritas',
                'prioritas.nama_prioritas',
                'merks.name as merk',
                'ukurans.name as ukuran'
            )
            ->whereIn('orders.forecast', $this->bulanDipilih)
            ->get();

        $dataMap = [];
        $pivotData = [];
        $bulanHeader = [];

        // Proses data
        foreach ($permintaans as $item) {
            $forecast = $item->forecast;
            $key = $item->merk . '|' . $item->ukuran . '|' . $item->motif . '|' . $item->name;

            // Simpan header bulan
            $bulanHeader[] = $forecast;

            // Tabel utama
            if (!isset($dataMap[$key])) {
                $dataMap[$key] = [
                    'merk' => $item->merk,
                    'ukuran' => $item->ukuran,
                    'motif' => $item->motif,
                    'prioritas' => $item->id_prioritas . ' - ' . $item->nama_prioritas,
                    'name' => $item->name,
                    'bulan' => [],
                ];
            }
            $dataMap[$key]['bulan'][$forecast] = ($dataMap[$key]['bulan'][$forecast] ?? 0) + $item->estimasi;

            // Pivot
            $customer = $item->name;
            $ukuran = $item->ukuran;
            $estimasi = $item->estimasi;

            if (!isset($pivotData[$customer])) {
                $pivotData[$customer] = [];
            }
            if (!isset($pivotData[$customer][$ukuran])) {
                $pivotData[$customer][$ukuran] = [];
            }
            $pivotData[$customer][$ukuran][$forecast] = ($pivotData[$customer][$ukuran][$forecast] ?? 0) + $estimasi;
        }

        $bulanHeader = array_values(array_unique($bulanHeader));
        sort($bulanHeader);

        // ================================
        // 1. TABEL UTAMA
        // ================================
        $this->headers = $this->target === 'sales'
            ? array_merge(['Customer', 'Merk', 'Ukuran', 'Motif'], $bulanHeader, ['Prioritas'])
            : array_merge(['Merk', 'Ukuran', 'Motif'], $bulanHeader, ['Prioritas']);

        $this->data = [];

        foreach ($dataMap as $row) {
            $line = [];

            if ($this->target === 'sales') {
                $line[] = $row['name'];
            }

            $line[] = $row['merk'];
            $line[] = $row['ukuran'];
            $line[] = $row['motif'];

            foreach ($bulanHeader as $b) {
                $line[] = $row['bulan'][$b] ?? 0;
            }

            $line[] = $row['prioritas'];
            $this->data[] = $line;
        }
        

        if ($this->target == 'sales') {
            $this->data[] = [''];

                // Header pivot
                $pivotHeaders = ['Customer', 'Ukuran'];
                foreach ($bulanHeader as $b) {
                    $pivotHeaders[] = 'Sum of ' . $b;
                }

                $this->data[] = $pivotHeaders;
                
                // Tabel pivot + subtotal
                foreach ($pivotData as $customer => $ukurans) {
                    $totalCustomer = array_fill_keys($bulanHeader, 0);

                    foreach ($ukurans as $ukuranData) {
                        foreach ($ukuranData as $b => $val) {
                            $totalCustomer[$b] += $val;
                        }
                    }

                    // Baris subtotal customer
                    $rowCustomer = [$customer, ''];
                    foreach ($bulanHeader as $b) {
                        $rowCustomer[] = $totalCustomer[$b] ?? 0;
                    }
                    $this->data[] = $rowCustomer;

                    // Detail per ukuran
                    foreach ($ukurans as $ukuran => $bulanData) {
                        $line = ['', $ukuran];
                        foreach ($bulanHeader as $b) {
                            $line[] = $bulanData[$b] ?? 0;
                        }
                        $this->data[] = $line;
                    }
                }

                // Baris Grand Total
                $this->data[] = [''];
                $totalPerBulan = array_fill_keys($bulanHeader, 0);
                foreach ($pivotData as $ukurans) {
                    foreach ($ukurans as $bulanData) {
                        foreach ($bulanData as $b => $val) {
                            $totalPerBulan[$b] += $val;
                        }
                    }
                }

                $grandTotalRow = ['Grand Total', ''];
                foreach ($bulanHeader as $b) {
                    $grandTotalRow[] = $totalPerBulan[$b];
                }
                $this->data[] = $grandTotalRow;
        }
        
        
        if ($this->target !== 'sales') {
            $this->data[] = [''];
            $this->data[] = ['Total Order per Ukuran:'];
            $this->data[] = ['Ukuran', 'Total'];

            $totalPerUkuran = [];

            foreach ($permintaans as $item) {
                $ukuran = $item->ukuran;
                $totalPerUkuran[$ukuran] = ($totalPerUkuran[$ukuran] ?? 0) + $item->estimasi;
            }

            foreach ($totalPerUkuran as $ukuran => $total) {
                $this->data[] = [$ukuran, $total];
        }
    }

}




    // private function prepareData()
    // {
    //     // Filter data kalau isinya ga lengkap ga ditampilkan
    //     // $permintaans = DB::table('permintaans')
    //     //     ->join('orders', 'permintaans.order_id', '=', 'orders.id')
    //     //     ->join('merks', 'permintaans.merk_id', '=', 'merks.id')
    //     //     ->join('ukurans', 'permintaans.ukuran_id', '=', 'ukurans.id')
    //     //     ->join('prioritas', 'permintaans.prioritas', '=', 'prioritas.id_prioritas') // <-- tambahkan ini
    //     //     ->select(
    //     //         'orders.forecast',
    //     //         'permintaans.name',
    //     //         'permintaans.motif',
    //     //         'permintaans.estimasi',
    //     //         'prioritas.id_prioritas',
    //     //         'prioritas.nama_prioritas', // <-- ambil nama prioritas
    //     //         'merks.name as merk',
    //     //         'ukurans.name as ukuran'
    //     //     )
    //     //     ->whereIn('orders.forecast', $this->bulanDipilih)
    //     //     ->get();

    //     $totalPerAgen = [];
    //     $totalPerUkuran = [];


    //     // Filter data kalau isinya ga lengkap tetap ditampilkan
    //     $permintaans = DB::table('permintaans')
    //         ->leftJoin('orders', 'permintaans.order_id', '=', 'orders.id')
    //         ->leftJoin('merks', 'permintaans.merk_id', '=', 'merks.id')
    //         ->leftJoin('ukurans', 'permintaans.ukuran_id', '=', 'ukurans.id')
    //         ->leftJoin('prioritas', 'permintaans.prioritas', '=', 'prioritas.id_prioritas')
    //         ->select(
    //             'orders.forecast',
    //             'permintaans.name',
    //             'permintaans.motif',
    //             'permintaans.estimasi',
    //             'prioritas.id_prioritas',
    //             'prioritas.nama_prioritas',
    //             'merks.name as merk',
    //             'ukurans.name as ukuran'
    //         )
    //         ->whereIn('orders.forecast', $this->bulanDipilih)
    //         ->get();


    //     $dataMap = [];
    //     $bulanHeader = [];

    //     foreach ($permintaans as $item) {
    //         $forecast = $item->forecast;
    //         $key = $item->merk . '|' . $item->ukuran . '|' . $item->motif . '|' . $item->name;
    //         // Total per agen (customer)
    //         if ($this->target == 'sales') {
    //             $totalPerAgen[$item->name] = ($totalPerAgen[$item->name] ?? 0) + $item->estimasi;
    //         }

    //         // Total per ukuran (untuk semua laporan)
    //         $totalPerUkuran[$item->ukuran] = ($totalPerUkuran[$item->ukuran] ?? 0) + $item->estimasi;


    //         if (!isset($dataMap[$key])) {
    //             $dataMap[$key] = [
    //                 'merk' => $item->merk,
    //                 'ukuran' => $item->ukuran,
    //                 'motif' => $item->motif,
    //                 'prioritas' => $item->id_prioritas . ' - ' . $item->nama_prioritas,
    //                 'name' => $item->name,
    //                 'bulan' => [],
    //             ];
    //         }

    //         $dataMap[$key]['bulan'][$forecast] = ($dataMap[$key]['bulan'][$forecast] ?? 0) + $item->estimasi;
    //         $bulanHeader[] = $forecast;
    //     }

    //     $bulanHeader = array_values(array_unique($bulanHeader));
    //     sort($bulanHeader);

    //     // Header tergantung target
    //     if ($this->target == 'sales') {
    //         $this->headers = array_merge(['Customer', 'Merk', 'Ukuran', 'Motif'], $bulanHeader, ['Prioritas']);
    //     } else {
    //         $this->headers = array_merge(['Merk', 'Ukuran', 'Motif'], $bulanHeader, ['Prioritas']);
    //     }

    //     $this->data = [];

    //     foreach ($dataMap as $row) {
    //         $line = [];

    //         if ($this->target == 'sales') {
    //             $line[] = $row['name'];
    //         }

    //         $line[] = $row['merk'];
    //         $line[] = $row['ukuran'];
    //         $line[] = $row['motif'];
            
    //         foreach ($bulanHeader as $b) {
    //             $line[] = $row['bulan'][$b] ?? 0;
    //         }
    //         $line[] = $row['prioritas'];

    //         $this->data[] = $line;
    //     }

    //     if ($this->target == 'sales') {
    //         $this->data[] = [''];
    //         $this->data[] = ['Total Order per Agen:'];
    //         $this->data[] = ['Customer', 'Total'];
    //         foreach ($totalPerAgen as $nama => $total) {
    //             $this->data[] = [$nama, $total];
    //         }

    //         $this->data[] = [''];
    //         $this->data[] = ['Total Order per Ukuran:'];
    //         $this->data[] = ['Ukuran', 'Total'];
    //         foreach ($totalPerUkuran as $ukuran => $total) {
    //             $this->data[] = [$ukuran, $total];
    //         }
    //     }

    //     if ($this->target != 'sales') {
    //         $this->data[] = [''];
    //         $this->data[] = ['Total Order per Ukuran:'];
    //         $this->data[] = ['Ukuran', 'Total'];
    //         foreach ($totalPerUkuran as $ukuran => $total) {
    //             $this->data[] = [$ukuran, $total];
    //         }
    //     }
    // }


    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headers;
    }
}
