<?php

namespace App\Models;

use App\Models\Jenjang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgramStudi extends Model
{
    use HasFactory;
    protected $table = 'program_studi';
    protected $primaryKey = 'id_program_studi';

    public function jenjang(){
        return $this->belongsTo(Jenjang::class,"id_jenjang","id_jenjang");
    }

    public function getJenjangProgramStudiAttribute(){
        return $this->jenjang->nm_jenjang . " " .$this->nm_program_studi;
    }

    public function fakultas() {
        return $this->belongsTo(Fakultas::class, 'id_fakultas', 'id_fakultas');
    }

}
