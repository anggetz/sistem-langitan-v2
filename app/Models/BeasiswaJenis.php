<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeasiswaJenis extends Model
{
    use HasFactory;
    protected $table = 'jenis_beasiswa';
    protected $primaryKey = 'id_jenis_beasiswa';
}
