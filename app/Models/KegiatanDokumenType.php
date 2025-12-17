<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanDokumenType extends Model
{
    protected $table = 'kegiatan_dokumen_type';

    protected $primaryKey = 'id_kegiatan_dokumen_type';

    public $timestamps = false;

    protected $fillable = [
        'nm_kegiatan_dokumen_type',
    ];

    public function kegiatanBobots()
    {
        return $this->hasMany(KegiatanBobot::class, 'id_kegiatan_dokumen_type', 'id_kegiatan_dokumen_type');
    }
}
