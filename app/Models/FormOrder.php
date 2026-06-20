<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Voucher;

class FormOrder extends Model
{
    use HasFactory;

    protected $table = 'form_orders';

    protected $fillable = [
        'is_grand_launching',
        'tanggal_order',
        'kode_agen',
        'nama_agen',
        'kode_toko',
        'nama_toko',
        'nama_sales',
        'lokasi_event',
        'brand',
        'pic',
        'no_hp',
        'kota',
        'total_point',
        'jumlah_voucher',
        'kode_unik_voucher',
        'ttd_pic',
        'ttd_agen',
        'ttd_kobin_tiles',
        'nama_terang',
        'ttd_nama_terang',
    ];

    protected $casts = [
        'tanggal_order' => 'date',
        'total_point' => 'integer',
    ];

    /**
     * Relasi ke detail form order
     */
    public function details(): HasMany
    {
        return $this->hasMany(FormOrderDetail::class, 'form_order_id');
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->where('tanggal_order', $tanggal);
    }

    /**
     * Scope untuk filter berdasarkan agen
     */
    public function scopeByAgen($query, $kodeAgen)
    {
        return $query->where('kode_agen', $kodeAgen);
    }

    /**
     * Scope untuk filter berdasarkan toko
     */
    public function scopeByToko($query, $kodeToko)
    {
        return $query->where('kode_toko', $kodeToko);
    }

    /**
     * Accessor untuk formatted tanggal
     */
    public function getTanggalOrderFormattedAttribute()
    {
        return $this->tanggal_order->format('d F Y');
    }

    /**
     * Hitung ulang total point dari detail
     */
    public function calculateTotalPoint()
    {
        return $this->details()->sum('total_point');
    }

    /**
     * Update total point berdasarkan detail
     */
    public function updateTotalPoint()
    {
        $this->total_point = $this->calculateTotalPoint();
        $this->save();
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'form_order_id');
    }
}