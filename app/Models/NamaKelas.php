<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NamaKelas extends Model
{
    use HasFactory;
    protected $table = 'nama_kelas';
    protected $primaryKey = 'id_nama_kelas';

    protected static function booted()
    {
        static::addGlobalScope('byResolvedPT', function (Builder $builder) {
            $builder->where('id_perguruan_tinggi', pt()->id_perguruan_tinggi);
        });
    }
}
