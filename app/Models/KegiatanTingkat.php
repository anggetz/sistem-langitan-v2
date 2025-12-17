<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanTingkat extends Model
{
    protected $table = 'kegiatan_tingkat';

    protected $primaryKey = 'id_kegiatan_tingkat';

    public $timestamps = false;

    protected $fillable = [
        'nm_kegiatan_tingkat',
    ];

    public function kegiatanBobots()
    {
        return $this->hasMany(KegiatanBobot::class, 'id_kegiatan_tingkat', 'id_kegiatan_tingkat');
    }
}
