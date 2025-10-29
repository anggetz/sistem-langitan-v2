<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanKemahasiswaan extends Model
{
    protected $table = 'KEGIATAN_KEMAHASISWAAN';

    protected $primaryKey = 'ID_KEGIATAN_KEMAHASISWAAN';

    public $timestamps = false;

    protected $fillable = [
        'ID_KEGIATAN_BOBOT',
        'ID_MHS',
        'TGL_KEGIATAN',
        'NM_KEGIATAN',
        'DESKRIPSI_KEGIATAN',
        'DOKUMEN_KEGIATAN_PATH',
        'POINT_KEGIATAN',
        'IS_APPROVED',
        'APPROVED_AT',
        'APPROVED_BY',
    ];

    protected $casts = [
        'TGL_KEGIATAN' => 'date',
        'POINT_KEGIATAN' => 'integer',
        'IS_APPROVED' => 'boolean',
        'APPROVED_AT' => 'datetime',
    ];

    public function kegiatanBobot()
    {
        return $this->belongsTo(KegiatanBobot::class, 'ID_KEGIATAN_BOBOT', 'ID_KEGIATAN_BOBOT');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'ID_MHS', 'ID_MHS');
    }

    public function approver()
    {
        return $this->belongsTo(Pengguna::class, 'APPROVED_BY', 'ID_PENGGUNA');
    }
}
