<?php

namespace App\Models;

use App\Models\Pengguna;
use App\Models\PengampuMk;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class PenelitianBidangIlmu extends Model
{
    protected $table = 'penelitian_bidang_ilmu';
    protected $primaryKey = 'id_penelitian_bidang_ilmu';
    public $timestamps = false;
}
