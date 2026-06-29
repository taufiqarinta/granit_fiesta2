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
        'nama_file',
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

    /**
     * Mendapatkan path lengkap gambar
     */
    public function getImagePathAttribute()
    {
        return $this->nama_file ? asset('images/doorprizes/' . $this->nama_file) : null;
    }

    /**
     * Cek apakah doorprize adalah voucher
     */
    public function isVoucher()
    {
        return str_contains($this->nama_doorprize, 'Voucher') || 
               str_contains($this->nama_doorprize, 'Uang');
    }
}