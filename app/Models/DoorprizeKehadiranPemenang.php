<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoorprizeKehadiranPemenang extends Model
{
    use HasFactory;

    protected $table = 'doorprize_kehadiran_pemenang';

    protected $fillable = [
        'doorprize_kehadiran_id',
        'kode_toko',
        'nama_toko',
        'nama_pic',
        'kota',
        'lokasi_event',
        'hadiah',
        'sudah_ditukarkan',
        'ditukarkan_at',
    ];

    protected $casts = [
        'sudah_ditukarkan' => 'integer',
        'ditukarkan_at' => 'datetime',
    ];

    /**
     * Relasi ke DoorprizeKehadiran
     */
    public function doorprize()
    {
        return $this->belongsTo(DoorprizeKehadiran::class);
    }

    /**
     * Scope untuk pemenang yang sudah menang di lokasi tertentu
     */
    public function scopeSudahMenangDiLokasi($query, $lokasi)
    {
        return $query->where('lokasi_event', $lokasi);
    }
}
