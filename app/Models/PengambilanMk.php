<?php

namespace App\Models;

use App\Models\KelasMk;
use App\Models\Semester;
use App\Models\Mahasiswa;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Znck\Eloquent\Traits\BelongsToThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengambilanMk extends Model
{
    use HasFactory, BelongsToThrough, Blameable;

    protected $table = 'pengambilan_mk';
    protected $primaryKey = 'id_pengambilan_mk';
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    //guarded
    protected $guarded = [];

    public function scopeSemesterAktif($query)
    {
        $idSemester = Semester::aktif();
        return $query->where("id_semester", $idSemester->id_semester);
    }

    public function scopeWhereSemester($query, $idSemester)
    {
        return $query->whereHas(
            "semester",
            function (Builder $builder) use ($idSemester) {
                $builder->where("id_semester", $idSemester);
            }
        );
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, "id_semester", "id_semester");
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, "id_mhs", "id_mhs");
    }

    public function kelasMk()
    {
        return $this->belongsTo(KelasMk::class, "id_kelas_mk", "id_kelas_mk");
    }

    public function activePresensiKelas()
    {
        return $this->belongsTo(PresensiKelas::class, "id_kelas_mk", "id_kelas_mk")
             ->whereRaw("tgl_entry >= SYSDATE - (5 / 1440)");
    }

    public function namaKelas()
    {
        return $this->belongsToThrough(
            NamaKelas::class,
            KelasMk::class,
            'id_kelas_mk',
            '',
            [KelasMk::class => "id_kelas_mk", NamaKelas::class => "no_kelas_mk"]
        );
    }

    public function mataKuliah()
    {
        return $this->belongsToThrough(
            MataKuliah::class,
            KelasMk::class,
            'id_kelas_mk',
            '',
            [KelasMk::class => "id_kelas_mk", MataKuliah::class => "id_mata_kuliah"]
        );
    }
}
