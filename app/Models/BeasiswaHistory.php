<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeasiswaHistory extends Model
{
    use HasFactory;
    protected $table = 'sejarah_beasiswa';
    protected $primaryKey = 'id_sejarah_beasiswa';

    public function Mahasiswa() {
        return $this->belongsTo(Mahasiswa::class, 'id_mhs', 'id_mhs');
    }
}
