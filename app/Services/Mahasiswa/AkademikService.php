<?php

namespace App\Services\Mahasiswa;

use App\Models\KelasMk;
use App\Models\Message;
use App\Models\Semester;
use App\Models\NamaKelas;
use App\Models\MataKuliah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKegiatanSemester;

class AkademikService{

    public function __construct()
    {
        
    }

    public function kalender()
    {
        $data = JadwalKegiatanSemester::select(
            ["KEGIATAN.ID_KEGIATAN",
            "KEGIATAN.NM_KEGIATAN",
            "JADWAL_KEGIATAN_SEMESTER.TGL_MULAI_JKS",
            "JADWAL_KEGIATAN_SEMESTER.TGL_SELESAI_JKS"]
        )->leftJoin("KEGIATAN", "JADWAL_KEGIATAN_SEMESTER.ID_KEGIATAN", "=", "KEGIATAN.ID_KEGIATAN")
        ->leftJoin("SEMESTER","JADWAL_KEGIATAN_SEMESTER.ID_SEMESTER", "=", "SEMESTER.ID_SEMESTER")
        ->where("SEMESTER.STATUS_AKTIF_SEMESTER", "True")
        ->where("KEGIATAN.ID_PERGURUAN_TINGGI", 1)
        ->orderByDesc("JADWAL_KEGIATAN_SEMESTER.TGL_MULAI_JKS")
        ->get();

        return $data;
    }

    public function jadwalKuliah(){
        $data=auth()->user()->mahasiswa->pengambilanMk()
                ->with(["namaKelas:nama_kelas.nama_kelas",
                        "mataKuliah:mata_kuliah.nm_mata_kuliah,mata_kuliah.kredit_semester",
                        "jadwalKelasMk"])
                ->semesterAktif()
                ->whereHas("kelasMk.jadwalKelas")
                ->get(["id_pengambilan_mk","id_kelas_mk","id_semester"]);

        // $data=$mahasiswa->pengambilan_mk()
        //         ->addSelect([
        //             "no_kelas_mk"=>KelasMk::select("no_kelas_mk")
        //             ->whereColumn("pengambilan_mk.id_kelas_mk","kelas_mk.id_kelas_mk")
        //         ])->get();

        return $data;
    }

    public function khs($idSemester){
        $data=auth()->user()->mahasiswa->pengambilanMk()
                ->with(["namaKelas:nama_kelas.nama_kelas",
                        "mataKuliah:mata_kuliah.nm_mata_kuliah,mata_kuliah.kredit_semester"
                        ])
                ->whereSemester($idSemester)
                ->get(["id_pengambilan_mk","id_kelas_mk","id_mhs","nilai_huruf","flagnilai","id_semester"]);
        return $data;
    }
}
