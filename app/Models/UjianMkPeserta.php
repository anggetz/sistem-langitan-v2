<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianMkPeserta extends Model
{
    use HasFactory;
    protected $table = 'ujian_mk_peserta';
    protected $primaryKey = 'id_ujian_mk_peserta';


    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mhs', 'id_mhs');
    }

    public function ujianJadwal()
    {
        return $this->belongsTo(UjianMk::class, 'id_ujian_mk', 'id_ujian_mk');
    }
}
