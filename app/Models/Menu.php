<?php

namespace App\Models;

use App\Models\Modul;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    public $timestamps = false;

    // belongsTo modul
    public function modul()
    {
        return $this->belongsTo(Modul::class, 'id_modul', 'id_modul');
    }

    // scope active
    public function scopeAktif($query)
    {
        return $query->where('akses', 1);
    }

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
