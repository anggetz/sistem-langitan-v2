<?php

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use Blameable;

    protected $table = 'mahasiswa';

    protected $primaryKey = 'id_mhs';

    const CREATED_AT = 'created_on';

    const UPDATED_AT = 'updated_on';

    protected $guarded = [];

    public function getFotoAttribute()
    {
        $perguruan = $this->perguruanTinggi()->select('nama_singkat')->first();
        $url = 'https://langitan.umaha.ac.id/foto_mhs/'.$perguruan->nama_singkat.'/'.$this->nim_mhs.'.jpg';

        return $url;
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'id_program_studi', 'id_program_studi');
    }

    public function kota()
    {
        return $this->hasOne(Kota::class, 'id_kota', 'lahir_kota_mhs')
            ->select('id_kota', 'nm_kota');
    }

    public function provinsi()
    {
        return $this->hasOne(Provinsi::class, 'id_provinsi', 'lahir_prop_mhs')
            ->select('id_provinsi', 'nm_provinsi');
    }

    public function pendidikanIbu()
    {
        return $this->hasOne(PendidikanOrtu::class, 'id_pendidikan_ortu', 'pendidikan_ibu_mhs');
    }

    public function pekerjaanIbu()
    {
        return $this->hasOne(Pekerjaan::class, 'id_pekerjaan', 'pekerjaan_ibu_mhs');
    }

    public function kotaIbu()
    {
        return $this->hasOne(Kota::class, 'id_kota', 'alamat_ibu_mhs_kota')
            ->select('id_kota', 'nm_kota');
    }

    public function provinsiIbu()
    {
        return $this->hasOne(Provinsi::class, 'id_provinsi', 'alamat_ibu_mhs_prov')
            ->select('id_provinsi', 'nm_provinsi');
    }

    public function pendidikanAyah()
    {
        return $this->hasOne(PendidikanOrtu::class, 'id_pendidikan_ortu', 'pendidikan_ayah_mhs');
    }

    public function pekerjaanAyah()
    {
        return $this->hasOne(Pekerjaan::class, 'id_pekerjaan', 'pekerjaan_ayah_mhs');
    }

    public function kotaAyah()
    {
        return $this->hasOne(Kota::class, 'id_kota', 'alamat_ayah_mhs_kota')
            ->select('id_kota', 'nm_kota');
    }

    public function provinsiAyah()
    {
        return $this->hasOne(Provinsi::class, 'id_provinsi', 'alamat_ayah_mhs_prov')
            ->select('id_provinsi', 'nm_provinsi');
    }

    public function pendidikanWali()
    {
        return $this->hasOne(PendidikanOrtu::class, 'id_pendidikan_ortu', 'pendidikan_wali_mhs');
    }

    public function pekerjaanWali()
    {
        return $this->hasOne(Pekerjaan::class, 'id_pekerjaan', 'pekerjaan_wali_mhs');
    }

    public function kotaWali()
    {
        return $this->hasOne(Kota::class, 'id_kota', 'alamat_wali_mhs_kota')
            ->select('id_kota', 'nm_kota');
    }

    public function provinsiWali()
    {
        return $this->hasOne(Provinsi::class, 'id_provinsi', 'alamat_wali_mhs_prov')
            ->select('id_provinsi', 'nm_provinsi');
    }

    public function pengambilanMk()
    {
        return $this->hasMany(PengambilanMk::class, 'id_mhs', 'id_mhs');
    }

    public function pengambilanMkKprs()
    {
        return $this->hasMany(PengambilanMkKprs::class, 'id_mhs', 'id_mhs');
    }

    public function pengambilanMkApproved()
    {
        return $this->pengambilanMk()->where('status_apv_pengambilan_mk', 1);
    }

    public function mahasiswaKrsApprovalSign()
    {
        return $this->hasMany(MahasiswaKrsApprovalSign::class, 'id_mhs', 'id_mhs');
    }

    public function tagihanMhs()
    {
        return $this->hasMany(TagihanMhs::class, 'id_mhs', 'id_mhs');
    }

    public function statusPengguna()
    {
        return $this->belongsTo(StatusPengguna::class, 'status_akademik_mhs', 'id_status_pengguna');
    }

    public function perguruanTinggi()
    {
        return $this->hasOneThrough(PerguruanTinggi::class, Pengguna::class, 'id_pengguna', 'id_perguruan_tinggi', 'id_pengguna', 'id_perguruan_tinggi');
    }

    public function agama()
    {
        return $this->hasOneThrough(Agama::class, Pengguna::class, 'id_pengguna', 'id_agama', 'id_pengguna', 'id_agama');
    }

    public function historyNilai()
    {
        return $this->hasMany(MahasiswaStatus::class, 'id_mhs', 'id_mhs');
    }

    public function transportasi()
    {
        return $this->belongsTo(Transportasi::class, 'id_jenis_transportasi', 'id_jenis_transportasi');
    }

    public function dosenWali()
    {
        return $this->hasMany(DosenWali::class, 'id_mhs', 'id_mhs');
    }

    public function sumberBiaya()
    {
        return $this->belongsTo(SumberBiaya::class, 'sumber_biaya', 'id_sumber_biaya');
    }

    public function asalSekolah()
    {
        return $this->belongsTo(Sekolah::class, 'id_sekolah_asal_mhs', 'id_sekolah');
    }

    public function getDataAkademikAttribute()
    {
        $arrReturn = [
            'ipk_sekarang' => 0,
            'ipk_lalu' => 0,
            'sks_sekarang' => 0,
            'sks_lalu' => 0,
        ];
        if ($data = $this->historyNilai()->orderBy('id_mhs_status', 'desc')->limit(2)->get(['ipk', 'sks_semester', 'id_semester'])) {
            $count = 0;
            foreach ($data as $objStatus) {
                if (! $count) {
                    $arrReturn['ipk_sekarang'] = $objStatus->ipk;
                    $arrReturn['sks_sekarang'] = $objStatus->sks_semester;
                    $arrReturn['id_semester'] = $objStatus->id_semester;
                } else {
                    $arrReturn['ipk_lalu'] = $objStatus->ipk;
                    $arrReturn['sks_lalu'] = $objStatus->sks_semester;
                    $arrReturn['id_semester'] = $objStatus->id_semester;
                }
                $count++;
            }
        }

        return $arrReturn;
    }

    public function jadwalUjian()
    {
        return $this->hasManyThrough(UjianMk::class, UjianMkPeserta::class, 'id_mhs', 'id_ujian_mk', 'id_mhs', 'id_ujian_mk');
    }

    public function pesertaUjian()
    {
        return $this->hasMany(UjianMkPeserta::class, 'id_mhs', 'id_mhs');
    }

    public function kegiatanKemahasiswaan()
    {
        return $this->hasMany(KegiatanKemahasiswaan::class, 'id_mhs', 'id_mhs');
    }
}
