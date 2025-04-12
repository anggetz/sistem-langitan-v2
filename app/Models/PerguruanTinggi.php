<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;

class PerguruanTinggi extends Model
{
    protected $table = 'perguruan_tinggi';
    protected $primaryKey = 'id_perguruan_tinggi';

    public function semesterAktif()
    {
        return $this->hasMany(Semester::class, 'id_perguruan_tinggi', 'id_perguruan_tinggi')->where('status_aktif_semester', 'True');
    }

    public function scopeByDomain($query, $domain)
    {
        return $query->where('http_host', $domain);
    }

    public function scopeByDomainApi($query, $domain)
    {
        return $query->where('http_host_api', $domain);
    }

    protected static function booted()
    {
        // static::addGlobalScope('byResolvedPT', function (Builder $builder) {
        //     $builder->where('id_perguruan_tinggi', pt()->id_perguruan_tinggi);
        // });
    }
}
