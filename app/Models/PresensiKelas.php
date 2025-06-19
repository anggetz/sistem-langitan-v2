<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiKelas extends Model
{
    use HasFactory;
    protected $table = 'presensi_kelas';
    protected $primaryKey = 'id_presensi_kelas';
    public $timestamps = false;

    protected $fillable = [
        'id_kelas_mk',
        'tgl_entry',
        'waktu_mulai',
        'waktu_selesai',
        'id_materi_mk',
        'tgl_presensi_kelas'
    ];

    public function kelasMk()
    {
        return $this->belongsTo(KelasMk::class, 'id_kelas_mk', 'id_kelas_mk');
    }

    public function presensiMhs()
    {
        return $this->hasMany(PresensiMhs::class, 'id_presensi_kelas', 'id_presensi_kelas');
    }
}
