<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motif extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'ukuran_id'];

    public function ukuran()
    {
        return $this->belongsTo(Ukuran::class);
    }
}
