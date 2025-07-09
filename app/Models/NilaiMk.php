<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiMk extends Model
{
    use HasFactory;
    protected $table = 'nilai_mk';
    protected $primaryKey = 'id_nilai_mk';
}
