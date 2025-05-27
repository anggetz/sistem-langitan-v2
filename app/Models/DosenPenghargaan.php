<?php

namespace App\Models;

use App\Models\Pengguna;
use App\Models\PengampuMk;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class DosenPenghargaan extends Model
{
    protected $table = 'dosen_penghargaan';
    protected $primaryKey = 'id_dosen_penghargaan';
    public $timestamps = false;
}
