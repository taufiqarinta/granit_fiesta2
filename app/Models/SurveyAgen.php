<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAgen extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_survey',
        'nama_agen',
        'kode_agen',
        'status_klaim_hadiah',
    ];

    public function details()
    {
        return $this->hasMany(SurveyAgenDetail::class, 'survey_agen_id');
    }
}

