<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportasi extends Model
{
    use HasFactory;
    protected $table = 'jenis_transportasi';
    protected $primaryKey = 'id_jenis_transportasi';
}
