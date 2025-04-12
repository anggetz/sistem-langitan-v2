<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBiaya extends Model
{
    use HasFactory;
    protected $table = 'detail_biaya';
    protected $primaryKey = 'id_detail_biaya';

    public function biaya(){
        return $this->belongsTo(Biaya::class,'id_biaya','id_biaya');
    }
}
