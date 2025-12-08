<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PublikasiJobStatus extends Model
{
    protected $table = 'PUBLIKASI_JOB_STATUS';
    protected $primaryKey = 'ID_PUBLIKASI_JOB_STATUS';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = true;
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'WEBSOCKET_TOPIC',
        'JOB_STATUS',
        'PARAMETER',
        'PROCESS_TIMES',
        'ID_PENGGUNA'
    ];

    protected $casts = [
        'CREATED_AT' => 'datetime',
        'UPDATED_AT' => 'datetime',
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         $pk = $model->getKeyName();
    //         if (empty($model->{$pk})) {
    //             $seqName = 'PUBLIKASI_JOB_STATUS_SEQ';
    //             $res = DB::select("SELECT {$seqName}.NEXTVAL AS NEXTID FROM DUAL");

    //             if (!empty($res) && isset($res[0]->NEXTID)) {
    //                 $model->{$pk} = (int) $res[0]->NEXTID;
    //             }
    //         }
    //     });
    // }
}
