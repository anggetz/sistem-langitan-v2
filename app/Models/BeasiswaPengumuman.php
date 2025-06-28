<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeasiswaPengumuman extends Model
{
    use HasFactory;
    protected $table = 'pengumuman_beasiswa';
    protected $primaryKey = 'id_pengumuman';

}
