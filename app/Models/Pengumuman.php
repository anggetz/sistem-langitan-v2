<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;
    protected $table = 'pengumuman';
    public $timestamps = true;
    protected $primaryKey = 'id_pengumuman';

    protected $fillable = [
        'title',
        'waktu',
        'deskripsi',
    ];
}
