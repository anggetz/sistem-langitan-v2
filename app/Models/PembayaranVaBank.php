<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranVaBank extends Model
{
    use HasFactory;
    protected $table = 'pembayaran_va_bank';
    protected $primaryKey = 'id_pembayaran_va';
}
