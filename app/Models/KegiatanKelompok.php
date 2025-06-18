<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanKelompok extends Model
{
    use HasFactory;
    protected $timestamp = false;
    protected $primaryKey = 'id_kelompok_kegiatan';

    protected $fillable = [
        'nm_kelompok_kegiatan',
        'is_kemahasiswaan',
        'is_akademik',
    ];

    protected $table = 'kelompok_kegiatan';
}
