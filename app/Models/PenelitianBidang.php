<?php

namespace App\Models;

use App\Models\Pengguna;
use App\Models\PengampuMk;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class PenelitianBidang extends Model
{
    protected $table = 'penelitian_bidang';
    protected $primaryKey = 'id_penelitian_bidang';
    public $timestamps = false;
}
