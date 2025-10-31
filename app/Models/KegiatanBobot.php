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
        'id_kegiatan_jenis',
        'id_kegiatan_tingkat',
        'id_kegiatan_prestasi',
        'id_kegiatan_dokumen_type',
        'point_kegiatan_bobot',
    ];

    protected $casts = [
        'point_kegiatan_bobot' => 'integer',
    ];

    /**
     * Relasi ke KegiatanJenis
     */
    public function kegiatanJenis(): BelongsTo
    {
        return $this->belongsTo(KegiatanJenis::class, 'id_kegiatan_jenis', 'id_kegiatan_jenis');
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
     * Relasi ke KegiatanDokumenType
     */
    public function kegiatanDokumenType(): BelongsTo
    {
        return $this->belongsTo(KegiatanDokumenType::class, 'id_kegiatan_dokumen_type', 'id_kegiatan_dokumen_type');
    }

    /**
     * Relasi ke KegiatanKemahasiswaan
     */
    public function kegiatanKemahasiswaans(): HasMany
    {
        return $this->hasMany(KegiatanKemahasiswaan::class, 'id_kegiatan_bobot', 'id_kegiatan_bobot');
    }
}
