<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormOrderDetail extends Model
{
    use HasFactory;

    protected $table = 'form_order_details';

    protected $fillable = [
        'form_order_id',
        'master_target_id',
        'paket',
        'point_per_paket',
        'jumlah_pengambilan',
        'total_point',
        'kupon_per_paket',
        'total_kupon',
    ];

    protected $casts = [
        'point_per_paket' => 'integer',
        'jumlah_pengambilan' => 'integer',
        'total_point' => 'integer',
    ];

    /**
     * Relasi ke form order header
     */
    public function formOrder(): BelongsTo
    {
        return $this->belongsTo(FormOrder::class, 'form_order_id');
    }

    /**
     * Relasi ke master target
     */
    public function masterTarget(): BelongsTo
    {
        return $this->belongsTo(MasterTarget::class, 'master_target_id');
    }

    /**
     * Hitung total point
     */
    public function calculateTotalPoint()
    {
        return $this->point_per_paket * $this->jumlah_pengambilan;
    }

    /**
     * Update total point
     */
    public function updateTotalPoint()
    {
        $this->total_point = $this->calculateTotalPoint();
        $this->save();
    }

    /**
     * Event ketika model disimpan
     */
    protected static function booted()
    {
        static::saving(function ($detail) {
            $detail->total_point = $detail->calculateTotalPoint();
        });

        static::saved(function ($detail) {
            // Update total point di header
            $detail->formOrder->updateTotalPoint();
        });

        static::deleted(function ($detail) {
            // Update total point di header setelah delete
            $detail->formOrder->updateTotalPoint();
        });
    }
}