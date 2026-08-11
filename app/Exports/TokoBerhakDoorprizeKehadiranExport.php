<?php

namespace App\Exports;

use App\Models\DaftarToko;
use App\Models\DoorprizeKehadiran;
use App\Models\DoorprizeKehadiranPemenang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TokoBerhakDoorprizeKehadiranExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    protected $lokasi;

    protected $doorprizeId;

    protected $rowNumber = 0;

    public function __construct($lokasi, $doorprizeId = null)
    {
        $this->lokasi = strtoupper($lokasi);
        $this->doorprizeId = $doorprizeId;
    }

    public function collection()
    {
        $batasJam = $this->getBatasJam();

        $query = DaftarToko::select('kode_toko')
            ->selectRaw('MAX(id) as max_id')
            ->where('lokasi_event', $this->lokasi)
            ->where('status', 1)
            ->where('hadir', 1)
            ->whereNotNull('waktu_kehadiran')
            ->where('waktu_kehadiran', '<=', $batasJam)
            ->where(function ($query) {
                $query->whereNull('nama_agen')
                    ->orWhereRaw('LOWER(TRIM(nama_toko)) != LOWER(TRIM(nama_agen))');
            })
            ->groupBy('kode_toko')
            ->orderByDesc('max_id');

        $rows = $query->get();

        $sudahMenang = DoorprizeKehadiranPemenang::where('lokasi_event', $this->lokasi)
            ->pluck('hadiah', 'kode_toko');

        return $rows->map(function ($row) use ($sudahMenang) {
            $toko = DaftarToko::find($row->max_id);

            if (! $toko) {
                return null;
            }

            return [
                'kode_toko' => $toko->kode_toko,
                'nama_toko' => $toko->nama_toko,
                'nama_pic' => $toko->pic,
                'kota' => $toko->kota,
                'waktu_kehadiran' => $toko->waktu_kehadiran,
                'hadiah' => $sudahMenang->get($toko->kode_toko, null),
                'sudah_menang' => $sudahMenang->has($toko->kode_toko),
            ];
        })->filter()->values();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Toko',
            'Nama Toko',
            'Nama PIC',
            'Kota',
            'Waktu Hadir',
            'Hadiah',
            'Status',
        ];
    }

    public function map($toko): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $toko['kode_toko'],
            $toko['nama_toko'],
            $toko['nama_pic'],
            $toko['kota'],
            $toko['waktu_kehadiran'],
            $toko['hadiah'] ?: '-',
            $toko['sudah_menang'] ? 'Sudah Menang' : 'Berhak',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DC143C'],
                ],
            ],
            'A:H' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
            ],
            'A' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 30,
            'D' => 20,
            'E' => 20,
            'F' => 15,
            'G' => 25,
            'H' => 15,
        ];
    }

    private function getBatasJam()
    {
        if (! $this->doorprizeId) {
            return '18:00:00';
        }

        $doorprize = DoorprizeKehadiran::find($this->doorprizeId);

        return $doorprize ? ($doorprize->batas_jam_kehadiran ?: '18:00:00') : '18:00:00';
    }
}
