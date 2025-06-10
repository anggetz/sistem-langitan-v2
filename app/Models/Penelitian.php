<?php

namespace App\Models;

use App\Models\Pengguna;
use App\Models\PengampuMk;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class Penelitian extends Model
{
    use Blameable;
    protected $table = 'penelitian';
    protected $primaryKey = 'id_penelitian';
    public $timestamps = false;


    protected $fillable = [
        'judul',
        'lokasi',
        'jangka_waktu',
        'kota',
        'nama_institusi',
        'jangka_waktu_ke',
        'id_penelitian_skim',
        'id_penelitian_bidang',
        'id_penelitian_bidang_ilmu',
        'id_penelitian_sumber_dana',
        'penelitian_bidang_lain',
        'is_proposal',
        'nama_file',
        'id_peneliti',
    ];

    public function penelitianSkim()
    {
        return $this->belongsTo(PenelitianSkim::class, 'id_penelitian_skim', 'id_penelitian_skim');
    }
    public function penelitianBidang()
    {
        return $this->belongsTo(PenelitianBidang::class, 'id_penelitian_bidang', 'id_penelitian_bidang');
    }
    public function penelitianBidangIlmu()
    {
        return $this->belongsTo(PenelitianBidangIlmu::class, 'id_penelitian_bidang_ilmu', 'id_penelitian_bidang_ilmu');
    }
    public function penelitianSumberDana()
    {
        return $this->belongsTo(penelitianSumberDana::class, 'id_penelitian_sumber_dana', 'id_penelitian_sumber_dana');
    }
    public function peneliti()
    {
        return $this->belongsTo(Dosen::class, 'id_peneliti', 'id_dosen');
    }
}
