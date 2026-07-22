<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarToko extends Model
{
    use HasFactory;

    protected $table = 'daftar_toko';
    
    protected $fillable = [
        'kode_agen',
        'nama_agen',
        'kode_toko',
        'nama_toko',
        'alamat',
        'provinsi',
        'kota',
        'pic',
        'nomor_pic',
        'email',
        'nama_sales',
        'lokasi_event',
        'status',
        'hadir',
        'jumlah_kehadiran',
        'waktu_kehadiran',
        'hotel',
        'checkin',
        'nomor_kamar_hotel',
        'jumlah_orang_menginap',
    ];

    // Accessor untuk status
    public function getStatusTextAttribute()
    {
        return $this->status == 1 ? 'Aktif' : 'Tidak Aktif';
    }

    // Scope untuk toko aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 1);
    }

    // Scope untuk toko tidak aktif
    public function scopeTidakAktif($query)
    {
        return $query->where('status', 0);
    }
}