<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SejarahJabatanFungsional extends Model
{
    use HasFactory;
    protected $table = 'sejarah_jabatan_fungsional';
    protected $primaryKey = 'id_sejarah_jabatan_fungsional';

    public function jabatanFungsional() {
        return $this->belongsTo(JabatanFungsional::class, 'id_jabatan_fungsional', 'id_jabatan_fungsional');
    }
}
