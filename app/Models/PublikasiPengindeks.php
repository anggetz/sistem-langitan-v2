<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublikasiPengindeks extends Model
{
    protected $table = 'publikasi_pengindeks';
    protected $primaryKey = 'id_pengindeks_publikasi';
    public $timestamps = false;

    protected $fillable = [
        'pengindeks_publikasi',
    ];

    /**
     * Relasi ke Publikasi
     */
    public function publikasi()
    {
        return $this->hasMany(Publikasi::class, 'id_pengindeks_publikasi', 'id_pengindeks_publikasi');
    }
}
