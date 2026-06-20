<?php

namespace App\Exports;

use App\Models\DaftarToko;
use App\Models\FormOrder;
use App\Models\Voucher;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class TokoTrackingExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $search;
    protected $lokasiEvent;

    public function __construct($search = null, $lokasiEvent = null)
    {
        $this->search = $search;
        $this->lokasiEvent = $lokasiEvent;
    }

    public function collection()
    {
        try {
            $query = DaftarToko::query();
            
            // Filter berdasarkan role user
            // if(auth()->check() && auth()->user()->role_as == 0){
            //     $query->where('kode_agen', auth()->user()->id_customer);
            // }
            
            // Filter pencarian
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('nama_toko', 'like', "%{$this->search}%")
                      ->orWhere('kode_toko', 'like', "%{$this->search}%")
                      ->orWhere('pic', 'like', "%{$this->search}%")
                      ->orWhere('lokasi_event', 'like', "%{$this->search}%")
                      ->orWhere('kota', 'like', "%{$this->search}%");
                });
            }
            
            // Filter lokasi event
            if ($this->lokasiEvent && $this->lokasiEvent != 'semua') {
                $query->where('lokasi_event', $this->lokasiEvent);
            }
            
            // Ambil data toko yang sudah di-group
            $tokos = $query->selectRaw('
                    nama_toko,
                    pic,
                    kota,
                    nomor_pic,
                    lokasi_event,
                    MAX(kode_toko) as kode_toko,
                    MAX(jumlah_kehadiran) as jumlah_kehadiran,
                    MAX(hotel) as hotel,
                    MAX(checkin) as checkin
                ')
                ->groupBy('nama_toko', 'pic', 'kota', 'nomor_pic', 'lokasi_event')
                ->orderBy('nama_toko', 'asc')
                ->get();
            
            Log::info('Jumlah toko ditemukan: ' . $tokos->count());
            
            // Tambahkan data order dan doorprize untuk setiap toko
            $result = new Collection();
            
            foreach ($tokos as $toko) {
                try {
                    // Hitung total order dari tabel form_orders
                    $totalOrder = FormOrder::where('nama_toko', $toko->nama_toko)
                        ->where('pic', $toko->pic)
                        ->where('no_hp', $toko->nomor_pic)
                        ->where('kota', $toko->kota)
                        ->where('lokasi_event', $toko->lokasi_event)
                        ->sum('total_point');
                    
                    // Ambil doorprize dari tabel vouchers
                    $voucher = Voucher::where('nama_toko', $toko->nama_toko)
                        ->where('nama_pic', $toko->pic)
                        ->where('no_hp', $toko->nomor_pic)
                        ->where('lokasi_event', $toko->lokasi_event)
                        ->first();
                    
                    // Tambahkan data ke result
                    $toko->total_order = $totalOrder ?? 0;
                    $toko->doorprize = $voucher ? ($voucher->hadiah ?? '-') : '-';
                    
                    $result->push($toko);
                    
                } catch (\Exception $e) {
                    Log::error('Error processing toko: ' . $toko->nama_toko, [
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            Log::error('Export trace: ' . $e->getTraceAsString());
            return new Collection();
        }
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Toko',
            'Hadir',
            'Order (Point)',
            'Hotel',
            'Ditempati',
            'Doorprize'
        ];
    }

    public function map($toko): array
    {
        static $counter = 0;
        $counter++;
        
        // Format untuk kolom hotel dan checkin
        $hotel = isset($toko->hotel) && $toko->hotel !== '' ? $toko->hotel : '-';
        $checkin = isset($toko->checkin) && $toko->checkin !== '' ? $toko->checkin : '-';
        $hadir = isset($toko->jumlah_kehadiran) ? (int)$toko->jumlah_kehadiran : 0;
        $order = isset($toko->total_order) ? (float)$toko->total_order : 0;
        $doorprize = isset($toko->doorprize) ? $toko->doorprize : '-';
        
        return [
            $counter,
            $toko->nama_toko ?? '-',
            $hadir,
            $order,
            $hotel,
            $checkin,
            $doorprize
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => '2E75B5']
                ]
            ],
            
            // Style untuk seluruh tabel
            'A:G' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            
            // Auto size kolom
            'A' => ['width' => 8],
            'B' => ['width' => 40],
            'C' => ['width' => 15],
            'D' => ['width' => 15],
            'E' => ['width' => 15],
            'F' => ['width' => 15],
            'G' => ['width' => 30],
        ];
    }
}