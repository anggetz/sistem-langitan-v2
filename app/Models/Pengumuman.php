<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;
    protected $table = 'pengumuman';
    public $timestamps = true;
    protected $primaryKey = 'id_pengumuman';

    protected $fillable = [
        'judul',
        'konten',
        'tanggal_expired',
    ];
}
// CREATE SEQUENCE UMAHA.PENGUMUMAN_SEQ INCREMENT BY 1 MINVALUE 0 NOCYCLE NOCACHE NOORDER ;
// CREATE OR REPLACE TRIGGER "UMAHA"."PENGUMUMAN_TRG"
// BEFORE INSERT
// ON PENGUMUMAN
// REFERENCING NEW AS NEW
// FOR EACH ROW
// BEGIN
// SELECT pengumuman_seq.nextval INTO :NEW.ID_PENGUMUMAN FROM dual;
// END;
