<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanTingkat extends Model
{
    protected $table = 'KEGIATAN_TINGKAT';

    protected $primaryKey = 'ID_KEGIATAN_TINGKAT';

    public $timestamps = false;

    protected $fillable = [
        'NM_KEGIATAN_TINGKAT',
    ];

    public function kegiatanBobots()
    {
        return $this->hasMany(KegiatanBobot::class, 'ID_KEGIATAN_TINGKAT', 'ID_KEGIATAN_TINGKAT');
    }
}
