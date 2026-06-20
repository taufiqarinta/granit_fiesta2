<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tanggal',
        'merk_id',
        'ukuran_id',
        'motif',
        'estimasi',
        'prioritas',
        'order_id',
        'user_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function merk()
    {
        return $this->belongsTo(Merk::class);
    }

    public function ukuran()
    {
        return $this->belongsTo(Ukuran::class);
    }

    public function prioritas()
    {
        return $this->belongsTo(Prioritas::class, 'prioritas', 'id_prioritas');
    }
}
