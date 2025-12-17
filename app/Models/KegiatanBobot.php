<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KegiatanBobot extends Model
{
    protected $table = 'KEGIATAN_BOBOT';

    protected $primaryKey = 'ID_KEGIATAN_BOBOT';

    public $timestamps = false;

    protected $fillable = [
        'id_kegiatan_golongan',
        'id_kegiatan_tingkat',
        'id_kegiatan_prestasi',
        'point_kegiatan_bobot',
    ];

    /**
     * Relasi ke KegiatanGolongan
     */
    protected $casts = [
        'point_kegiatan_bobot' => 'integer',
    ];

    public function kegiatanGolongan(): BelongsTo
    {
        return $this->belongsTo(KegiatanGolongan::class, 'id_kegiatan_golongan', 'id_kegiatan_golongan');
    }
    /**
     * Relasi ke KegiatanTingkat
     */
    public function kegiatanTingkat(): BelongsTo
    {
        return $this->belongsTo(KegiatanTingkat::class, 'id_kegiatan_tingkat', 'id_kegiatan_tingkat');
    }

    /**
     * Relasi ke KegiatanPrestasi
     */
    public function kegiatanPrestasi(): BelongsTo
    {
        return $this->belongsTo(KegiatanPrestasi::class, 'id_kegiatan_prestasi', 'id_kegiatan_prestasi');
    }

    /**
     * Relasi ke KegiatanKemahasiswaan
     */
    public function kegiatanKemahasiswaans(): HasMany
    {
        return $this->hasMany(KegiatanKemahasiswaan::class, 'id_kegiatan_bobot', 'id_kegiatan_bobot');
    }
}
