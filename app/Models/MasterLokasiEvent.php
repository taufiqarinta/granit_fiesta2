<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterLokasiEvent extends Model
{
    use HasFactory;

    protected $table = 'master_lokasi_event';

    protected $fillable = [
        'tanggal',
        'nama_lokasi',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        // 'status' => 'integer'
    ];

    /**
     * Scope untuk data aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', "Aktif");
    }

    /**
     * Relasi ke doorprize_lokasi
     */
    public function doorprizeLokasi()
    {
        return $this->hasMany(DoorprizeLokasi::class, 'lokasi_event', 'nama_lokasi');
    }
}