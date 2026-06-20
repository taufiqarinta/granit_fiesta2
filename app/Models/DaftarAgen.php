<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarAgen extends Model
{
    use HasFactory;

    protected $table = 'daftar_agen';
    
    protected $fillable = [
        'kode_agen',
        'nama_agen',
        'alamat',
        'provinsi',
        'kota',
        'pic',
        'nomor_pic',
        'lokasi_event',
        'status',
        'hadir',
        'jumlah_kehadiran',
        'waktu_kehadiran',
            'hotel',
            'checkin',
    ];

    // Relasi ke users_merks - PERBAIKI: gunakan kode_agen sebagai foreign key
    public function usersMerks()
    {
        return $this->hasMany(UsersMerk::class, 'id_customer', 'kode_agen');
    }

    // Relasi many-to-many ke merks melalui users_merks
    public function merks()
    {
        return $this->belongsToMany(
            Merk::class,
            'users_merks',
            'id_customer', // Foreign key pada users_merks table
            'id_merks', // Foreign key pada users_merks table yang merujuk ke merks
            'kode_agen', // Local key pada daftar_agen table (kode_agen)
            'id' // Local key pada merks table
        );
    }
}