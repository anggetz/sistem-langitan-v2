<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomponenMk extends Model
{
    use HasFactory;
    protected $table = 'komponen_mk';
    protected $primaryKey = 'id_komponen_mk';
    public $timestamps = false;

     protected $fillable = [
        'id_kelas_mk',
        'nm_komponen_mk',
        'persentase_komponen_mk',
        'urutan_komponen_mk'
    ];

}
