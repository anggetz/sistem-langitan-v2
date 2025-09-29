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
use Illuminate\Support\Facades\Log;

class AkademikService
{
    protected $CODE_JADWAL_PENILAIAN;

    public function __construct()
    {
        $this->CODE_JADWAL_PENILAIAN = env('CODE_JADWAL_PENILAIAN', 'INPUT_NILAI');
    }

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

    public function jadwalKuliah($hari = null)
    {
        try {
            //kurang approval
            $jadwal = auth()->user()->mahasiswa->pengambilanMk()
                ->with([
                    "namaKelas:nama_kelas.nama_kelas",
                    "mataKuliah:mata_kuliah.nm_mata_kuliah,mata_kuliah.kredit_semester",
                    "kelasMk" => function ($q) {
                        $q->select('id_kelas_mk');
                        $q->with([
                            'jadwalKelas' => function ($q2) {
                                $q2->select('id_kelas_mk', 'id_jadwal_jam', 'id_jadwal_hari', 'id_ruangan');
                                $q2->with('jadwalJam:id_jadwal_jam,jam_mulai,menit_mulai,jam_selesai,menit_selesai');
                            }
                        ]);
                    }
                ])
                ->semesterAktif()
                ->where('status_apv_pengambilan_mk', 1) // jadwal kuliah hanya yang sudah di approve
                ->whereHas("kelasMk.jadwalKelas", function ($q) use ($hari) {
                    $q->when($hari, function ($query) use ($hari) {
                        $query->where('id_jadwal_hari', $hari);
                    });
                })
                ->get(["id_pengambilan_mk", "id_kelas_mk", "id_semester"])
                ->map(function ($item) {
                    $nm_kelas = $item->namaKelas->nama_kelas ?? '-';
                    $nama_mk = $item->mataKuliah->nm_mata_kuliah ?? '-';

                    $kelasMk = $item->kelasMk;

                    $jadwallAll = [];
                    foreach ($kelasMk->jadwalKelas as $jadwal) {
                        $jadwallAll[] = [
                            'id_jadwal_hari' => $jadwal->id_jadwal_hari,
                            'nama_hari' => $jadwal->nama_hari,
                            'jam_mulai' => $jadwal->jadwalJam->jam_mulai . ":" . $jadwal->jadwalJam->menit_mulai,
                            'jam_selesai' => $jadwal->jadwalJam->jam_selesai . ":" . $jadwal->jadwalJam->menit_selesai,
                            'jam_mulai_ord' => $jadwal->jadwalJam->jam_mulai * 100 +  $jadwal->jadwalJam->menit_mulai,
                            'jam_selesai_ord' =>  $jadwal->jadwalJam->jam_selesai * 100 +  $jadwal->menit_selesai,
                            'gedung' => $jadwal->ruangan->gedung->nm_gedung ?? '-',
                            'ruangan' => $jadwal->ruangan->nm_ruangan ?? '-',
                        ];
                    }
                    return [
                        'nm_kelas' => $nm_kelas,
                        'nama_mk' => $nama_mk,
                        'id_kelas_mk' => $item->id_kelas_mk,
                        'jadwalAll' => $jadwallAll,
                    ];
                });

            $jadwalResponse = [];
            // make the jadwal as single array
            $jadwal = $jadwal->map(function ($item) use (&$jadwalResponse) {
                foreach ($item['jadwalAll'] as $jadwal) {
                    # code...
                    array_push($jadwalResponse, [
                        'nama_mk' => $item['nama_mk'],
                        'id_kelas_mk' => $item['id_kelas_mk'],
                        'id_jadwal_hari' => $jadwal['id_jadwal_hari'],
                        'hari' => $jadwal['nama_hari'],
                        'jam_mulai' => $jadwal['jam_mulai'],
                        'jam_selesai' => $jadwal['jam_selesai'],
                        'jam_mulai_ord' => $jadwal['jam_mulai_ord'],
                        'jam_selesai_ord' => $jadwal['jam_selesai_ord'],
                        'gedung' => $jadwal['gedung'],
                        'ruangan' => $jadwal['ruangan'],
                    ]);
                }
            });

            if (!collect($jadwalResponse)->count()) return null;

            return  collect($jadwalResponse)
                ->sortBy([
                    fn($a, $b) => $a['id_jadwal_hari'] <=> $b['id_jadwal_hari'],
                    fn($a, $b) => $a['jam_mulai_ord'] <=> $b['jam_selesai_ord'],
                ])
                ->groupBy('hari')->toArray();
            //dd($arrReturn);
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
                "mataKuliah:mata_kuliah.nm_mata_kuliah,kd_mata_kuliah",
                "kelasMk:kelas_mk.id_kelas_mk,kelas_mk.kredit_semester"
            ])
            ->whereSemester($idSemester)
            // multi on
            ->join('mahasiswa_status', function ($join) {
                $join->on('pengambilan_mk.id_mhs', '=', 'mahasiswa_status.id_mhs')
                    ->whereRaw('mahasiswa_status.id_semester = pengambilan_mk.id_semester');
            })
            ->get(["pengambilan_mk.id_pengambilan_mk", "pengambilan_mk.id_kelas_mk", "pengambilan_mk.id_mhs", "pengambilan_mk.nilai_huruf", "pengambilan_mk.nilai_angka", "pengambilan_mk.flagnilai", "pengambilan_mk.id_semester", "mahasiswa_status.komponens"])
            ->map(function ($item) {
                $item->komponens = $item->komponens == null ? json_decode("{}", true) : json_decode($item->komponens, true);
                return $item;
            });
        return $data;
    }


    public function ValidateInputNilaiScheduleByActiveSemester()
    {
        // get jadwal penilaian current semester in range or not
        $semesterAktif = Semester::aktif();
        if (!$semesterAktif) {
            Log::error("Semester aktif tidak ditemukan.");
            return [
                'status' => false,
                'message' => "Semester aktif tidak ditemukan.",
                'error' => "Semester aktif tidak ditemukan."
            ];
        }

        $jadwalPenilaian = JadwalKegiatanSemester::where('id_semester', $semesterAktif->id_semester)
            ->where('id_perguruan_tinggi', pt()->id_perguruan_tinggi)
            ->whereHas('kegiatan', function ($query) {
                $query->where('id_perguruan_tinggi', pt()->id_perguruan_tinggi);
                $query->where('kode_kegiatan', $this->CODE_JADWAL_PENILAIAN);
            })
            ->first();

        if (!$jadwalPenilaian) {
            Log::error("Jadwal penilaian tidak ditemukan untuk semester aktif: {$semesterAktif->id_semester}.");
            return [
                'status' => false,
                'message' => "Jadwal penilaian tidak ditemukan untuk semester aktif: {$semesterAktif->id_semester} dan perguruan tinggi " . pt()->id_perguruan_tinggi,
                'error' => "Jadwal penilaian tidak ditemukan untuk semester aktif: {$semesterAktif->id_semester} dan perguruan tinggi " . pt()->id_perguruan_tinggi
            ];
        }

        $currentDate = now();
        $startDate = $jadwalPenilaian->tgl_mulai_jks;
        $endDate = $jadwalPenilaian->tgl_selesai_jks;

        if ($currentDate < $startDate || $currentDate > $endDate) {
            Log::error("Jadwal penilaian tidak valid untuk tanggal saat ini: {$currentDate}. Jadwal penilaian berlaku dari {$startDate} hingga {$endDate}.");
            return [
                'status' => false,
                'message' => "Jadwal penilaian tidak valid untuk tanggal saat ini: {$currentDate}. Jadwal penilaian berlaku dari {$startDate} hingga {$endDate}.",
                'error' => "Jadwal penilaian tidak valid untuk tanggal saat ini: {$currentDate}. Jadwal penilaian berlaku dari {$startDate} hingga {$endDate}.",
                'info' => $jadwalPenilaian
            ];
        }
        return [
            'status' => 'true',
            'info' => $jadwalPenilaian,
        ];
    }
}
