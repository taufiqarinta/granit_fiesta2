<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderGathering extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_agen',
        'nama_toko',
        'provinsi',
        'kota_kab',
        'alamat',
        'target',
        'total_point',
        'tanggal_order'
    ];

    protected $casts = [
        'tanggal_order' => 'date'
    ];

    public function details()
    {
        return $this->hasMany(OrderGatheringDetail::class);
    }
}