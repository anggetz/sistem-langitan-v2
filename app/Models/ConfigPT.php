<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfigPT extends Model
{
    use HasFactory;
    protected $table = 'config_pt';
    protected $primaryKey = 'KD_CONFIG';

    // primary key type
    protected $keyType = 'string';

    // timestamps
    public $timestamps = false;
    //guarded
    protected $guarded = [];


}
