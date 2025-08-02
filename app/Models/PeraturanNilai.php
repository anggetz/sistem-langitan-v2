<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeraturanNilai extends Model
{
    use HasFactory;
    protected $table = 'peraturan_nilai';
    protected $primaryKey = 'id_peraturan_nilai';

    public function standardNilai()
    {
        return $this->belongsTo(StandarNilai::class, 'id_standar_nilai', 'id_standar_nilai');
    }
}
