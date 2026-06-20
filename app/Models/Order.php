<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'tanggal',
        'status',
        'user_id',
        'id_cabang',
        'hapus',
        'forecast'
    ];

    public function permintaans()
    {
        return $this->hasMany(Permintaan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approval()
    {
        return $this->belongsTo(User::class, 'approval_id');
    }

    public function cabang()
    {
        return $this->belongsTo(CabangCustomer::class, 'id_cabang', 'id_cabang');
    }
}

