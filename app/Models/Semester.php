<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Semester extends Model
{
    use HasFactory;
    protected $table = 'semester';
    protected $primaryKey = 'id_semester';

    public static function aktif()
    {
        return self::where("STATUS_AKTIF_SEMESTER", "True")
            ->orderBY("ID_SEMESTER", "DESC")->first();
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
