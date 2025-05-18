<?php

namespace App\Models;

use App\Models\Pengguna;
use App\Models\PengampuMk;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class DosenDepartemen extends Model
{
    protected $table = 'dosen_departemen';
    protected $primaryKey = 'id_dosen_departemen';
    public $timestamps = false;

     public function departemen(){
        return $this->belongsTo(Departemen::class,"id_departemen","id_departemen");
    }

}
