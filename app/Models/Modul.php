<?php

namespace App\Models;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Modul extends Model
{
    use HasFactory;
    protected $table = 'modul';
    protected $primaryKey = 'id_modul';
    public $timestamps = false;


    // belongsTo role
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    // hasMany menu
    public function menu()
    {
        return $this->hasMany(Menu::class, 'id_modul', 'id_modul');
    }

    // menu aktif
    public function menuV2Aktif()
    {
        return $this->menu()->v2()->aktif();
    }

    // scope active
    public function scopeAktif($query)
    {
        return $query->where('akses', 1);
    }

    // scope v2
    public function scopeV2($query)
    {
        return $query->where('is_v2', 1);
    }

    // global scope urutan
    protected static function booted()
    {
        static::addGlobalScope('urutan', function ($builder) {
            $builder->orderBy('urutan', 'asc');
        });
    }
}
