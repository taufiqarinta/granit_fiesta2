<?php

namespace App\Exports;

use App\Models\DaftarToko;
use App\Models\DaftarAgen;
use App\Models\Wilayah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KehadiranExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $lokasiEvent;
    protected $rowNumber = 0;

    public function __construct($lokasiEvent)
    {
        $this->lokasiEvent = $lokasiEvent;
    }

    public function collection()
    {
        // Ambil data dari kedua tabel dengan urutan ID descending
        $daftarTokos = DaftarToko::where('lokasi_event', $this->lokasiEvent)
                        ->where('status', 1)
                        ->orderBy('waktu_kehadiran', 'desc')
                        ->get();

        $daftarAgens = DaftarAgen::where('lokasi_event', $this->lokasiEvent)
                        ->where('status', 1)
                        ->orderBy('waktu_kehadiran', 'desc')
                        ->get();
        
        $gabunganData = collect();
        $groupedData = [];
        
        // Proses data daftar toko
        foreach ($daftarTokos as $item) {
            $uniqueKey = strtolower(trim($item->nama_toko)) . '|' . 
                        strtolower(trim($item->pic)) . '|' . 
                        strtolower(trim($item->nomor_pic)) . '|' . 
                        strtolower(trim($item->kota)) . '|';
            
            if (!isset($groupedData[$uniqueKey])) {
                $provinsi = Wilayah::where('kode', $item->provinsi)->first();
                $kota = Wilayah::where('kode', $item->kota)->first();
                
                $groupedData[$uniqueKey] = (object)[
                    'type' => 'Toko',
                    'kode_toko' => $item->kode_toko,
                    'nama_toko' => $item->nama_toko,
                    'pic' => $item->pic,
                    'nomor_pic' => $item->nomor_pic,
                    'alamat' => $item->alamat,
                    'provinsi' => $provinsi->nama ?? $item->provinsi,
                    'kota' => $kota->nama ?? $item->kota,
                    'kode_agen' => $item->kode_agen,
                    'nama_agen' => $item->nama_agen,
                    'nama_sales' => $item->nama_sales,
                    'hadir' => $item->hadir,
                    'jumlah_kehadiran' => $item->jumlah_kehadiran,
                    'waktu_kehadiran' => $item->waktu_kehadiran,
                    'agen_info' => [[
                        'kode_agen' => $item->kode_agen,
                        'nama_agen' => $item->nama_agen,
                        'nama_sales' => $item->nama_sales
                    ]]
                ];
            } else {
                // Tambahkan informasi agen ke data yang sudah ada
                $groupedData[$uniqueKey]->agen_info[] = [
                    'kode_agen' => $item->kode_agen,
                    'nama_agen' => $item->nama_agen,
                    'nama_sales' => $item->nama_sales
                ];
                
                // Update status hadir dan jumlah kehadiran jika perlu
                if ($item->hadir) {
                    $groupedData[$uniqueKey]->hadir = $item->hadir;
                }
                if ($item->jumlah_kehadiran > $groupedData[$uniqueKey]->jumlah_kehadiran) {
                    $groupedData[$uniqueKey]->jumlah_kehadiran = $item->jumlah_kehadiran;
                }
            }
        }
        
        // Proses data daftar agen
        foreach ($daftarAgens as $item) {
            $uniqueKey = strtolower(trim($item->nama_agen)) . '|' . 
                        strtolower(trim($item->pic)) . '|' . 
                        strtolower(trim($item->nomor_pic)) . '|' . 
                        strtolower(trim($item->kota)) . '|';
            
            if (!isset($groupedData[$uniqueKey])) {
                $provinsi = Wilayah::where('kode', $item->provinsi)->first();
                $kota = Wilayah::where('kode', $item->kota)->first();
                
                $groupedData[$uniqueKey] = (object)[
                    'type' => 'Agen',
                    'kode_toko' => $item->kode_agen,
                    'nama_toko' => $item->nama_agen,
                    'pic' => $item->pic,
                    'nomor_pic' => $item->nomor_pic,
                    'alamat' => $item->alamat,
                    'provinsi' => $provinsi->nama ?? $item->provinsi,
                    'kota' => $kota->nama ?? $item->kota,
                    'kode_agen' => '-',
                    'nama_agen' => '-',
                    'nama_sales' => '-',
                    'hadir' => $item->hadir,
                    'jumlah_kehadiran' => $item->jumlah_kehadiran,
                    'waktu_kehadiran' => $item->waktu_kehadiran,
                    'agen_info' => [[
                        'kode_agen' => '-',
                        'nama_agen' => '-',
                        'nama_sales' => '-'
                    ]]
                ];
            } else {
                $groupedData[$uniqueKey]->agen_info[] = [
                    'kode_agen' => '-',
                    'nama_agen' => '-',
                    'nama_sales' => '-'
                ];
            }
        }
        
        $gabunganData = collect(array_values($groupedData));
        
        return $gabunganData->sortBy(function($item) {
            return strtolower($item->nama_toko);
        })->values();
    }

    public function headings(): array
    {
        return [
            'No',
            'Jumlah Hadir',
            'Status Hadir',
            'Tipe',
            'Kode Toko',
            'Nama Toko',
            'PIC',
            'Nomor PIC',
            'Kota',
            'Kode Agen',
            'Nama Agen',
            'Nama Sales',
            'Alamat',
            'Waktu Kehadiran',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        
        // Format informasi agen untuk ditampilkan
        $kodeAgenList = '';
        $namaAgenList = '';
        $namaSalesList = '';
        
        if (isset($row->agen_info) && count($row->agen_info) > 0) {
            foreach ($row->agen_info as $index => $agen) {
                if ($index > 0) {
                    $kodeAgenList .= "\n";
                    $namaAgenList .= "\n";
                    $namaSalesList .= "\n";
                }
                $kodeAgenList .= $agen['kode_agen'];
                $namaAgenList .= $agen['nama_agen'];
                $namaSalesList .= $agen['nama_sales'];
            }
        } else {
            $kodeAgenList = $row->kode_agen;
            $namaAgenList = $row->nama_agen;
            $namaSalesList = $row->nama_sales;
        }

        // Format waktu kehadiran
        $waktuKehadiran = '';
        if (isset($row->waktu_kehadiran) && $row->waktu_kehadiran) {
            // Format dari "H:i:s" menjadi "H:i"
            $waktuParts = explode(':', $row->waktu_kehadiran);
            if (count($waktuParts) >= 2) {
                $waktuKehadiran = $waktuParts[0] . ':' . $waktuParts[1];
            }
        }
        
        return [
            $this->rowNumber,
            $row->jumlah_kehadiran,
            $row->hadir ? 'Hadir' : 'Tidak Hadir',
            $row->type,
            $row->kode_toko,
            $row->nama_toko,
            $row->pic,
            $row->nomor_pic,
            $row->kota,
            $kodeAgenList,
            $namaAgenList,
            $namaSalesList,
            $row->alamat,
            $waktuKehadiran,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply styling untuk semua row yang memiliki multiple agen (dengan line breaks)
        $lastRow = $this->rowNumber + 1; // +1 untuk header
        for ($row = 2; $row <= $lastRow; $row++) {
            $sheet->getStyle("J{$row}:L{$row}")->getAlignment()->setWrapText(true);
        }
        
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ],
            'A:O' => [ // Ubah dari 'A:O' menjadi 'A:P' karena tambah 1 kolom
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
            ],
            'J:L' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
            ],
            'O' => [ // Tambahkan style untuk kolom waktu kehadiran
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,    // No
            'B' => 10,   // Tipe
            'C' => 15,   // Kode Toko
            'D' => 30,   // Nama Toko
            'E' => 20,   // PIC
            'F' => 15,   // Nomor PIC
            'G' => 35,   // Alamat
            'H' => 15,   // Provinsi
            'I' => 15,   // Kota
            'J' => 15,   // Kode Agen
            'K' => 20,   // Nama Agen
            'L' => 20,   // Nama Sales
            'M' => 12,   // Jumlah Hadir
            'N' => 15,   // Status Hadir
            'O' => 15,   // Waktu Hadir
        ];
    }
}