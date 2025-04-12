<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use HasFactory;
    protected $table = 'provinsi';
    protected $primaryKey = 'id_provinsi';
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_provinsi', 'lahir_prop_mhs');
    }
}
