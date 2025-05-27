<?php

namespace App\Models;

use App\Models\Pengguna;
use App\Models\PengampuMk;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class Penelitian extends Model
{
    use Blameable;
    protected $table = 'penelitian';
    protected $primaryKey = 'id_penelitian';
    public $timestamps = false;


}
