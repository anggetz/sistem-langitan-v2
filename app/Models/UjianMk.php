<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianMk extends Model
{
    use HasFactory;
    protected $table = 'ujian_mk';
    protected $primaryKey = 'id_ujian_mk';

    public function kegiatan(){
        return $this->belongsTo(Kegiatan::class,'id_kegiatan','id_kegiatan');
    }

    public function semester(){
        return $this->belongsTo(Semester::class,'id_semester','id_semester');
    }

    public function mahasiswa(){
        return $this->hasManyThrough(Mahasiswa::class,UjianMkPeserta::class,'id_ujian_mk','id_mhs','id_ujian_mk','id_mhs');
    }

    public function kelas(){
        return $this->belongsTo(KelasMk::class,'id_kelas_mk','id_kelas_mk');
    }

}
