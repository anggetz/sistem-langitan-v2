<?php

namespace App\Models;

use App\Models\Pengguna;
use App\Models\PengampuMk;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use Blameable;
    protected $table = 'dosen';
    protected $primaryKey = 'id_dosen';
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    protected $fillable = [
        'nomor_npwp',
        'no_ktp',
        'alamat_rumah_dosen',
        'kode_pos',
        'tlp_dosen',
        'mobile_dosen',
    ];

    protected function casts(): array
    {
        return [
            'id_pengguna' => 'integer',
        ];
    }

    public function pengampuMk(){
        return $this->hasMany(PengampuMk::class,"id_dosen","id_dosen");
    }

    public function penghargaan(){
        return $this->hasMany(DosenPenghargaan::class,"id_dosen","id_dosen");
    }
    public function penelitian(){
        return $this->hasMany(Penelitian::class,"id_peneliti","id_dosen");
    }

    public function programStudi(){
        return $this->belongsTo(ProgramStudi::class,"id_program_studi","id_program_studi");
    }

    public function departemen(){
        return $this->belongsTo(DosenDepartemen::class,"id_dosen","id_dosen");
    }

    public function pengguna(){
        return $this->belongsTo(Pengguna::class,"id_pengguna","id_pengguna");
    }

    public function namaPengguna(){
        return $this->belongsTo(Pengguna::class,"id_pengguna","id_pengguna")->select(["id_pengguna","nm_pengguna"]);
    }

    public function sejarahGolongan(){
        return $this->hasMany(SejarahGolongan::class,"id_pengguna","id_pengguna")->orderByDesc('id_sejarah_golongan');
    }

    public function sejarahJabatanFungsional(){
        return $this->hasMany(SejarahJabatanFungsional::class,"id_pengguna","id_pengguna")->orderByDesc('id_sejarah_jabatan_fungsional');
    }

    public function sejarahJabatanStruktural() {
        return $this->hasMany(SejarahJabatanStruktural::class,"id_pengguna","id_pengguna")->orderByDesc('id_sejarah_jabatan_struktural');
    }

    public function sejarahPendidikan() {
        return $this->hasMany(SejarahPendidikan::class,"id_pengguna","id_pengguna")->orderByDesc('id_sejarah_pendidikan');
    }

    public function statusPengguna(){
        return $this->belongsTo(StatusPengguna::class,"id_status_pengguna","id_status_pengguna");
    }

}
