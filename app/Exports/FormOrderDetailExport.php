<?php

namespace App\Exports;

use App\Models\FormOrder;
use App\Models\DaftarToko;
use App\Models\FormOrderDetail;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FormOrderDetailExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $request;
    protected $rowNumber = 0;
    protected $totalRows = 0;
    protected $summaryData = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = FormOrder::with(['details', 'vouchers']);
        
        // Filter untuk user role
        // if(auth()->user()->role_as == 0) {
        //     $query->where('kode_agen', auth()->user()->id_customer);
        // }
        
        // Apply search filter jika ada
        if ($this->request->has('search') && $this->request->search != '') {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_agen', 'like', "%{$search}%")
                ->orWhere('nama_sales', 'like', "%{$search}%")
                ->orWhere('nama_toko', 'like', "%{$search}%")
                ->orWhere('pic', 'like', "%{$search}%")
                ->orWhere('kota', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%")
                ->orWhere('kode_unik_voucher', 'like', "%{$search}%")
                ->orWhere('kode_toko', 'like', "%{$search}%");
            });
        }
        
        // Apply lokasi event filter hanya jika bukan 'semua'
        if ($this->request->has('lokasi_event') && $this->request->lokasi_event != '' && $this->request->lokasi_event != 'semua') {
            $query->where('lokasi_event', $this->request->lokasi_event);
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        $this->totalRows = count($data);
        
        // Prepare summary data
        $this->prepareSummaryData($data);
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pembuatan',
            'Nama Toko',
            'Kode Toko',
            'PIC',
            'Nomor PIC',
            'Kota Toko',
            'Alamat',
            'Lokasi Event',
            'Brand',
            'Paket',
            'Total Point',
            'Jumlah Voucher',
            'Kode Unik Voucher',
            'Kode Agen',
            'Nama Agen',
            'Nama Sales',
            'Hadir',
            'Jumlah Kehadiran'
        ];
    }

    public function map($order): array
    {
        $this->rowNumber++;

        // Cari data toko dari DaftarToko berdasarkan kode_toko
        $tokoData = null;
        if ($order->kode_toko) {
            $tokoData = DaftarToko::where('kode_toko', $order->kode_toko)
                ->where('lokasi_event', $order->lokasi_event)
                ->orderBy('id', 'desc')
                ->first();
        }

        // Jika tidak ditemukan dengan kode_toko, cari dengan data lainnya
        if (!$tokoData) {
            $tokoData = DaftarToko::where('nama_toko', $order->nama_toko)
                ->where('pic', $order->pic)
                ->where('nomor_pic', $order->no_hp)
                ->where('lokasi_event', $order->lokasi_event)
                ->orderBy('id', 'desc')
                ->first();
        }

        // Format status hadir
        $statusHadir = $tokoData ? ($tokoData->hadir == 1 ? 'Hadir' : 'Tidak Hadir') : 'Tidak Hadir';
        
        // Format jumlah kehadiran
        $jumlahKehadiran = $tokoData ? $tokoData->jumlah_kehadiran : 0;

        // Format alamat
        $alamat = $tokoData ? $tokoData->alamat : '';

        // Ambil data paket dari form_order_details
        $paketList = [];
        if ($order->details && $order->details->isNotEmpty()) {
            foreach ($order->details as $detail) {
                if (!empty($detail->paket)) {
                    $paketList[] = $detail->paket;
                }
            }
        }
        
        // Gabungkan paket dengan koma jika ada lebih dari satu
        $paketString = !empty($paketList) ? implode(', ', $paketList) : '';

        return [
            $this->rowNumber,
            $order->tanggal_order_formatted,
            $order->nama_toko,
            $order->kode_toko ?? '',
            $order->pic,
            $order->no_hp,
            $order->kota,
            $alamat,
            $order->lokasi_event,
            $order->brand ?? '',
            $paketString,
            $order->total_point,
            $order->jumlah_voucher ?? 0,
            $order->kode_unik_voucher ?? '',
            $order->kode_agen,
            $order->nama_agen,
            $order->nama_sales,
            $statusHadir,
            $jumlahKehadiran
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply styling untuk header
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ],
            'A:S' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
            ],
            'H' => [ // STYLE KHUSUS UNTUK KOLOM ALAMAT
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
            ],
            'K' => [ // STYLE KHUSUS UNTUK KOLOM PAKET BARU
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,    // No
            'B' => 15,   // Tanggal Pembuatan
            'C' => 30,   // Nama Toko
            'D' => 15,   // Kode Toko
            'E' => 20,   // PIC
            'F' => 15,   // Nomor PIC
            'G' => 15,   // Kota Toko
            'H' => 30,   // Alamat
            'I' => 20,   // Lokasi Event
            'J' => 20,   // Brand
            'K' => 25,   // PAKET
            'L' => 12,   // Total Point
            'M' => 12,   // Jumlah Voucher
            'N' => 20,   // Kode Unik Voucher
            'O' => 15,   // Kode Agen
            'P' => 20,   // Nama Agen
            'Q' => 20,   // Nama Sales
            'R' => 12,   // Hadir
            'S' => 15,   // Jumlah Kehadiran
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Tambahkan summary table setelah data utama
                $this->addSummaryTable($event);
            },
        ];
    }

    protected function prepareSummaryData($orders)
    {
        $this->summaryData = [];
        
        // Get all unique packages
        $allPackages = FormOrderDetail::distinct()
            ->whereNotNull('paket')
            ->where('paket', '!=', '')
            ->pluck('paket')
            ->sort()
            ->toArray();
        
        // Get all unique locations from the filtered data
        $allLocations = $orders->pluck('lokasi_event')->unique()->sort()->toArray();
        
        // Initialize summary data structure
        foreach ($allPackages as $package) {
            $this->summaryData[$package] = [];
            foreach ($allLocations as $location) {
                $this->summaryData[$package][$location] = 0;
            }
        }
        
        // Count packages per location
        foreach ($orders as $order) {
            if ($order->details && $order->details->isNotEmpty()) {
                foreach ($order->details as $detail) {
                    if (!empty($detail->paket) && isset($this->summaryData[$detail->paket])) {
                        if (isset($this->summaryData[$detail->paket][$order->lokasi_event])) {
                            $this->summaryData[$detail->paket][$order->lokasi_event]++;
                        } else {
                            $this->summaryData[$detail->paket][$order->lokasi_event] = 1;
                        }
                    }
                }
            }
        }
    }

    protected function addSummaryTable(AfterSheet $event)
    {
        if (empty($this->summaryData)) {
            return;
        }
        
        $sheet = $event->sheet->getDelegate();
        
        // Start row for summary table (after main data + 2 empty rows)
        $startRow = $this->totalRows + 4;
        
        // Get unique locations
        $locations = [];
        foreach ($this->summaryData as $packageData) {
            foreach (array_keys($packageData) as $location) {
                if (!in_array($location, $locations)) {
                    $locations[] = $location;
                }
            }
        }
        sort($locations);
        
        // Add summary header
        $headerRow = $startRow;
        $sheet->setCellValue('A' . $headerRow, 'Paket');
        $col = 'B';
        foreach ($locations as $location) {
            $sheet->setCellValue($col . $headerRow, $location);
            $col++;
        }
        
        // Style header
        $headerRange = 'A' . $headerRow . ':' . chr(ord('A') + count($locations)) . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '34D399'] // Green color for summary header
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ]);
        
        // Add package data
        $currentRow = $headerRow + 1;
        foreach ($this->summaryData as $package => $locationData) {
            $sheet->setCellValue('A' . $currentRow, $package);
            
            $col = 'B';
            foreach ($locations as $location) {
                $count = isset($locationData[$location]) ? $locationData[$location] : 0;
                $sheet->setCellValue($col . $currentRow, $count);
                $col++;
            }
            $currentRow++;
        }
        
        // Add totals row
        $totalRow = $currentRow;
        $sheet->setCellValue('A' . $totalRow, 'TOTAL');
        
        $col = 'B';
        foreach ($locations as $location) {
            $total = 0;
            foreach ($this->summaryData as $packageData) {
                $total += isset($packageData[$location]) ? $packageData[$location] : 0;
            }
            $sheet->setCellValue($col . $totalRow, $total);
            $col++;
        }
        
        // Style totals row
        $totalRange = 'A' . $totalRow . ':' . chr(ord('A') + count($locations)) . $totalRow;
        $sheet->getStyle($totalRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FBBF24'] // Yellow color for totals
            ]
        ]);
        
        // Auto width for summary columns
        $summaryCol = 'A';
        for ($i = 0; $i <= count($locations); $i++) {
            $sheet->getColumnDimension($summaryCol)->setAutoSize(true);
            $summaryCol++;
        }
        
        // Add border to summary table
        $summaryRange = 'A' . $headerRow . ':' . chr(ord('A') + count($locations)) . $totalRow;
        $sheet->getStyle($summaryRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    }
}