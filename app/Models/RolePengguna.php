<?php

namespace App\Models;

use App\Models\Modul;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RolePengguna extends Model
{
    use HasFactory;
    protected $table = 'role_pengguna';
    protected $primaryKey = 'id_role_pengguna';

    // scope v2
    public function scopeV2($query)
    {
        return $query->where('is_v2', 1);
    }
}
