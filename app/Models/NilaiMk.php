<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiMk extends Model
{
    use HasFactory;
    protected $table = 'nilai_mk';
    protected $primaryKey = 'id_nilai_mk';
    public $timestamps = false;

    public function pengambilanMk()
    {
        return $this->belongsTo(PengambilanMk::class, 'id_pengambilan_mk', 'id_pengambilan_mk');
    }

    public function komponenMk()
    {
        return $this->belongsTo(KomponenMk::class, 'id_komponen_mk', 'id_komponen_mk');
    }
}
