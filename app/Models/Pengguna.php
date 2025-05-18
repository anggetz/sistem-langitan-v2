<?php

namespace App\Models;

use App\Models\Role;
use App\Traits\Blameable;
use App\Models\PerguruanTinggi;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, Blameable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    protected $hidden = [
        'password_hash',
        'password_encrypted',
        'password_hash_temp'
    ];
    protected $fillable = ['password_hash'];
    protected $dates = [
        'tgl_lahir_pengguna',
        'last_time_password',
        'last_time_login',
        'otp_expired',
        'fd_sync_on'
    ];

    public const CREATED_AT = 'created_on';
    public const UPDATED_AT = 'updated_on';

    protected $appends = ['nama_lengkap', 'foto', 'email'];

    public function getAuthIdentifierName()
    {
        return 'id_pengguna';
    }

    public function getEmailAttribute()
    {
        return $this->email_pengguna ?? $this->email_alternate;
    }

    public function getNamaLengkapAttribute()
    {
        return "{$this->gelar_depan} {$this->nm_pengguna} {$this->gelar_belakang}";
    }

    public function getFotoAttribute()
    {
        $pt = pt();

        if ($this->id_role == Role::MAHASISWA) {
            $fotoPath = config('app.foto_mahasiswa_path');
            $extension = config('app.foto_mahasiswa_ext');
        }
        else {
            $fotoPath = config('app.foto_pegawai_path');
            $extension = config('app.foto_pegawai_ext');
        }

        return url($fotoPath . '/' . $this->username . '.' . $extension);
    }

    // role
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'id_pengguna', 'id_pengguna')->with(['programStudi:id_program_studi,nm_program_studi,sks_lulus', 'statusPengguna:id_status_pengguna,nm_status_pengguna']);
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'id_pengguna', 'id_pengguna');
    }

    public function perguruanTinggi()
    {
        return $this->belongsTo(PerguruanTinggi::class, 'id_perguruan_tinggi', 'id_perguruan_tinggi');
    }

    public function kotaLahir()
    {
        return $this->belongsTo(Kota::class, 'id_kota_lahir', 'id_kota');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected static function booted()
    {
        static::addGlobalScope('byResolvedPT', function (Builder $builder) {
            // $builder->where('id_perguruan_tinggi', pt()->id_perguruan_tinggi);
        });
    }
}
