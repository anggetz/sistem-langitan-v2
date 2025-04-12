<?php

namespace App\Models;

use App\Models\Dosen;
use App\Models\KelasMk;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengampuMk extends Model
{
    use HasFactory, BelongsToThrough, Blameable;
    protected $table = 'pengampu_mk';
    protected $primaryKey = 'id_pengampu_mk';
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    protected $guarded = [];

    public function kelas_mk()
    {
        return $this->belongsTo(KelasMk::class, "id_kelas_mk", "id_kelas_mk");
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, "id_dosen", "id_dosen");
    }

    public function pengguna()
    {
        return $this->belongsToThrough(
            Pengguna::class,
            Dosen::class,
            'id_dosen',
            '',
            [Dosen::class => "id_dosen", Pengguna::class => "id_pengguna"]
        );
    }
}
