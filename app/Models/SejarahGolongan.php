<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SejarahGolongan extends Model
{
    use HasFactory;
    protected $table = 'sejarah_golongan';
    protected $primaryKey = 'id_sejarah_golongan';

    public function golongan(){
        return $this->belongsTo(Golongan::class, "id_golongan", "id_golongan");
    }
}
