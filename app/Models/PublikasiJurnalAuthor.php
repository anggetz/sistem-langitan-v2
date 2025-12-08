<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublikasiJurnalAuthor extends Model
{
    use HasFactory;
    protected $table = 'PUBLIKASI_JURNAL_AUTHOR';
    protected $primaryKey = 'ID_PUBLIKASI_JURNAL_AUTHOR';
}
