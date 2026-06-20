<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_toko',
        'nama_pemilik',
        'alamat', 
        'provinsi',
        'kota',
        'hadir'
    ];

    protected $casts = [
        'hadir' => 'boolean'
    ];
}