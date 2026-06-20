<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class MonitoringExport implements FromView, WithStyles, WithEvents
{
    protected $projects, $subprojects, $users, $groupedActivities, $bulan, $tahun;

    public function __construct($projects, $subprojects, $users, $groupedActivities, $bulan, $tahun)
    {
        $this->projects = $projects;
        $this->subprojects = $subprojects;
        $this->users = $users;
        $this->groupedActivities = $groupedActivities;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1E8449']]],
            2 => ['fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF27AE60']]],
            3 => ['fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF2ECC71']]],
            4 => ['fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFD5F5E3']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Mendapatkan jumlah baris dan kolom
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Range seluruh tabel
                $range = 'A1:' . $highestColumn . $highestRow;

                // Tambahkan border untuk seluruh sel yang digunakan
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
            },
        ];
    }

    public function view(): View
    {
        return view('admin.monokotil.export', [
            'projects' => $this->projects,
            'subprojects' => $this->subprojects,
            'users' => $this->users,
            'groupedActivities' => $this->groupedActivities,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
        ]);
    }
}
