<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CabangCustomer extends Model
{
    protected $table = 'cabang_customer';
    public $timestamps = false;

    protected $fillable = ['id_cabang', 'nama_cabang', 'id_customer'];
}

