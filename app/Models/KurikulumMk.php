<?php

namespace App\Models;

use App\Models\KelasMk;
use App\Traits\Blameable;
use App\Models\MataKuliah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KurikulumMk extends Model
{
    use HasFactory, Blameable;
    protected $table = 'kurikulum_mk';
    protected $primaryKey = 'id_kurikulum_mk';
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';
    //guarded
    protected $guarded = [];

    public function kelasMk(){
        return $this->hasMany(KelasMk::class,"id_kurikulum_mk","id_kurikulum_mk");
    }

    public function mataKuliah(){
        return $this->belongsTo(MataKuliah::class,"id_mata_kuliah","id_mata_kuliah");
    }

}
