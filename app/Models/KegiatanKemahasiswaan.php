<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanKemahasiswaan extends Model
{
    protected $table = 'kegiatan_kemahasiswaan';

    protected $primaryKey = 'id_kegiatan_kemahasiswaan';

    public $timestamps = false;

    protected $fillable = [
        'id_kegiatan_bobot',
        'id_mhs',
        'tgl_kegiatan',
        'nm_kegiatan',
        'deskripsi_kegiatan',
        'dokumen_kegiatan_path',
        'point_kegiatan',
        'is_approved',
        'approved_at',
        'approved_by',
        'is_rejected',
        'rejected_at',
        'rejected_by',
        'rejected_message',
    ];

    protected $casts = [
        'tgl_kegiatan' => 'date',
        'point_kegiatan' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'is_rejected' => 'boolean',
        'rejected_at' => 'datetime',
    ];

    public function kegiatanBobot()
    {
        return $this->belongsTo(KegiatanBobot::class, 'id_kegiatan_bobot', 'id_kegiatan_bobot');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mhs', 'id_mhs');
    }

    public function approver()
    {
        return $this->belongsTo(Pengguna::class, 'approved_by', 'id_pengguna');
    }
}
