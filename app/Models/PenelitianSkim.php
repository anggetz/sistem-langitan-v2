<?php

namespace App\Models;

use App\Models\Pengguna;
use App\Models\PengampuMk;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class PenelitianSkim extends Model
{
    protected $table = 'penelitian_skim';
    protected $primaryKey = 'id_penelitian_skim';
    public $timestamps = false;
}
