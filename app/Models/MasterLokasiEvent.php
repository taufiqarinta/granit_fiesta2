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
    ];
}