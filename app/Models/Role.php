<?php

namespace App\Models;

use App\Models\Modul;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    protected $table = 'role';
    protected $primaryKey = 'id_role';

    const MAHASISWA = 3;
    const DOSEN = 4;

    // hasMany modul
    public function modul()
    {
        return $this->hasMany(Modul::class, 'id_role', 'id_role');
    }

    // scope v2
    public function scopeV2($query)
    {
        return $query->where('is_v2', 1);
    }
}
