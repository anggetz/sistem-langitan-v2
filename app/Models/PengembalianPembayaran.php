<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembalianPembayaran extends Model
{
    use HasFactory;
    protected $table = 'pengembalian_pembayaran';
    protected $primaryKey = 'id_pengembalian_pembayaran';
}
