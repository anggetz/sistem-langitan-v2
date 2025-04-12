<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumberBiaya extends Model
{
    use HasFactory;
    protected $table = 'sumber_biaya';
    protected $primaryKey = 'id_sumber_biaya';
}
