<?php

namespace App\Models;

use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SessionPengguna extends Model
{
    use HasFactory;
    protected $table = 'session_pengguna';
    protected $primaryKey = 'id_session';
    // key type
    protected $keyType = 'string';

    // pengguna
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}
