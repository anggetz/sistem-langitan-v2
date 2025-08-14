<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Config extends Model
{
    use HasFactory;
    protected $table = 'config';
    protected $primaryKey = 'kd_konfig';

    // primary key type
    protected $keyType = 'string';

    // timestamps
    public $timestamps = false;
    //guarded
    protected $guarded = [];


}
