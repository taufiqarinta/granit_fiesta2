<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2 extends Model
{
    use HasFactory;

    protected $table = 'p2';

    protected $fillable = [
        'id',
        'name'
    ];

    public function dailyActivities()
    {
        return $this->hasMany(DailyActivity::class);
    }
}
