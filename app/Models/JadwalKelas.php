<?php

namespace App\Models;

use App\Models\Ruangan;
use App\Models\JadwalJam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalKelas extends Model
{
    use HasFactory;
    protected $table = 'jadwal_kelas';
    protected $primaryKey = 'id_jadwal_kelas';

    public function jadwalJam(){
        return $this->belongsTo(JadwalJam::class,"id_jadwal_jam","id_jadwal_jam");
    }

    public function ruangan(){
        return $this->belongsTo(Ruangan::class,"id_ruangan","id_ruangan");
    }

    public function getNamaHariAttribute(){
        $hari="";
        switch($this->id_jadwal_hari){
            case 1:
                $hari="Minggu";
                break;
            case 2:
                $hari="Senin";
                break;
            case 3:
                $hari="Selasa";
                break;
            case 4:
                $hari="Rabu";
                break;
            case 5:
                $hari="Kamis";
                break;
            case 6:
                $hari="Jumat";
                break;
            case 7:
                $hari="Sabtu";
                break;
        }
        return $hari;
    }
}
