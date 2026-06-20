<?php

namespace App\Exports;

use App\Models\FormOrder;
use App\Models\DaftarToko;
use App\Models\DaftarAgen;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FormOrderExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $request;
    protected $rowNumber = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = FormOrder::with(['details', 'vouchers']); // TAMBAHKAN vouchers relation
        
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
                ->orWhere('kode_unik_voucher', 'like', "%{$search}%");
            });
        }
        
        // Apply lokasi event filter hanya jika bukan 'semua'
        if ($this->request->has('lokasi_event') && $this->request->lokasi_event != '' && $this->request->lokasi_event != 'semua') {
            $query->where('lokasi_event', $this->request->lokasi_event);
        }
        
        $formOrders = $query->orderBy('created_at', 'desc')->get();
        
        // Group form orders berdasarkan toko yang sama (tanpa nama_sales)
        $groupedData = [];
        
        foreach ($formOrders as $order) {
            // Buat unique key berdasarkan data toko (TANPA nama_sales)
            $uniqueKey = strtolower(trim($order->nama_toko)) . '|' . 
                        strtolower(trim($order->pic)) . '|' . 
                        strtolower(trim($order->no_hp)) . '|' . 
                        $order->kota . '|' . 
                        $order->lokasi_event;
            
            if (!isset($groupedData[$uniqueKey])) {
                // CARI DI DAFTAR_TOKO SAJA dengan kondisi yang sama
                $tokoData = DaftarToko::where('nama_toko', $order->nama_toko)
                    ->where('pic', $order->pic)
                    ->where('nomor_pic', $order->no_hp)
                    // ->where('kota', $order->kota)
                    ->where('lokasi_event', $order->lokasi_event)
                    ->orderBy('id', 'desc')
                    ->first();
                
                // Hitung total voucher untuk semua form orders dengan toko yang sama
                $totalVoucher = $order->jumlah_voucher;
                $kodeUnikList = $order->kode_unik_voucher ? [$order->kode_unik_voucher] : [];
                
                $groupedData[$uniqueKey] = (object)[
                    'nama_toko' => $order->nama_toko,
                    'pic' => $order->pic,
                    'no_hp' => $order->no_hp,
                    'kota' => $order->kota,
                    'lokasi_event' => $order->lokasi_event,
                    'tanggal_order_formatted' => $order->tanggal_order_formatted,
                    'total_point' => $order->total_point,
                    'total_voucher' => $totalVoucher, // TAMBAHKAN total voucher
                    'kode_unik_list' => $kodeUnikList, // TAMBAHKAN kode unik list
                    'hadir' => $tokoData ? $tokoData->hadir : 0,
                    'jumlah_kehadiran' => $tokoData ? $tokoData->jumlah_kehadiran : 0,
                    'alamat' => $tokoData ? $tokoData->alamat : '', // TAMBAHKAN ALAMAT
                    'agen_info' => [[
                        'nama_agen' => $order->nama_agen,
                        'kode_agen' => $order->kode_agen,
                        'nama_sales' => $order->nama_sales
                    ]],
                    'form_orders' => [$order]
                ];
            } else {
                // Tambahkan informasi agen ke data yang sudah ada
                $groupedData[$uniqueKey]->agen_info[] = [
                    'nama_agen' => $order->nama_agen,
                    'kode_agen' => $order->kode_agen,
                    'nama_sales' => $order->nama_sales
                ];
                
                // Tambahkan total point
                $groupedData[$uniqueKey]->total_point += $order->total_point;
                
                // Tambahkan jumlah voucher
                $groupedData[$uniqueKey]->total_voucher += $order->jumlah_voucher;
                
                // Tambahkan kode unik jika ada
                if ($order->kode_unik_voucher && !in_array($order->kode_unik_voucher, $groupedData[$uniqueKey]->kode_unik_list)) {
                    $groupedData[$uniqueKey]->kode_unik_list[] = $order->kode_unik_voucher;
                }
                
                // CARI DATA KEHADIRAN TERBARU DARI DAFTAR_TOKO untuk update
                $tokoData = DaftarToko::where('nama_toko', $order->nama_toko)
                    ->where('pic', $order->pic)
                    ->where('nomor_pic', $order->no_hp)
                    ->where('kota', $order->kota)
                    ->where('lokasi_event', $order->lokasi_event)
                    ->orderBy('id', 'desc')
                    ->first();

                // UPDATE data kehadiran jika ditemukan data yang lebih baru
                if ($tokoData) {
                    $groupedData[$uniqueKey]->hadir = $tokoData->hadir;
                    $groupedData[$uniqueKey]->jumlah_kehadiran = $tokoData->jumlah_kehadiran;
                    $groupedData[$uniqueKey]->alamat = $tokoData->alamat; // UPDATE ALAMAT
                }
                
                $groupedData[$uniqueKey]->form_orders[] = $order;
            }
        }
        
        return collect(array_values($groupedData))->sortBy(function($item) {
            return strtolower($item->nama_toko);
        })->values();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pembuatan',
            'Nama Toko',
            'PIC',
            'Nomor PIC',
            'Kota Toko',
            'Alamat', // KOLOM BARU: ALAMAT
            'Lokasi Event',
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
            $kodeAgenList = $row->agen_info[0]['kode_agen'] ?? '';
            $namaAgenList = $row->agen_info[0]['nama_agen'] ?? '';
            $namaSalesList = $row->agen_info[0]['nama_sales'] ?? '';
        }
        
        // Format kode unik voucher
        $kodeUnikVoucher = '';
        if (isset($row->kode_unik_list) && count($row->kode_unik_list) > 0) {
            foreach ($row->kode_unik_list as $index => $kodeUnik) {
                if ($index > 0) {
                    $kodeUnikVoucher .= "\n";
                }
                $kodeUnikVoucher .= $kodeUnik;
            }
        } else {
            $kodeUnikVoucher = '';
        }
        
        // Format status hadir - ambil langsung dari data yang sudah diolah
        $statusHadir = ($row->hadir == 1) ? 'Hadir' : 'Tidak Hadir';
        
        // Format jumlah kehadiran
        $jumlahKehadiran = $row->jumlah_kehadiran;

        // Format alamat
        $alamat = $row->alamat ?? '';

        return [
            $this->rowNumber,
            $row->tanggal_order_formatted,
            $row->nama_toko,
            $row->pic,
            $row->no_hp,
            $row->kota,
            $alamat, // KOLOM BARU: ALAMAT
            $row->lokasi_event,
            $row->total_point,
            $row->total_voucher ?? 0,
            $kodeUnikVoucher,
            $kodeAgenList,
            $namaAgenList,
            $namaSalesList,
            $statusHadir,
            $jumlahKehadiran
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply styling untuk semua row yang memiliki multiple data (dengan line breaks)
        $lastRow = $this->rowNumber + 1; // +1 untuk header
        for ($row = 2; $row <= $lastRow; $row++) {
            $sheet->getStyle("J{$row}:N{$row}")->getAlignment()->setWrapText(true); // UPDATE RANGE MENJADI J-N
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
            'A:P' => [ // UPDATE RANGE MENJADI A-P (karena tambahan 1 kolom)
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
            ],
            'G' => [ // STYLE KHUSUS UNTUK KOLOM ALAMAT
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
            ],
            'J:N' => [ // UPDATE RANGE MENJADI J-N
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
            'D' => 20,   // PIC
            'E' => 15,   // Nomor PIC
            'F' => 15,   // Kota Toko
            'G' => 30,   // Alamat (BARU) - Lebar kolom disesuaikan
            'H' => 20,   // Lokasi Event
            'I' => 12,   // Total Point
            'J' => 12,   // Jumlah Voucher
            'K' => 20,   // Kode Unik Voucher
            'L' => 15,   // Kode Agen
            'M' => 20,   // Nama Agen
            'N' => 20,   // Nama Sales
            'O' => 12,   // Hadir
            'P' => 15,   // Jumlah Kehadiran
        ];
    }
}