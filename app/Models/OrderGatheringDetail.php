<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderGatheringDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_gathering_id',
        'brand',
        'motif',
        'jumlah_box',
        'jumlah_point'
    ];

    public function orderGathering()
    {
        return $this->belongsTo(OrderGathering::class);
    }
}