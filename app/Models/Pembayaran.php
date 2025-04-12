<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';

    public function bank(){
        return $this->belongsTo(Bank::class,'id_bank','id_bank');
    }

    public function bankVia(){
        return $this->belongsTo(Bank::class,'id_bank_via','id_bank');
    }

    public function tagihan(){
        return $this->belongsTo(Tagihan::class,'id_tagihan','id_tagihan');
    }
}
