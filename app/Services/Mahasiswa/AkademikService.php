<?php

namespace App\Services\Mahasiswa;

use App\Models\Gedung;
use App\Models\KelasMk;
use App\Models\Message;
use App\Models\Semester;
use App\Models\NamaKelas;
use App\Models\MataKuliah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKegiatanSemester;
use App\Models\Ruangan;
use Exception;

class AkademikService
{

    public function __construct() {}

    public function kalender()
    {
        $data = JadwalKegiatanSemester::select(
            [
                "KEGIATAN.ID_KEGIATAN",
                "KEGIATAN.NM_KEGIATAN",
                "JADWAL_KEGIATAN_SEMESTER.TGL_MULAI_JKS",
                "JADWAL_KEGIATAN_SEMESTER.TGL_SELESAI_JKS"
            ]
        )->leftJoin("KEGIATAN", "JADWAL_KEGIATAN_SEMESTER.ID_KEGIATAN", "=", "KEGIATAN.ID_KEGIATAN")
            ->leftJoin("SEMESTER", "JADWAL_KEGIATAN_SEMESTER.ID_SEMESTER", "=", "SEMESTER.ID_SEMESTER")
            ->where("SEMESTER.STATUS_AKTIF_SEMESTER", "True")
            ->where("KEGIATAN.ID_PERGURUAN_TINGGI", 1)
            ->orderByDesc("JADWAL_KEGIATAN_SEMESTER.TGL_MULAI_JKS")
            ->get();

        return $data;
    }

    public function jadwalKuliah()
    {
        try {
            $data = auth()->user()->mahasiswa->pengambilanMk()
                ->with([
                    "namaKelas:nama_kelas.nama_kelas",
                    "mataKuliah:mata_kuliah.nm_mata_kuliah,mata_kuliah.kredit_semester",
                    "kelasMk" => function ($q) {
                        $q->select('id_kelas_mk');
                        $q->with([
                            'jadwalKelas' => function ($q2) {
                                $q2->select('id_kelas_mk','id_jadwal_jam', 'id_jadwal_hari', 'id_ruangan');
                                $q2->with('jadwalJam:id_jadwal_jam,jam_mulai,menit_mulai,jam_selesai,menit_selesai');
                            }
                        ]);
                    }
                ])
                ->semesterAktif()
                ->whereHas("kelasMk.jadwalKelas")
                ->get(["id_pengambilan_mk", "id_kelas_mk", "id_semester"])
                ->map(function ($item) {
                    $nm_kelas = $item->namaKelas->nama_kelas ?? '-';
                    $nama_mk = $item->mataKuliah->nm_mata_kuliah ?? '-';
                    $jadwalHari = '-';
                    $jadwalJamMulai = '-';
                    $jadwalJamSelesai = '-';
                    $jadwalJamMulaiOrd = 0;
                    $jadwalJamSelesaiOrd = 0;
                    $idJadwalHari = 0;
                    $ruangan = '-';
                    $gedung = '-';

                    $kelasMk = $item->kelasMk;
                    if (!empty($kelasMk)) {
                        $jadwalKelas = $kelasMk->jadwalKelas;

                        if(!empty($jadwalKelas)) {
                            $jadwalJam = $jadwalKelas->jadwalJam;
                            $jadwalHari = $jadwalKelas->nama_hari;
                            $idJadwalHari = $jadwalKelas->id_jadwal_hari;
                            $ruangan = $jadwalKelas->ruangan ?? new Ruangan();
                            $gedung = $ruangan->gedung ?? new Gedung();

                            if (!empty($jadwalJam)) {
                                $jadwalJamMulai = $jadwalJam->jam_mulai . ':' . $jadwalJam->menit_mulai;
                                $jadwalJamSelesai = $jadwalJam->jam_selesai . ':' . $jadwalJam->menit_selesai;
                                $jadwalJamMulaiOrd = $jadwalJam->jam_mulai * 100 + $jadwalJam->menit_mulai;
                                $jadwalJamSelesaiOrd = $jadwalJam->jam_selesai * 100 + $jadwalJam->menit_selesai;
                            }
                        }
                    }
                    return [
                        'nm_kelas' => $nm_kelas,
                        'nama_mk' => $nama_mk,
                        'id_kelas_mk' => $item->id_kelas_mk,
                        'hari' => $jadwalHari,
                        'jam_mulai' => $jadwalJamMulai,
                        'jam_selesai' => $jadwalJamSelesai,
                        'jam_mulai_ord' => $jadwalJamMulaiOrd,
                        'jam_selesai_ord' => $jadwalJamSelesaiOrd,
                        'id_jadwal_hari' => $idJadwalHari,
                        'ruangan' => $ruangan->nm_ruangan,
                        'gedung' => $gedung->nm_gedung
                    ];
                })->sortBy([
                    fn($a, $b) => $a['id_jadwal_hari'] <=> $b['id_jadwal_hari'],
                    fn($a, $b) => $a['jam_mulai_ord'] <=> $b['jam_selesai_ord'],
                ])->groupBy('hari')->map(function ($items) {
                    return $items->map(function ($item) {
                        return [
                            'nama_mk' => $item['nama_mk'],
                            'id_kelas_mk' => $item['id_kelas_mk'],
                            'ruangan' => $item['ruangan'],
                            'gedung' => $item['gedung'],
                            'jadwal' => [
                                'jam' => $item['jam_mulai'] . ' - ' . $item['jam_selesai'],
                            ],
                        ];
                    })->values();
                });;
            return $data;
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function khs($idSemester)
    {
        $data = auth()->user()->mahasiswa->pengambilanMk()
            ->with([
                "namaKelas:nama_kelas.nama_kelas",
                "mataKuliah:mata_kuliah.nm_mata_kuliah,mata_kuliah.kredit_semester"
            ])
            ->whereSemester($idSemester)
            ->get(["id_pengambilan_mk", "id_kelas_mk", "id_mhs", "nilai_huruf", "flagnilai", "id_semester"])
            ->map();
        return $data;
    }
}
