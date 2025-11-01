<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanPrestasi extends Model
{
    protected $table = 'kegiatan_prestasi';

    protected $primaryKey = 'id_kegiatan_prestasi';

    public $timestamps = false;

    protected $fillable = [
        'nm_kegiatan_prestasi',
    ];

    public function kegiatanBobots()
    {
        return $this->hasMany(KegiatanBobot::class, 'id_kegiatan_prestasi', 'id_kegiatan_prestasi');
    }
}
