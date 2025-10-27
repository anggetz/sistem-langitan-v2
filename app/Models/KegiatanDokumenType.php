<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanDokumenType extends Model
{
    protected $table = 'KEGIATAN_DOKUMEN_TYPE';

    protected $primaryKey = 'ID_KEGIATAN_DOKUMEN_TYPE';

    public $timestamps = false;

    protected $fillable = [
        'NM_KEGIATAN_DOKUMEN_TYPE',
    ];

    public function kegiatanBobots()
    {
        return $this->hasMany(KegiatanBobot::class, 'ID_KEGIATAN_DOKUMEN_TYPE', 'ID_KEGIATAN_DOKUMEN_TYPE');
    }
}
