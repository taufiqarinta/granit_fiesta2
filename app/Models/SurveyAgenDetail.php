<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAgenDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_agen_id',
        'nama_sales',
        'no_hp',
        'area',
        
        'top_10_pareto',
        'target_penjualan',
        'brands',
        'keliling_luar_kota',
        'toko_butuh_support',
        'saran_kobin'
    ];

    public function survey()
    {
        return $this->belongsTo(SurveyAgen::class, 'survey_agen_id');
    }
}

