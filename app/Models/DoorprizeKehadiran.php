<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoorprizeKehadiran extends Model
{
    use HasFactory;

    protected $table = 'doorprize_kehadiran';

    protected $fillable = [
        'nama_doorprize',
        'nama_file',
        'status'
    ];

    protected $casts = [
        'status' => 'integer'
    ];

    /**
     * Relasi ke tabel pivot doorprize_kehadiran_lokasi
     */
    public function lokasi()
    {
        return $this->hasMany(DoorprizeKehadiranLokasi::class);
    }

    /**
     * Relasi ke pemenang
     */
    public function pemenang()
    {
        return $this->hasMany(DoorprizeKehadiranPemenang::class);
    }

    /**
     * Scope untuk doorprize aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Mendapatkan path lengkap gambar
     */
    public function getImagePathAttribute()
    {
        return $this->nama_file ? asset('images/doorprizes/' . $this->nama_file) : null;
    }

    /**
     * Cek apakah doorprize adalah voucher
     */
    public function isVoucher()
    {
        return str_contains($this->nama_doorprize, 'Voucher') ||
               str_contains($this->nama_doorprize, 'Uang');
    }

    /**
     * Get jumlah doorprize untuk lokasi tertentu
     */
    public function getJumlahForLokasi($lokasi)
    {
        $lokasiData = $this->lokasi()->where('lokasi_event', strtoupper($lokasi))->first();
        return $lokasiData ? $lokasiData->jumlah_doorprize : 0;
    }

    /**
     * Get status untuk lokasi tertentu
     */
    public function getStatusForLokasi($lokasi)
    {
        $lokasiData = $this->lokasi()->where('lokasi_event', strtoupper($lokasi))->first();
        return $lokasiData ? $lokasiData->status : 0;
    }
}
