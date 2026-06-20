<?php

namespace App\Imports;

use App\Models\DaftarAgen;
use App\Models\UsersMerks;
use App\Models\Wilayah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DaftarAgenImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;
    
    private $successCount = 0;
    private $errorRows = [];
    private $rowNumber = 0;
    
    public function model(array $row)
    {
        $this->rowNumber++;
        
        // Skip jika semua kolom wajib kosong
        if (empty($row['kode_agen']) && empty($row['nama_agen'])) {
            return null;
        }
        
        // Cek apakah kode agen sudah ada
        $existingAgen = DaftarAgen::where('kode_agen', $row['kode_agen'])->first();
        if ($existingAgen) {
            $this->errorRows[] = [
                'row' => $this->rowNumber,
                'kode_agen' => $row['kode_agen'],
                'error' => 'Kode agen sudah terdaftar'
            ];
            return null;
        }
        
        // Proses nilai checkin
        $checkinValue = null;
        if (isset($row['checkin']) && !empty($row['checkin'])) {
            // Konversi Ya/ya/Y/y menjadi 'Check in', selain itu null
            if (in_array(strtolower(trim($row['checkin'])), ['ya', 'y', 'check in', 'checkin'])) {
                $checkinValue = 'Check in';
            } else if (strtolower(trim($row['checkin'])) == 'tidak') {
                $checkinValue = null;
            } else {
                $checkinValue = $row['checkin']; // biarkan apa adanya jika bukan keyword
            }
        }
        
        // Siapkan data untuk insert
        $agenData = [
            'kode_agen' => !empty($row['kode_agen']) ? strtoupper(trim($row['kode_agen'])) : null,
            'nama_agen' => !empty($row['nama_agen']) ? strtoupper(trim($row['nama_agen'])) : null,
            'alamat' => !empty($row['alamat_toko']) ? strtoupper(trim($row['alamat_toko'])) : null,
            'kota' => !empty($row['kota']) ? strtoupper(trim($row['kota'])) : null,
            'pic' => !empty($row['pic']) ? strtoupper(trim($row['pic'])) : null,
            'nomor_pic' => !empty($row['no_wa_pic']) ? trim($row['no_wa_pic']) : null,
            'lokasi_event' => !empty($row['lokasi_event']) ? trim($row['lokasi_event']) : null,
            'hotel' => !empty($row['nama_hotel']) ? strtoupper(trim($row['nama_hotel'])) : null,
            'checkin' => $checkinValue,
            'status' => 1, // Default aktif
            'hadir' => 0,
            'jumlah_kehadiran' => 0,
        ];
        
        // Hapus kolom yang null
        $agenData = array_filter($agenData, function($value) {
            return !is_null($value);
        });
        
        // Validasi minimal: kode_agen dan nama_agen harus ada
        if (empty($agenData['kode_agen']) || empty($agenData['nama_agen'])) {
            $this->errorRows[] = [
                'row' => $this->rowNumber,
                'kode_agen' => $row['kode_agen'] ?? '-',
                'error' => 'Kode agen dan Nama agen wajib diisi'
            ];
            return null;
        }
        
        $this->successCount++;
        return new DaftarAgen($agenData);
    }
    
    public function rules(): array
    {
        return [
            'kode_agen' => 'nullable|string|max:50',
            'nama_agen' => 'nullable|string|max:255',
            'alamat_toko' => 'nullable|string',
            'kota' => 'nullable|string|max:100',
            'pic' => 'nullable|string|max:255',
            'no_wa_pic' => 'nullable|string|max:20',
            'lokasi_event' => 'nullable|string|max:100',
            'nama_hotel' => 'nullable|string|max:255',
            'checkin' => 'nullable|string|max:20',
        ];
    }
    
    public function getSuccessCount()
    {
        return $this->successCount;
    }
    
    public function getErrorRows()
    {
        return $this->errorRows;
    }
}