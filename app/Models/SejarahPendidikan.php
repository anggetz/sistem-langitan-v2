<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SejarahPendidikan extends Model
{
    use HasFactory;
    protected $table = 'sejarah_pendidikan';
    protected $primaryKey = 'id_sejarah_pendidikan';

    protected $fillable = [
        'id_pengguna',
        'id_pendidikan_akhir',
    ];

    public $timestamps = false;

    public function pendidikanAkhir() {
        return $this->belongsTo(PendidikanAkhir::class, 'id_pendidikan_akhir', 'id_pendidikan_akhir');
    }
}
