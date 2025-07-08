<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akreditas extends Model
{
    use HasFactory;
    protected $table = 'prodi_akreditasi';
    protected $primaryKey = 'id_prodi_akreditasi';

    public function ProgramStudi() {
        return $this->belongsTo(ProgramStudi::class, 'id_program_studi', 'id_program_studi');
    }
}
