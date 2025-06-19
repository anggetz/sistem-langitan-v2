<?php

namespace App\Models;

use App\Traits\Blameable;
use App\Models\KurikulumMk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MateriMk extends Model
{
    use HasFactory, Blameable;
    protected $table = 'materi_mk';
    protected $primaryKey = 'id_materi_mk';
}
