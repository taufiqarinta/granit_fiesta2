<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoorprizeKehadiranLokasi extends Model
{
    use HasFactory;

    protected $table = 'doorprize_kehadiran_lokasi';

    protected $fillable = [
        'doorprize_kehadiran_id',
        'lokasi_event',
        'jumlah_doorprize',
        'status'
    ];

    protected $casts = [
        'jumlah_doorprize' => 'integer',
        'status' => 'integer'
    ];

    /**
     * Relasi ke DoorprizeKehadiran
     */
    public function doorprize()
    {
        return $this->belongsTo(DoorprizeKehadiran::class);
    }

    /**
     * Scope untuk data aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
