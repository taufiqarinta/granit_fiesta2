<?php

namespace App\Exports;

use App\Models\FormOrder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeringkatExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $lokasiEvent;
    protected $search;

    public function __construct($lokasiEvent = null, $search = null)
    {
        $this->lokasiEvent = $lokasiEvent;
        $this->search = $search;
    }

    public function collection()
    {
        $query = FormOrder::select(
            'nama_toko',
            'no_hp',
            'pic',
            'kota',
            DB::raw('SUM(total_point) as total_point_accumulated')
        )
        ->groupBy('nama_toko', 'no_hp', 'pic', 'kota');

        if ($this->lokasiEvent) {
            $query->where('lokasi_event', $this->lokasiEvent);
        }

        if ($this->search) {
            $searchTerm = $this->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama_toko', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('pic', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('kota', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('no_hp', 'LIKE', "%{$searchTerm}%");
            });
        }

        return $query->orderByDesc('total_point_accumulated')->get();
    }

    public function headings(): array
    {
        return [
            'Peringkat',
            'Total Point',
            'Nama Toko',
            'PIC',
            'No HP',
            'Kota Toko',
        ];
    }

    public function map($peringkat): array
    {
        static $index = 0;
        $index++;
        
        return [
            $index,
            $peringkat->total_point_accumulated,
            $peringkat->nama_toko,
            $peringkat->pic,
            $peringkat->no_hp,
            $peringkat->kota,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']]
            ],
            
            // // Style untuk peringkat 1-5
            // 'A2:A6' => [
            //     'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            //     'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DC2626']] // Merah terang
            // ],
        ];
    }
}