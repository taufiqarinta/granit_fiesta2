<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ukuran extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'merk_id'];

    public function merk()
    {
        return $this->belongsTo(Merk::class);
    }

    public function motifs()
    {
        return $this->hasMany(Motif::class);
    }
}
