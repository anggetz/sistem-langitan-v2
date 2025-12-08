<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublikasiJurnal extends Model
{
    use HasFactory;
    protected $table = 'PUBLIKASI_JURNAL';
    protected $primaryKey = 'ID_PUBLIKASI_JURNAL';

    public function Authors() {
        return $this->hasMany(PublikasiJurnalAuthor::class, 'id_publikasi_jurnal', 'id_publikasi_jurnal');
    }
}
