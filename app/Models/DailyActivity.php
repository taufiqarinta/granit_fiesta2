<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    use HasFactory;

    protected $table = 'daily_activities';

    protected $fillable = [
        'id',
        'user_id',
        'sub_project_id',
        'tanggal',
        'waktu',
        'keterangan',
        'cuti',
        'sakit',
        'ijin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subProject()
    {
        return $this->belongsTo(SubProject::class);
    }
}
