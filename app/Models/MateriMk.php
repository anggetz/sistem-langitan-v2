<?php

namespace App\Models;

use App\Traits\Blameable;
use App\Models\KurikulumMk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MateriMk extends Model
{
    use HasFactory;
    protected $table = 'materi_mk';
    protected $primaryKey = 'id_materi_mk';
    public $timestamps = false;

    protected $fillable = [
        'isi_materi_mk',
        'id_kelas_mk',
        'tgl_materi_mk'
    ];
}
