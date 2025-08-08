<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahasiswaKrsApprovalSign extends Model
{
    use HasFactory;
    protected $table = 'mahasiswa_krs_approval_sign';
    protected $primaryKey = 'id_mahasiswa_krs_approval_sign';

    protected $fillable = [
        'id_mhs',
        'id_semester',
        'id_dosen',
        'sign_path',
        'limit_sks',
        'kredit_sks'
    ];


}
