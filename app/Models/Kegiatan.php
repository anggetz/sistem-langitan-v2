<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kegiatan extends Model
{
    use HasFactory;
    protected $table = 'kegiatan';
    protected $primaryKey = 'id_kegiatan';

    public function perguruanTinggi(){
        return $this->belongsTo(PerguruanTinggi::class,'id_perguruan_tinggi','id_perguruan_tinggi');
    }

    public function ujianJadwal(){
        return $this->hasMany(UjianMk::class,'id_kegiatan','id_kegiatan');
    }

    protected static function booted()
    {
        static::addGlobalScope('byResolvedPT', function (Builder $builder) {
            $builder->where('id_perguruan_tinggi', pt()->id_perguruan_tinggi);
        });
    }
}
