<?php

namespace App\Models;

use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Model;

class LogApi extends Model
{
    protected $table = 'log_api';
    protected $primaryKey = 'token';
    public $incrementing = false;
    public $timestamps = false;

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'ID_PENGGUNA', 'ID_PENGGUNA');
    }
}
