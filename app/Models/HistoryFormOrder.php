<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryFormOrder extends Model
{
    protected $table = 'history_form_orders';

    protected $fillable = [
        'form_order_id',
        'aksi',
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
        'total_kupon',
        'jumlah_voucher',
        'kode_unik_voucher',
        'nama_terang',
        'detail_targets',
        'user_id',
        'username',
        'ip_address',
    ];

    protected $casts = [
        'detail_targets' => 'array',
    ];

    public function formOrder()
    {
        return $this->belongsTo(FormOrder::class, 'form_order_id');
    }
}