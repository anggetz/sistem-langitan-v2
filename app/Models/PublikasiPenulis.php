<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublikasiPenulis extends Model
{
    protected $table = 'publikasi_penulis';
    protected $primaryKey = 'id_penulis_publikasi';
    public $timestamps = false;

    protected $fillable = [
        'id_publikasi',
        'id_dosen',
        'nama',
        'afiliasi',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * Relasi ke Publikasi
     */
    public function publikasi()
    {
        return $this->belongsTo(Publikasi::class, 'id_publikasi', 'id_publikasi');
    }

    /**
     * Relasi ke Dosen (opsional jika penulis adalah dosen di sistem)
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'id_dosen');
    }
}
