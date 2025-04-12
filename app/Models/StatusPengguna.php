<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPengguna extends Model
{
    use HasFactory;
    protected $table = 'status_pengguna';
    protected $primaryKey = 'id_status_pengguna';
}
