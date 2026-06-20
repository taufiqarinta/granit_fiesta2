<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'target',
        'point',
        'periode_awal',
        'periode_akhir',
        'kupon',
        'status',
    ];

    protected $casts = [
        'point' => 'integer',
        'periode_awal' => 'date',
        'periode_akhir' => 'date'
    ];

    /**
     * Scope untuk data yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope untuk data yang inactive
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}