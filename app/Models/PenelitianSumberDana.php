<?php

namespace App\Models;

use App\Models\Pengguna;
use App\Models\PengampuMk;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class PenelitianSumberDana extends Model
{
    protected $table = 'penelitian_sumber_dana';
    protected $primaryKey = 'id_penelitian_sumber_dana';
    public $timestamps = false;
}
