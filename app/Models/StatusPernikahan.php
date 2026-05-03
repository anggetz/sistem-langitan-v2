<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPernikahan extends Model
{
    use HasFactory;
    protected $table = 'status_pernikahan';
    protected $primaryKey = 'id_status_pernikahan';

}
