<?php

namespace App\Models;

use App\Models\Pengguna;
use App\Models\PengampuMk;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use Blameable;
    protected $table = 'dosen';
    protected $primaryKey = 'id_dosen';
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    public function pengampuMk(){
        return $this->hasMany(PengampuMk::class,"id_dosen","id_dosen");
    }

    public function penghargaan(){
        return $this->hasMany(DosenPenghargaan::class,"id_dosen","id_dosen");
    }

    public function penelitian(){
        return $this->hasMany(Penelitian::class,"id_peneliti","id_dosen");
    }

    public function programStudi(){
        return $this->belongsTo(ProgramStudi::class,"id_program_studi","id_program_studi");
    }

    public function departemen(){
        return $this->belongsTo(DosenDepartemen::class,"id_dosen","id_dosen");
    }

    public function pengguna(){
        return $this->belongsTo(Pengguna::class,"id_pengguna","id_pengguna");
    }

    public function namaPengguna(){
        return $this->belongsTo(Pengguna::class,"id_pengguna","id_pengguna")->select(["id_pengguna","nm_pengguna"]);
    }

}
