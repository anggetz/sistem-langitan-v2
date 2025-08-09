<?php

namespace App\Models;

use App\Models\NamaKelas;
use App\Traits\Blameable;
use App\Models\MataKuliah;
use App\Models\PengampuMk;
use App\Models\KurikulumMk;
use App\Models\PengambilanMk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KelasMk extends Model
{
    use HasFactory, Blameable;
    protected $table = 'kelas_mk';
    protected $primaryKey = 'id_kelas_mk';
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';
    //guarded
    protected $guarded = [];

    public function pengambilanMk(){
        return $this->hasMany(PengambilanMk::class,"id_kelas_mk","id_kelas_mk");
    }

    public function nama(){
        return $this->belongsTo(NamaKelas::class,"no_kelas_mk","id_nama_kelas");
    }

    public function programStudi(){
        return $this->belongsTo(ProgramStudi::class,"id_program_studi","id_program_studi");
    }

    function pengambilanMkKprs(){
        return $this->hasMany(PengambilanMkKprs::class,"id_kelas_mk","id_kelas_mk")
            ->where('pengambilan_mk_kprs.id_mhs', '=', $this->id_mhs);
    }

    public function semester(){
        return $this->belongsTo(Semester::class,"id_semester","id_semester");
    }

    public function pengampuMk(){
        return $this->hasMany(PengampuMk::class,"id_kelas_mk","id_kelas_mk");
    }

    public function kurikulumMk(){
        return $this->belongsTo(KurikulumMk::class,"id_kurikulum_mk","id_kurikulum_mk");
    }

    public function mataKuliah(){
        return $this->belongsTo(MataKuliah::class,"id_mata_kuliah","id_mata_kuliah");
    }

    public function jadwalKelas(){
        return $this->hasMany(JadwalKelas::class,"id_kelas_mk","id_kelas_mk");
    }

    public function listMahasiswa(){
        return $this->hasMany(PengambilanMk::class,"id_kelas_mk","id_kelas_mk");
    }

    public function presensiKelas(){
        return $this->hasMany(PresensiKelas::class,'id_kelas_mk','id_kelas_mk');
    }

    public function presensiMhs(){
        return $this->hasManyThrough(PresensiMhs::class,PresensiKelas::class,'id_kelas_mk','id_presensi_kelas','id_kelas_mk','id_presensi_kelas');
    }

}
