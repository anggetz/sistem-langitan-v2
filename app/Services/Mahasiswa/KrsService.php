<?php

namespace App\Services\Mahasiswa;

use App\Models\DosenWali;
use App\Models\JadwalJam;
use App\Models\JadwalKegiatanSemester;
use App\Models\JadwalKelas;
use App\Models\Kegiatan;
use App\Models\KelasMk;
use App\Models\MataKuliah;
use App\Models\PengambilanMk;
use App\Models\PengambilanMkKprs;
use App\Models\PengampuMk;
use App\Models\ProgramStudi;
use App\Models\Semester;
use App\Models\TagihanMhs;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KrsService
{
    const CODE = "KRS";

    public function ValidateKRSScheduleByActiveSemester()
    {

        // get krs kegiatan id
        $kegiatan = Kegiatan::where('kode_kegiatan', self::CODE)
            ->first();

        if (!$kegiatan) {
            throw new \Exception("Kegiatan with code " . self::CODE . " not found.");
        }

        // get active semester
        $semesterAktif = Semester::aktif();
        if (!$semesterAktif) {
            throw new \Exception("No active semester found.");
        }

        $jadwalKegiatanSemester = JadwalKegiatanSemester::where('id_kegiatan', $kegiatan->id_kegiatan)
            ->where('id_semester', $semesterAktif->id_semester)
            ->where('tgl_mulai_jks', '<=', Carbon::now()->timezone(env('APP_TIMEZONE')))
            ->where('tgl_selesai_jks', '>=', Carbon::now()->timezone(env('APP_TIMEZONE')))
            ->first();

        if (!$jadwalKegiatanSemester) {
            throw new \Exception("No active schedule found for the KRS activity in the current semester.");
        }

        return true; // Placeholder for actual validation logic
    }

    public function listMataKuliahByActiveSemesterAndProdi($id_program_studi)
    {

        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 10);
        $offset = ($page - 1) * $perPage;

        $semesterAktif = Semester::aktif();
        if (!$semesterAktif) {
            throw new \Exception("No active semester found.");
        }

        $kelasMk = KelasMk::select([
            'id_kelas_mk',
            'kapasitas_kelas_mk',
            'no_kelas_mk',
            'id_mata_kuliah',
            'terisi_kelas_mk',
        ])
            ->where('id_semester', $semesterAktif->id_semester)
            ->where('id_program_studi', $id_program_studi)
            ->with([
                'nama',
                'mataKuliah' => function ($query) {
                    $query->select(['id_mata_kuliah', 'nm_mata_kuliah', 'kd_mata_kuliah', 'kredit_tatap_muka']);
                },
                'pengampuMk' => function ($query) {
                    $query->select(['id_pengampu_mk', 'id_dosen', 'id_kelas_mk'])
                        ->with(['dosen' => function ($q) {
                            $q->with(['pengguna' => function ($q2) {
                                $q2->select(['id_pengguna', DB::raw("gelar_depan || ' ' || nm_pengguna || ' ' || gelar_belakang as nama_lengkap")]);
                            }])->select(['id_dosen', 'id_pengguna']);
                        }]);
                },
                'jadwalKelas' => function ($query) {
                    $query
                        ->with(['ruangan' => function ($q) {
                            $q->select(['id_ruangan', 'nm_ruangan']);
                        }, 'jadwalJam' => function ($q) {
                            $q->select(['id_jadwal_jam', DB::raw('jam_mulai || \':\' || menit_mulai as waktu_mulai'), DB::raw('jam_selesai || \':\' || menit_selesai as waktu_selesai')]);
                        }])
                        ->select(['id_jadwal_jam', 'id_ruangan', 'id_kelas_mk']);
                },
                'pengambilanMkKprs' => function ($query) use ($semesterAktif) {
                    $query->where('id_mhs', auth()->user()->mahasiswa->id_mhs)
                        ->where('id_semester', $semesterAktif->id_semester);
                }

            ])
            ->limit($perPage)
            ->offset($offset)
            ->get()->map(function ($item) {
                $mataKuliah = $item->mataKuliah;
                $pengampu = !empty($item->pengampuMk) ? $item->pengampuMk->first() : new PengampuMk();
                $dosen = $pengampu->dosen ?? null;
                $pengguna = $dosen?->pengguna;

                $jadwal = $item->jadwalKelas;
                $ruangan = $jadwal?->ruangan;
                $jam = $jadwal?->jadwalJam;

                // TODO: add field for terisi kelasmk
                return [
                    'id_kelas_mk' => $item->id_kelas_mk,
                    'kapasitas_kelas_mk' => $item->kapasitas_kelas_mk,
                    'no_kelas_mk' => $item->no_kelas_mk,
                    'id_mata_kuliah' => $item->id_mata_kuliah,

                    'nama_kelas' => $item->nama->nama_kelas ?? '',
                    'nm_mata_kuliah' => $mataKuliah->nm_mata_kuliah ?? '',
                    'kd_mata_kuliah' => $mataKuliah->kd_mata_kuliah ?? '',
                    'sks' => $mataKuliah->kredit_mata_kuliah ?? 0,

                    'nama_dosen' => $pengguna->nama_lengkap ?? '',
                    'id_dosen' => $dosen?->id_dosen ?? null,

                    'nama_ruangan' => $ruangan->nm_ruangan ?? '',
                    'waktu_mulai' => $jam->waktu_mulai ?? '',
                    'waktu_selesai' => $jam->waktu_selesai ?? '',
                    'sudah_diambil' => $item->pengambilanMkKprs ? true : false,
                    'telah_disetujui' => $item->pengambilanMkKprs && $item->pengambilanMkKprs->status_apv_pengambilan_mk == 1 ? true : false,
                ];
            });;



        // Assuming there's a method to get courses by semester
        return $kelasMk;
    }

    public function takeCourse($id_kelas_mks = [])
    {

        $semesterAktif = Semester::aktif();
        if (!$semesterAktif) {
            throw new \Exception("No active semester found.");
        }

        DB::beginTransaction();

        foreach ($id_kelas_mks as $id_kelas_mk) {
            $pengambilanMkKprs = PengambilanMkKprs::where('id_kelas_mk', $id_kelas_mks)
                ->where('id_mhs', auth()->user()->mahasiswa->id_mhs)
                ->where('id_semester', $semesterAktif->id_semester)
                ->first();

            if ($pengambilanMkKprs) {
                throw new \Exception("You have already taken this course.");
            }


            $pengambilanMkKprs = new PengambilanMkKprs();
            $pengambilanMkKprs->id_kelas_mk = $id_kelas_mk;
            $pengambilanMkKprs->id_mhs = auth()->user()->mahasiswa->id_mhs;
            $pengambilanMkKprs->id_semester = $semesterAktif->id_semester;
            $pengambilanMkKprs->status_apv_pengambilan_mk = 0; // Not approved yet
            $pengambilanMkKprs->save();
        }

        DB::commit();

        return true;
    }

    public function approveKprsMk($id_pengambilan_mk_kprs)
    {
        DB::beginTransaction();

        $pengambilanMkKprs = PengambilanMkKprs::whereIn('id_pengambilan_mk_kprs', $id_pengambilan_mk_kprs)
            ->get();
        if ($pengambilanMkKprs->isEmpty()) {
            throw new \Exception("No courses found for approval.");
        }

        foreach ($pengambilanMkKprs as $item) {
            $item->status_apv_pengambilan_mk = 1; // Set to approved
            $item->save();

            // save to penambilan mk
            // check if exist to prevent double
            $isExist = PengambilanMk::where('id_kelas_mk', $item->id_kelas_mk)
                ->where('id_mhs', $item->id_mhs)
                ->where('id_semester', $item->id_semester)
                ->exists();

            if (!$isExist) {
                $pengambilanMk = new PengambilanMk();
                $pengambilanMk->id_kelas_mk = $item->id_kelas_mk;
                $pengambilanMk->id_mhs = $item->id_mhs;
                $pengambilanMk->id_semester = $item->id_semester;
                $pengambilanMk->status_apv_pengambilan_mk = $item->status_apv_pengambilan_mk;
                $pengambilanMk->status_pengambilan_mk = 1;
                $pengambilanMk->save();
            }
        }

        DB::commit();

        return true;
    }

    public function listCourse($id_dosen)
    {
        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 10);
        $offset = ($page - 1) * $perPage;

        $semesterAktif = Semester::aktif();
        if (!$semesterAktif) {
            throw new \Exception("No active semester found.");
        }

        $anakWaliMhses = DosenWali::where('id_dosen', $id_dosen)
            ->where('id_semester', $semesterAktif->id_semester)->get();

        $idMahasiswa = [];
        foreach ($anakWaliMhses as $anakWaliMhs) {
            # code...
            array_push($idMahasiswa, $anakWaliMhs->id_mhs);
        }

        // get pengambilan mk kprs
        $pengambilanMkKprs = PengambilanMkKprs::whereIn('id_mhs', $idMahasiswa)
            ->where('id_semester', $semesterAktif->id_semester)
            ->with([
                'kelasMk' => function ($q) {
                    $q->select([
                        'id_kelas_mk',
                        'no_kelas_mk',
                        'id_mata_kuliah'
                    ])
                        ->with([
                            'mataKuliah' => function ($q2) {
                                $q2->select('id_mata_kuliah', 'nm_mata_kuliah', 'kredit_tatap_muka');
                            },
                            'nama' => function ($q2) {
                                $q2->select('id_nama_kelas', 'nama_kelas');
                            },
                            'jadwalKelas' => function ($query) {
                                $query
                                    ->with(['ruangan' => function ($q) {
                                        $q->select(['id_ruangan', 'nm_ruangan']);
                                    }, 'jadwalJam' => function ($q) {
                                        $q->select(['id_jadwal_jam', DB::raw('jam_mulai || \':\' || menit_mulai as waktu_mulai'), DB::raw('jam_selesai || \':\' || menit_selesai as waktu_selesai')]);
                                    }])
                                    ->select(['id_jadwal_jam', 'id_ruangan', 'id_kelas_mk']);
                            },
                        ]);
                },
                'mahasiswa' => function ($q2) {
                    $q2->select([
                        'id_mhs',
                        'id_pengguna'
                    ])
                        ->with([
                            'pengguna' => function ($q3) {
                                $q3->select([
                                    'id_pengguna',
                                    'nm_pengguna'
                                ]);
                            }
                        ]);
                }
            ])
            ->limit($perPage)
            ->offset($offset)
            ->get()
            ->map(function ($item) {

                $kelasMk = $item->kelasMk;

                if (!empty($kelasMk)) {
                    $jadwal = $kelasMk->jadwalKelas;
                    $ruangan = $jadwal?->ruangan;
                    $jam = $jadwal?->jadwalJam;
                } else {
                    $ruangan = '-';
                    $jam = '-';
                    $jadwal = '-';
                }


                return [
                    'id_pengambilan_mk_kprs' => $item->id_pengambilan_mk_kprs,
                    'id_mhs' => $item->id_mhs,
                    'id_semester' => $item->id_semester,
                    'nm_mahasiswa' => optional($item->mahasiswa->pengguna)->nm_pengguna,
                    'id_kelas_mk' => optional($item->kelasMk)->id_kelas_mk,
                    'no_kelas_mk' => optional($item->kelasMk)->no_kelas_mk,
                    'nama_ruangan' => $ruangan->nm_ruangan ?? '',
                    'waktu_mulai' => $jam->waktu_mulai ?? '',
                    'waktu_selesai' => $jam->waktu_selesai ?? '',
                    'status_approval' => $item->status_apv_pengambilan_mk,
                    'id_mata_kuliah' => optional($item->kelasMk)->id_mata_kuliah,
                    'nm_mata_kuliah' => !empty($mataKuliah) ? optional($item->kelasMk->mataKuliah)->nm_mata_kuliah : '-',
                    'kredit_tatap_muka' => !empty($mataKuliah) ? optional($item->kelasMk->mataKuliah)->kredit_tatap_muka : '-',
                    'nama_kelas' => !empty($mataKuliah) ? optional($item->kelasMk->nama)->nama_kelas : '-',
                ];
            });;

        return $pengambilanMkKprs;
    }

    public function getHistoryKrsByIdMhs($id_mhs, $th_semester)
    {

        $q = PengambilanMk::where('id_mhs', $id_mhs)
            ->with([
                'kelasMk.programStudi',
                'kelasMk.mataKuliah',
                'kelasMk.jadwalKelas',
                'semester'
            ]);

        if (!empty($th_semester)) {
            $q = $q->whereHas('semester', function ($q2) use ($th_semester) {
                $q2->where('thn_akademik_semester', $th_semester);
            });
        }

        $data = $q->get()->map(function ($item) {
            $semester = $item->semester ?? new Semester();
            $kelasMk = $item->kelasMk ?? new KelasMk();
            $programStudi = $kelasMk->programStudi ?? new ProgramStudi();
            $mataKuliah = $kelasMk->mataKuliah ?? new MataKuliah();
            $jadwalKelas = $kelasMk->jadwalKelas ?? new JadwalKelas();
            $jadwalJam = $jadwalKelas->jadwalJam ?? new JadwalJam();
            $jadwalHari = $jadwalKelas->nama_hari;

            return [
                'status' => $item->status_apv_pengambilan_mk,
                'semester' => $semester->nm_semester,
                'th_semester' => $semester->thn_akademik_semester,
                'program_studi' => $programStudi->nm_program_studi,
                'mata_kuliah' => $mataKuliah->nm_mata_kuliah,
                'hari' => $jadwalHari,
                'jam_mulai' => $jadwalJam ? $jadwalJam->jam_mulai . ':' . $jadwalJam->menit_mulai : '-',
                'jam_selesai' => $jadwalJam ? $jadwalJam->jam_selesai . ':' . $jadwalJam->menit_selesai : '-'
            ];
        })->groupBy(['th_semester', 'semester']);

        return $data;
    }

    public function validateMahasiswaCanKrsByActiveSemesterAndPrevSemester($id_mhs)
    {
        $semesterAktif = Semester::aktif();
        if (!$semesterAktif) {
            throw new \Exception("No active semester found.");
        }

        $krsActiveSemester = $this->getTagihanBySemester($id_mhs, $semesterAktif->id_semester);

        $isTrueActiveSemster = empty($krsActiveSemester); //tidak ada tagihan mhs untuk semester aktif

        if (!empty($krsActiveSemester)) {
            // check if the total biaya and denda is less than or equal to total terbayar
            if ($krsActiveSemester->total_besar_biaya + $krsActiveSemester->total_denda_biaya > $krsActiveSemester->total_terbayar_bayar) {
                return false;
            }
            $isTrueActiveSemster = true; // ada tagihan mhs untuk semester aktif
        }

        $prevSemester = Semester::prevAktif();
        if (!$prevSemester) {
            throw new \Exception("No previous semester found.");
        }

       $prevKrsProdi = $this->getTagihanBySemester($id_mhs, $prevSemester->id_semester);

       $isTruePrevSemester = empty($prevKrsProdi); //tidak ada tagihan mhs untuk semester sebelumnya

        if (!empty($prevKrsProdi)) {
            // check if the total biaya and denda is less than or equal to total terbayar
            if ($prevKrsProdi->total_besar_biaya + $prevKrsProdi->total_denda_biaya > $prevKrsProdi->total_terbayar_bayar) {
                return false;
            }
            $isTruePrevSemester = true; // ada tagihan mhs untuk semester sebelumnya
        }

        return true && $isTrueActiveSemster && $isTruePrevSemester;
    }

    private function getTagihanBySemester($id_mhs, $id_semester)
    {
        return TagihanMhs::where('id_mhs', $id_mhs)
            ->where('id_perguruan_tinggi', pt()->id_perguruan_tinggi)
            ->where('id_semester', $id_semester)
            ->first();
    }
}
