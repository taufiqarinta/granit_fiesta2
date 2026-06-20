<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doorprize extends Model
{
    use HasFactory;

    protected $table = 'doorprizes';

    protected $fillable = [
        'nama_doorprize',
        'jumlah_doorprize',
        'status'
    ];

    protected $casts = [
        'status' => 'integer',
        'jumlah_doorprize' => 'integer'
    ];

    /**
     * Scope untuk doorprize aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}