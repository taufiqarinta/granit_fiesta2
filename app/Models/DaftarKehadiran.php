<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarKehadiran extends Model
{
    use HasFactory;

    protected $table = 'daftar_kehadiran';

    protected $fillable = [
        'kode_toko',
        'lokasi_event',
        'hadir',
        'total_kehadiran',
    ];

    public function toko()
    {
        return $this->belongsTo(DaftarToko::class, 'kode_toko', 'kode_toko');
    }
}