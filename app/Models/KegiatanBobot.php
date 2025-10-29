<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanBobot extends Model
{
    protected $table = 'KEGIATAN_BOBOT';

    protected $primaryKey = 'ID_KEGIATAN_BOBOT';

    public $timestamps = false;

    protected $fillable = [
        'ID_KEGIATAN_JENIS',
        'ID_KEGIATAN_TINGKAT',
        'ID_KEGIATAN_PRESTASI',
        'ID_KEGIATAN_DOKUMEN_TYPE',
        'POINT_KEGIATAN_BOBOT',
    ];

    protected $casts = [
        'POINT_KEGIATAN_BOBOT' => 'integer',
    ];

    public function kegiatanJenis()
    {
        return $this->belongsTo(KegiatanJenis::class, 'ID_KEGIATAN_JENIS', 'ID_KEGIATAN_JENIS');
    }

    public function kegiatanTingkat()
    {
        return $this->belongsTo(KegiatanTingkat::class, 'ID_KEGIATAN_TINGKAT', 'ID_KEGIATAN_TINGKAT');
    }

    public function kegiatanPrestasi()
    {
        return $this->belongsTo(KegiatanPrestasi::class, 'ID_KEGIATAN_PRESTASI', 'ID_KEGIATAN_PRESTASI');
    }

    public function kegiatanDokumenType()
    {
        return $this->belongsTo(KegiatanDokumenType::class, 'ID_KEGIATAN_DOKUMEN_TYPE', 'ID_KEGIATAN_DOKUMEN_TYPE');
    }

    public function kegiatanKemahasiswaans()
    {
        return $this->hasMany(KegiatanKemahasiswaan::class, 'ID_KEGIATAN_BOBOT', 'ID_KEGIATAN_BOBOT');
    }
}
