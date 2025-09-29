<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Semester extends Model
{
    use HasFactory;
    protected $table = 'semester';
    protected $primaryKey = 'id_semester';

    public static function aktif()
    {
        $semesterAktif = Cache::get("semester_aktif", null);
        if ($semesterAktif == null ) {
            $semesterAktif = self::where("STATUS_AKTIF_SEMESTER", "True")
                    ->orderBY("ID_SEMESTER", "DESC")->first();
            Cache::put("semester_aktif", $semesterAktif, 30);
        }

        return $semesterAktif;
    }

    public static function prevAktif()
    {
        $semesterAktif = self::aktif();

        $prevCoditionNmSemester = [];
        $prevYear = $semesterAktif->thn_akademik_semester;

        if ($semesterAktif->nm_semester == 'Genap') {
            $prevCoditionNmSemester = ['Ganjil', 'Pendek'];
        } else if ($semesterAktif->nm_semester == 'Ganjil') {
            $prevYear--;
            $prevCoditionNmSemester = ['Genap'];
        }

        // Get the previous semester
        $semesterAktif = self::whereRaw("thn_akademik_semester = '".(int)$prevYear."'")
            ->whereIn("nm_semester", $prevCoditionNmSemester)
            ->where("id_perguruan_tinggi", pt()->id_perguruan_tinggi)
            ->orderBy("ID_SEMESTER", "DESC")
            ->get();

        return $semesterAktif;
    }

    public function scopeSemesterAktif($query)
    {
        return $query->where("STATUS_AKTIF_SEMESTER", "True");
    }

    protected static function booted()
    {
        static::addGlobalScope('byResolvedPT', function (Builder $builder) {
            $builder->where('id_perguruan_tinggi', pt()->id_perguruan_tinggi);
        });
    }
}
