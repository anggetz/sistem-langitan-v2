<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKegiatanSemester extends Model
{
    use HasFactory;
    protected $table = 'jadwal_kegiatan_semester';
    protected $primaryKey = 'id_jadwal_kegiatan_semester';

    public function kegiatan(){
        return $this->belongsTo(Kegiatan::class, 'id_kegiatan', 'id_kegiatan');
    }
}
