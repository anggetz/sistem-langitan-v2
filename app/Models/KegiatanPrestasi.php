<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanPrestasi extends Model
{
    protected $table = 'KEGIATAN_PRESTASI';

    protected $primaryKey = 'ID_KEGIATAN_PRESTASI';

    public $timestamps = false;

    protected $fillable = [
        'NM_KEGIATAN_PRESTASI',
    ];

    public function kegiatanBobots()
    {
        return $this->hasMany(KegiatanBobot::class, 'ID_KEGIATAN_PRESTASI', 'ID_KEGIATAN_PRESTASI');
    }
}
