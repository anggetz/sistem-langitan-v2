<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanJenis extends Model
{
    use HasFactory;

    protected $table = 'kegiatan_jenis';

    protected $primaryKey = 'id_kegiatan_jenis';

    public $timestamps = false;

    protected $fillable = [
        'nm_kegiatan_jenis',
        'is_have_tingkat',
    ];

    protected $casts = [
        'is_have_tingkat' => 'boolean',
    ];

    public function kegiatanBobots()
    {
        return $this->hasMany(KegiatanBobot::class, 'id_kegiatan_jenis', 'id_kegiatan_jenis');
    }
}
