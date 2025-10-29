<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanJenis extends Model
{
    use HasFactory;

    protected $table = 'KEGIATAN_JENIS';

    protected $primaryKey = 'ID_KEGIATAN_JENIS';

    public $timestamps = false;

    protected $fillable = [
        'NM_KEGIATAN_JENIS',
        'IS_HAVE_TINGKAT',
    ];

    protected $casts = [
        'IS_HAVE_TINGKAT' => 'boolean',
    ];

    public function kegiatanBobots()
    {
        return $this->hasMany(KegiatanBobot::class, 'ID_KEGIATAN_JENIS', 'ID_KEGIATAN_JENIS');
    }
}
