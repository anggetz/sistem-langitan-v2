<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beasiswa extends Model
{
    use HasFactory;
    protected $table = 'beasiswa';
    protected $primaryKey = 'id_beasiswa';

    public function GroupBeasiswa() {
        return $this->belongsTo(BeasiswaGroup::class, 'id_group_beasiswa', 'id_group_beasiswa');
    }

    public function JenisBeasiswa() {
        return $this->belongsTo(BeasiswaJenis::class, 'id_jenis_beasiswa', 'id_jenis_beasiswa');
    }

    public function PengumumanBeasiswa() {
        return $this->hasMany(BeasiswaPengumuman::class, 'id_beasiswa', 'id_beasiswa');
    }
}
