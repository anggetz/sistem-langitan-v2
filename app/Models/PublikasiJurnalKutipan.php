<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublikasiJurnalKutipan extends Model
{
    use HasFactory;
    protected $table = 'PUBLIKASI_JURNAL_KUTIPAN';
    protected $primaryKey = 'ID_PUBLIKASI_JURNAL_KUTIPAN';
}
