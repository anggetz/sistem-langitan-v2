<?php

namespace App\Models;

use App\Models\Semester;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TagihanMhs extends Model
{
    use HasFactory,Blameable;
    protected $table = 'tagihan_mhs';
    protected $primaryKey = 'id_tagihan_mhs';
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    protected $guarded = [];

    public function tagihan(){
        return $this->hasMany(Tagihan::class,"id_tagihan_mhs","id_tagihan_mhs");
    }

    public function semester(){
        return $this->belongsTo(Semester::class,"id_semester","id_semester");
    }

    public function pengembalianPembayaran(){
        return $this->hasMany(PengembalianPembayaran::class,"id_tagihan_mhs","id_tagihan_mhs");
    }
}
