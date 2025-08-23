<?php

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahasiswaStatus extends Model
{
    use HasFactory,Blameable;
    protected $table = 'mahasiswa_status';
    protected $primaryKey = 'id_mhs_status';
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';


    // guarded
    protected $guarded = [];

    public function semester(){
        return $this->belongsTo(Semester::class,'id_semester','id_semester');
    }

     protected function casts(): array
    {
        return [
            'ips' => 'float',
            'ipk' => 'float',
        ];
    }
}
