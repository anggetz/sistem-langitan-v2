<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;
    protected $table = 'tagihan';
    protected $primaryKey = 'id_tagihan';

    public function pembayaran(){
        return $this->hasMany(Pembayaran::class,"id_tagihan","id_tagihan");
    }

    public function detailBiaya(){
        return $this->belongsTo(DetailBiaya::class,'id_detail_biaya','id_detail_biaya');
    }
}
