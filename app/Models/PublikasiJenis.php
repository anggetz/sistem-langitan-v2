<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublikasiJenis extends Model
{
    protected $table = 'publikasi_jenis';
    protected $primaryKey = 'id_jenis_publikasi';
    public $timestamps = false;

    protected $fillable = [
        'jenis_publikasi',
    ];

    /**
     * Relasi ke Publikasi
     */
    public function publikasi()
    {
        return $this->hasMany(Publikasi::class, 'id_jenis_publikasi', 'id_jenis_publikasi');
    }
}
