<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandarNilai extends Model
{
    use HasFactory;
    protected $table = 'standar_nilai';
    protected $primaryKey = 'id_standar_nilai';
}
