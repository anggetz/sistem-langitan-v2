<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalJam extends Model
{
    use HasFactory;
    protected $table = 'jadwal_jam';
    protected $primaryKey = 'id_jadwal_jam';
}
