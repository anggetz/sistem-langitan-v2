<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;
    protected $table = 'ruangan';
    protected $primaryKey = 'id_ruangan';

    public function gedung(){
        return $this->belongsTo(Gedung::class,"id_gedung","id_gedung");
    }
}
