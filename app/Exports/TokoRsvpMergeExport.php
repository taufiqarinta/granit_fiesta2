<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TokoRsvpMergeExport implements FromView, WithEvents
{
    protected $groups;

    public function __construct($groups)
    {
        $this->groups = $groups;
    }

    public function view(): View
    {
        return view('toko-rsvp.toko-rsvp-merge', [
            'groups' => $this->groups,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'I';

                $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType'   => Fill::FILL_GRADIENT_LINEAR,
                        'rotation'   => 90,
                        'startColor' => ['rgb' => '950000'],
                        'endColor'   => ['rgb' => 'FA0000'],
                    ],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                foreach (range('A', $lastColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->getStyle('A1:' . $lastColumn . $sheet->getHighestRow())
                    ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            },
        ];
    }
}