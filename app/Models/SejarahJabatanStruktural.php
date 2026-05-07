<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SejarahJabatanStruktural extends Model
{
    use HasFactory;
    protected $table = 'sejarah_jabatan_struktural';
    protected $primaryKey = 'id_sejarah_jabatan_struktural';

    public function jabatanStruktural() {
        return $this->belongsTo(JabatanStruktural::class, 'id_jabatan_struktural', 'id_jabatan_struktural');
    }
}
