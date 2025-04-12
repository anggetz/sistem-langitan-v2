<?php

namespace App\Models;

use App\Models\KelasMk;
use App\Traits\Blameable;
use App\Models\MataKuliah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kurikulum extends Model
{
    use HasFactory, Blameable;
    protected $table = 'kurikulum';
    protected $primaryKey = 'id_kurikulum';
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    //guarded
    protected $guarded = [];

}
