<?php

namespace App\Exports;

use App\Models\FormOrder;
use App\Models\DaftarToko;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FormOrderDetailExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $request;
    protected $rowNumber = 0;

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
        
        return $query->orderBy('created_at', 'desc')->get();
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
            'Paket', // KOLOM BARU: PAKET
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
            $paketString, // KOLOM BARU: PAKET
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
            'A:S' => [ // DIUBAH DARI 'A:R' MENJADI 'A:S' KARENA TAMBAH 1 KOLOM
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
            'K' => 25,   // PAKET BARU (ditambahkan)
            'L' => 12,   // Total Point (dipindah ke L)
            'M' => 12,   // Jumlah Voucher (dipindah ke M)
            'N' => 20,   // Kode Unik Voucher (dipindah ke N)
            'O' => 15,   // Kode Agen (dipindah ke O)
            'P' => 20,   // Nama Agen (dipindah ke P)
            'Q' => 20,   // Nama Sales (dipindah ke Q)
            'R' => 12,   // Hadir (dipindah ke R)
            'S' => 15,   // Jumlah Kehadiran (dipindah ke S)
        ];
    }
}