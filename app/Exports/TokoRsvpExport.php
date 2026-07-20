<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TokoRsvpExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    protected $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Lokasi Event', 'Kode Toko', 'Nama Toko', 'PIC', 'Nomor PIC',
            'Kota', 'Kode Agen', 'Nama Agen', 'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->lokasi_event,
            $row->kode_toko,
            $row->nama_toko,
            $row->pic,
            $row->nomor_pic,
            $row->kota,
            $row->kode_agen,
            $row->nama_agen,
            (int) $row->konfirmasi_kehadiran === 1 ? 'Sudah RSVP' : 'Belum RSVP',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType'   => Fill::FILL_GRADIENT_LINEAR,
                        'rotation'   => 90,
                        'startColor' => ['rgb' => '950000'],
                        'endColor'   => ['rgb' => 'FA0000'],
                    ],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);
            },
        ];
    }
}