<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormLkp extends Model
{
    protected $table = 'form_lkp';

    protected $fillable = [
        'nomor',
        'tanggal',
        'nama',
        'email',
        'provinsi',
        'kabupaten',
        'via_agen',
        'no_sj',
        'lampiran_sj',
        'tanggal_pembelian',
        'tanggal_komplain',
        'sales',
        'id_merks',
        'id_ukurans',
        'id_motifs',
        'kw',
        'tonality',
        'kaliber',
        'batch',
        'lampiran_batch',
        'jumlah_order',
        'jumlah_kirim',
        'jumlah_komplain',
        'jenis_komplain',
        'lampiran_bukti',
        'penyelesaian',
        'analisa',
        'lampiran_analisa',
        'keputusan',
        'created_by',
        'created_date',
        'created_time',
        'approval1_by',
        'approval1_date',
        'approval1_time',
        'approval1_status',
        'approval2_by',
        'approval2_date',
        'approval2_time',
        'approval2_status',
        'approval3_by',
        'approval3_date',
        'approval3_time',
        'approval3_status',
        'approval4_by',
        'approval4_date',
        'approval4_time',
        'approval4_status',
        'status',
        'flag',
    ];

    public $timestamps = false; // Matikan jika tabel tidak pakai created_at dan updated_at

    public function merks()
    {
        return $this->belongsTo(Merk::class, 'id_merks');
    }

    public function ukurans()
    {
        return $this->belongsTo(Ukuran::class, 'id_ukurans');
    }

    public function motifs()
    {
        return $this->belongsTo(Motif::class, 'id_motifs');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_customer');
    }


}
