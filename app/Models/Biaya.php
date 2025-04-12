<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Biaya extends Model
{
    use HasFactory;
    protected $table = 'biaya';
    protected $primaryKey = 'id_biaya';
    //guarded
    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope('byResolvedPT', function (Builder $builder) {
            $builder->where('id_perguruan_tinggi', pt()->id_perguruan_tinggi);
        });
    }
}
