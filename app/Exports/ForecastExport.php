<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ForecastExport implements FromCollection, WithHeadings
{
    protected $tanggalAwal;
    protected $tanggalAkhir;

    public function __construct($tanggalAwal, $tanggalAkhir)
    {
       $this->tanggalAwal = $tanggalAwal;
       $this->tanggalAkhir = $tanggalAkhir;
    }

    public function collection()
    {
        $orders = Order::with(['permintaans.merk', 'permintaans.ukuran', 'user'])->whereBetween('tanggal', [$this->tanggalAwal, $this->tanggalAkhir])->get();

        $data = [];

        foreach ($orders as $order) {
            foreach ($order->permintaans as $permintaan) {
                $data[] = [
                    'Kode Order'   => $order->kode,
                    'Tanggal Order'=> \Carbon\Carbon::parse($order->tanggal)->format('d-m-Y'),
                    'Customer'     => $order->user->name ?? '-',
                    'Forecast Period' => $this->getForecastBulan($order->tanggal),
                    'Merk'         => $permintaan->merk->name ?? '-',
                    'Motif'        => $permintaan->motif ?? '-',
                    'Ukuran'       => $permintaan->ukuran->name ?? '-',
                    'Qty' => $permintaan->estimasi ?? 0,
                    'Status' => $order->status == 1 ? 'Confirm' : 'Pending',
                ];
            }
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Kode Order',
            'Tanggal Order',
            'Customer',
            'Forecast Period',
            'Merk',
            'Motif',
            'Ukuran',
            'Qty',
            'Status'
        ];
    }

    private function getForecastBulan($tanggal)
    {
        $tanggal = \Carbon\Carbon::parse($tanggal);
        $year = now()->format('Y');

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
            $start = \Carbon\Carbon::create($tanggal->year, substr($item['start'], 0, 2), substr($item['start'], 3, 2));
            $endMonth = substr($item['end'], 0, 2);
            $endDay = substr($item['end'], 3, 2);
            if ($endMonth == '01' && $start->month == 12) {
                $end = \Carbon\Carbon::create($tanggal->year + 1, $endMonth, $endDay);
            } else {
                $end = \Carbon\Carbon::create($tanggal->year, $endMonth, $endDay);
            }

            if ($tanggal->between($start, $end)) {
                return "{$item['hasil']} {$year}";
            }
        }

        return "N/A {$year}";
    }
}
