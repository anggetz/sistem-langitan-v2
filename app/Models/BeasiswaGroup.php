<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeasiswaGroup extends Model
{
    use HasFactory;
    protected $table = 'group_beasiswa';
    protected $primaryKey = 'id_group_beasiswa';
}
