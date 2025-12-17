<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanGolongan extends Model
{
    /** @use HasFactory<\Database\Factories\KegiatanGolonganFactory> */
    use HasFactory;

    protected $table = 'kegiatan_golongan';

    protected $primaryKey = 'id_kegiatan_golongan';

    public $timestamps = false;

    protected $fillable = [
        'nm_kegiatan_golongan',
        'id_kegiatan_dokumen_type',
        'id_kegiatan_jenis',
    ];

    public function kegiatanDokumenType()
    {
        return $this->belongsTo(KegiatanDokumenType::class, 'id_kegiatan_dokumen_type', 'id_kegiatan_dokumen_type');
    }

    public function kegiatanJenis()
    {
        return $this->belongsTo(KegiatanJenis::class, 'id_kegiatan_jenis', 'id_kegiatan_jenis');
    }

    public function kegiatanBobots()
    {
        return $this->hasMany(KegiatanBobot::class, 'id_kegiatan_golongan', 'id_kegiatan_golongan');
    }
}
