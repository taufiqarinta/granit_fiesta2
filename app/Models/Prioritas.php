<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prioritas extends Model
{
    protected $table = 'prioritas';
    public $timestamps = false;

    protected $fillable = ['id_prioritas', 'nama_prioritas'];
}

