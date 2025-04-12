<?php

namespace App\Models;

use App\Models\ProgramStudi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fakultas extends Model
{
    use HasFactory;
    protected $table = 'fakultas';
    protected $primaryKey = 'id_fakultas';

    public function programStudi(){
        return $this->hasMany(ProgramStudi::class, 'id_fakultas', 'id_fakultas');
    }


    protected static function booted()
    {
        static::addGlobalScope('byResolvedPT', function (Builder $builder) {
            $builder->where('id_perguruan_tinggi', pt()->id_perguruan_tinggi);
        });
    }

}
