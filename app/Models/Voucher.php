<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vouchers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_unik',
        'nomor_voucher',
        'kode_toko',
        'nama_toko',
        'nama_pic',
        'form_order_id',
        'lokasi_event',
        'status',
        'hadiah',
        'sudah_ditukarkan',
        'ditukarkan_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the form order that owns the voucher.
     */
    public function formOrder()
    {
        return $this->belongsTo(FormOrder::class, 'form_order_id');
    }

    /**
     * Scope a query to only include active vouchers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Scope a query to only include used vouchers.
     */
    public function scopeUsed($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Check if voucher is active.
     */
    public function isActive()
    {
        return $this->status === 0;
    }

    /**
     * Check if voucher is used.
     */
    public function isUsed()
    {
        return $this->status === 1;
    }

    /**
     * Mark voucher as used.
     */
    public function markAsUsed()
    {
        $this->update(['status' => 1]);
    }
}