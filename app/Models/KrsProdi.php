<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KrsProdi extends Model
{
    use HasFactory;
    protected $table = 'krs_prodi';
    protected $primaryKey = 'id_krs_prodi';

    public function kelasMk()
    {
        return $this->belongsTo(KelasMk::class, 'id_kelas_mk', 'id_kelas_mk');
    }
}
