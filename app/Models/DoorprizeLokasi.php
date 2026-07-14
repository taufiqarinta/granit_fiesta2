<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoorprizeLokasi extends Model
{
    use HasFactory;

    protected $table = 'doorprize_lokasi';

    protected $fillable = [
        'doorprize_id',
        'lokasi_event',
        'jumlah_doorprize',
        'status'
    ];

    protected $casts = [
        'jumlah_doorprize' => 'integer',
        'status' => 'integer'
    ];

    /**
     * Relasi ke Doorprize
     */
    public function doorprize()
    {
        return $this->belongsTo(Doorprize::class);
    }

    /**
     * Scope untuk data aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}