<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiMhs extends Model
{
    use HasFactory;
    protected $table = 'presensi_mkmhs';
    protected $primaryKey = 'id_presensi_mkmhs';
    public $timestamps = false;

     protected $fillable = [
        'id_mhs',
        'id_presensi_kelas',
        'kehadiran',
        'qr_flag',
    ];


    public function mahasiswa(){
        return $this->belongsTo(Mahasiswa::class,'id_mhs','id_mhs');
    }

    public function presensiKelas(){
        return $this->belongsTo(PresensiKelas::class,'id_presensi_kelas','id_presensi_kelas');
    }

}
