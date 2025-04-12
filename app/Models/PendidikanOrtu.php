<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendidikanOrtu extends Model
{
    use HasFactory;
    protected $table = 'pendidikan_ortu';
    protected $primaryKey = 'id_pendidikan_ortu';

    // public function mahasiswa()
    // {
    //     return $this->belongsTo(Mahasiswa::class, 'id_kota', 'lahir_kota_mhs');
    // }
}
