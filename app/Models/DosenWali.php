<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DosenWali extends Model
{
    protected $table = 'dosen_wali';
    protected $primaryKey = 'id_dosen_wali';
    //guarded
    protected $guarded = [];

    public function dosen(){
        return $this->belongsTo(Dosen::class,'id_dosen','id_dosen');
    }

    public function mahasiwa(){
        return $this->belongsTo(Mahasiswa::class,'id_mhs','id_mhs');
    }

    public function semester(){
        return $this->belongsTo(Semester::class,'id_semester','id_semester');
    }
}
