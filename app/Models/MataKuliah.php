<?php

namespace App\Models;

use App\Traits\Blameable;
use App\Models\KurikulumMk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MataKuliah extends Model
{
    use HasFactory, Blameable;
    protected $table = 'mata_kuliah';
    protected $primaryKey = 'id_mata_kuliah';
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    //guarded
    protected $guarded = [];

    public function kurikulumMk(){
        return $this->hasMany(KurikulumMk::class,"id_mata_kuliah","id_mata_kuliah");
    }
}
