<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

use App\Models\FormLkp;

class KomplainExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $tanggalAwal;
    protected $tanggalAkhir;
    protected $user;

    public function __construct($tanggalAwal, $tanggalAkhir, $user)
    {
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->user = $user;
    }

    public function collection()
    {
        $query = FormLkp::with(['merks', 'ukurans', 'motifs'])
        ->when($this->tanggalAwal && $this->tanggalAkhir, function ($q) {
            $q->whereBetween('created_date', [$this->tanggalAwal, $this->tanggalAkhir]);
        })
        ->orderBy('id', 'desc');


        // Filter created_by jika bukan QMS, DIR, SLS, GM
        // if (!in_array($this->user->id_customer, ['QMS', 'DIR', 'SLS', 'GM'])) {
        //     $query->where('created_by', $this->user->id_customer);
        // }

        $currentRole = auth()->user()->role_as;

        if ($currentRole == 0) {
            $query->where('created_by', $this->user->id_customer);
        }

        return $query->get()->map(function ($item, $index) {
            // Transformasi nama relasi dan wilayah
            $provinsi = DB::table('wilayah')
                ->whereRaw('CHAR_LENGTH(kode) = 2')
                ->where('kode', substr($item->provinsi, 0, 2))
                ->value('nama') ?? $item->provinsi;

            $kabupaten = DB::table('wilayah')
                ->whereRaw('CHAR_LENGTH(kode) = 5')
                ->where('kode', substr($item->kabupaten, 0, 5))
                ->value('nama') ?? $item->kabupaten;

            $flag = (int) $item->flag;

            $status = match (true) {
                $item->status == "-1" => 'Not Valid',
                $item->status == "-2" => 'Not Approved',
                in_array($item->status, [0, 1]) => match ($flag) {
                    0 => 'Submitted',
                    1 => 'Updated by QMS',
                    2 => 'Valid',
                    3 => 'Updated by SLS',
                    4 => 'Approved',
                    default => 'Unknown'
                },
                default => 'Unknown'
            };


            return [
                'No' => $index + 1,
                'Nomor' => $item->nomor,
                'Tanggal' => $item->tanggal,
                'Nama' => $item->nama,
                'Email' => $item->email,
                'Provinsi' => $provinsi,
                'Kabupaten' => $kabupaten,
                'Via Agen' => $item->via_agen,
                'No SJ' => $item->no_sj,
                'Tgl Pembelian' => $item->tanggal_pembelian,
                'Tgl Komplain' => $item->tanggal_komplain,
                'Sales' => $item->sales,
                'Merk' => $item->merks->name ?? '-',
                'Ukuran' => $item->ukurans->name ?? '-',
                'Motif' => $item->motifs->name ?? '-',
                'KW' => $item->kw,
                'Tonality' => $item->tonality,
                'Kaliber' => $item->kaliber,
                'Batch' => $item->batch,
                'Jumlah Order' => $item->jumlah_order,
                'Jumlah Kirim' => $item->jumlah_kirim,
                'Jumlah Komplain' => $item->jumlah_komplain,
                'Jenis Komplain' => $item->jenis_komplain,
                'Penyelesaian' => $item->penyelesaian,
                'Analisa' => $item->analisa,
                'Keputusan' => $item->keputusan,
                'Dibuat Oleh' => $item->created_by,
                'Tgl Dibuat' => $item->created_date,
                'Waktu Dibuat' => $item->created_time,
                'Status' => $status,
            ];
        });

    }

    public function headings(): array
    {
        return [
            'No', 'Nomor', 'Tanggal', 'Nama', 'Email', 'Provinsi', 'Kabupaten', 'Via Agen',
            'No SJ', 'Tgl Pembelian', 'Tgl Komplain', 'Sales', 'Merk', 'Ukuran',
            'Motif', 'KW', 'Tonality', 'Kaliber', 'Batch', 'Jumlah Order',
            'Jumlah Kirim', 'Jumlah Komplain', 'Jenis Komplain', 'Penyelesaian',
            'Analisa', 'Keputusan', 'Dibuat Oleh', 'Tgl Dibuat', 'Waktu Dibuat',
            'Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Bold untuk baris pertama (header)
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Data Komplain';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 12,
            'D' => 20,
            'E' => 25,
            'F' => 25,
            'G' => 25,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 20,
            'M' => 20,
            'N' => 20,
            'O' => 20,
            'P' => 20,
            'Q' => 20,
            'R' => 20,
            'S' => 20,
            'T' => 20,
            'U' => 20,
            'V' => 20,
            'W' => 20,
            'X' => 20,
            'Y' => 20,
            'Z' => 20,
            'AA' => 20,
            'AB' => 20,
            'AC' => 20,
            'AD' => 20,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:AD1')->getFill()->setFillType('solid')
                    ->getStartColor()->setARGB('FFDAECFF');
                $event->sheet->getStyle('A1:AD1')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('A1:AD1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
        ];
    }
}



