<?php

namespace App\Exports;

use App\Models\DaftarToko;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DaftarTokoExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $search;
    protected $lokasiEvent;

    public function __construct($search = null, $lokasiEvent = null)
    {
        $this->search = $search;
        $this->lokasiEvent = $lokasiEvent;
    }

    // public function collection()
    // {
    //     // Gunakan subquery dengan groupBy yang sama seperti di controller
    //     $sub = DB::table('daftar_toko')
    //         ->select('lokasi_event', 'nama_toko', 'pic', 'nomor_pic', 'kota')
    //         ->whereNotNull('lokasi_event')
    //         ->where('lokasi_event', '!=', '')
    //         ->where('status', 1);

    //     // Apply filters to subquery jika ada
    //     if ($this->search) {
    //         $sub->where(function($query) {
    //             $query->where('nama_toko', 'like', "%{$this->search}%")
    //                   ->orWhere('kode_toko', 'like', "%{$this->search}%")
    //                   ->orWhere('pic', 'like', "%{$this->search}%")
    //                   ->orWhere('kota', 'like', "%{$this->search}%")
    //                   ->orWhere('lokasi_event', 'like', "%{$this->search}%");
    //         });
    //     }

    //     if ($this->lokasiEvent && $this->lokasiEvent !== 'semua') {
    //         $sub->where('lokasi_event', $this->lokasiEvent);
    //     }

    //     $sub->groupBy('lokasi_event', 'nama_toko', 'pic', 'nomor_pic', 'kota');

    //     // Query utama
    //     $query = DB::table('daftar_toko as dt')
    //         ->select(
    //             'dt.kode_toko',
    //             'dt.nama_toko', 
    //             'dt.alamat',
    //             'dt.kota',
    //             'dt.provinsi',
    //             'dt.pic',
    //             'dt.nomor_pic',
    //             'dt.lokasi_event'
    //         )
    //         ->joinSub($sub, 'grouped_toko', function($join) {
    //             $join->on('dt.nama_toko', '=', 'grouped_toko.nama_toko')
    //                  ->on('dt.pic', '=', 'grouped_toko.pic')
    //                  ->on('dt.nomor_pic', '=', 'grouped_toko.nomor_pic')
    //                  ->on('dt.kota', '=', 'grouped_toko.kota')
    //                  ->on('dt.lokasi_event', '=', 'grouped_toko.lokasi_event');
    //         })
    //         ->where('dt.status', 1)
    //         ->whereNotNull('dt.lokasi_event')
    //         ->where('dt.lokasi_event', '!=', '');

    //     // Apply filters to main query juga untuk konsistensi
    //     if ($this->search) {
    //         $query->where(function($q) {
    //             $q->where('dt.nama_toko', 'like', "%{$this->search}%")
    //               ->orWhere('dt.kode_toko', 'like', "%{$this->search}%")
    //               ->orWhere('dt.pic', 'like', "%{$this->search}%")
    //               ->orWhere('dt.kota', 'like', "%{$this->search}%")
    //               ->orWhere('dt.lokasi_event', 'like', "%{$this->search}%");
    //         });
    //     }

    //     if ($this->lokasiEvent && $this->lokasiEvent !== 'semua') {
    //         $query->where('dt.lokasi_event', $this->lokasiEvent);
    //     }

    //     return $query->orderBy('dt.lokasi_event', 'desc')
    //                 ->orderBy('dt.nama_toko', 'asc')
    //                 ->get();
    // }

    public function collection()
    {
        // Ambil semua data dengan filter
        $query = DaftarToko::where('status', 1)
            ->whereNotNull('lokasi_event')
            ->where('lokasi_event', '!=', '')
            ->where('kode_agen', '!=', 'C0188')
            ->where('kode_agen', '!=', 'C0189');

        // Apply filters
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_toko', 'like', "%{$this->search}%")
                ->orWhere('kode_toko', 'like', "%{$this->search}%")
                ->orWhere('pic', 'like', "%{$this->search}%")
                ->orWhere('kota', 'like', "%{$this->search}%")
                ->orWhere('lokasi_event', 'like', "%{$this->search}%");
            });
        }

        if ($this->lokasiEvent && $this->lokasiEvent !== 'semua') {
            $query->where('lokasi_event', $this->lokasiEvent);
        }

        // Ambil data dan group secara manual
        return $query->orderBy('lokasi_event', 'desc')
                    ->orderBy('nama_toko', 'asc')
                    ->get()
                    ->groupBy(function($item) {
                        return $item->nama_toko . '|' . $item->pic . '|' . $item->nomor_pic . '|' . $item->kota . '|' . $item->lokasi_event;
                    })
                    ->map(function($group) {
                        // Ambil record pertama dari setiap group
                        return $group->first();
                    })
                    ->values();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Toko',
            'Nama Toko',
            'Alamat',
            'Kota',
            'Provinsi',
            'Nama PIC',
            'Nomor HP',
            'Lokasi Event'
        ];
    }

    public function map($toko): array
    {
        static $counter = 0;
        $counter++;
        
        return [
            $counter,
            $toko->kode_toko,
            $toko->nama_toko,
            $toko->alamat,
            $toko->kota,
            $toko->provinsi,
            $toko->pic,
            $toko->nomor_pic,
            $toko->lokasi_event
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            
            // Set auto size for all columns
            'A:K' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
            ],
        ];
    }
}