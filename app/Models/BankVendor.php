<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankVendor extends Model
{
    protected $table = 'bank_vendor';
    protected $primaryKey = 'id_bank_vendor';
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';
    //guarded
    protected $guarded = [];
}
