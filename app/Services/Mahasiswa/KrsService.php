<?php

namespace App\Services\Mahasiswa;

use App\Models\BebanSks;
use App\Models\DosenWali;
use App\Models\JadwalJam;
use App\Models\JadwalKegiatanSemester;
use App\Models\JadwalKelas;
use App\Models\Kegiatan;
use App\Models\KelasMk;
use App\Models\KrsProdi;
use App\Models\Mahasiswa;
use App\Models\MahasiswaKrsApprovalSign;
use App\Models\MataKuliah;
use App\Models\PengambilanMk;
use App\Models\PengambilanMkKprs;
use App\Models\PengampuMk;
use App\Models\Pengguna;
use App\Models\ProgramStudi;
use App\Models\Ruangan;
use App\Models\Semester;
use App\Models\TagihanMhs;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KrsService
{
    const CODE = "KRS";

    public function ValidateKRSScheduleByActiveSemester()
    {

        // get krs kegiatan id
        $kegiatan = Kegiatan::where('kode_kegiatan', self::CODE)
            ->where('id_perguruan_tinggi', 1)
            ->first();

        if (!$kegiatan) {
            throw new \Exception("Kegiatan with code " . self::CODE . " not found.");
        }

        // get active semester
        $semesterAktif = Semester::aktif();
        if (!$semesterAktif) {
            throw new \Exception("Tidak ada semester aktif yang ditemukan.");
        }

        $jadwalKegiatanSemester = JadwalKegiatanSemester::where('id_kegiatan', $kegiatan->id_kegiatan)
            ->where('id_semester', $semesterAktif->id_semester)
            ->where('tgl_mulai_jks', '<=', Carbon::now()->timezone(env('APP_TIMEZONE')))
            ->where('tgl_selesai_jks', '>=', Carbon::now()->timezone(env('APP_TIMEZONE')))
            ->first();

        if (!$jadwalKegiatanSemester) {
            throw new \Exception("Tidak ada jadwal kegiatan KRS untuk semester aktif.");
        }

        return true; // Placeholder for actual validation logic
    }

    public function listMataKuliahByActiveSemesterAndProdi($id_program_studi, $id_semester = null)
    {

        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 10);
        $offset = ($page - 1) * $perPage;


        $semesterAktif = Semester::aktif();
        if (!$semesterAktif) {
            throw new \Exception("Semester aktif tidak ditemukan.");
        }

        $q = KrsProdi::whereHas('kelasMk', function ($query) use ($semesterAktif, $id_program_studi, $id_semester) {
            $query->where('id_program_studi', $id_program_studi);

        });

        if ($id_semester) {
            $q->where('id_semester', $id_semester);
        } else {
            $q->where('id_semester', $semesterAktif->id_semester);
        }

        // get total count
        $totalCount = $q->count();

        $q = $q->with([
            'kelasMk' => function ($query) {
                $query->select([
                    'id_kelas_mk',
                    'kapasitas_kelas_mk',
                    'kredit_semester',
                    'no_kelas_mk',
                    'id_mata_kuliah',
                    'terisi_kelas_mk'
                ])
                    ->with([
                        'nama' => function ($q) {
                            $q->select(['id_nama_kelas', 'nama_kelas']);
                        },
                        'mataKuliah' => function ($q) {
                            $q->select(['id_mata_kuliah', 'nm_mata_kuliah', 'kd_mata_kuliah', 'kredit_tatap_muka']);
                        },
                        'pengampuMk' => function ($q) {
                            $q->select(['id_pengampu_mk', 'id_dosen', 'id_kelas_mk'])
                                ->with(['dosen' => function ($q2) {
                                    $q2->with(['pengguna' => function ($q3) {
                                        $q3->select(['id_pengguna', DB::raw("gelar_depan || ' ' || nm_pengguna || ' ' || gelar_belakang as nama_lengkap")]);
                                    }])->select(['id_dosen', 'id_pengguna', 'gelar_depan', 'nm_pengguna', 'gelar_belakang']);
                                }]);
                        },
                        'jadwalKelas' => function ($query) {
                            $query
                                ->with(['ruangan' => function ($q) {
                                    $q->select(['id_ruangan', 'nm_ruangan']);
                                }, 'jadwalJam' => function ($q) {
                                    $q->select(['id_jadwal_jam', DB::raw('jam_mulai || \':\' || menit_mulai as waktu_mulai'), DB::raw('jam_selesai || \':\' || menit_selesai as waktu_selesai')]);
                                }])
                                ->select(['id_jadwal_jam', 'id_ruangan', 'id_kelas_mk', 'id_jadwal_kelas', 'id_jadwal_hari']);
                        },
                    ]);
            }
        ]);

        $listMk = $q->limit($perPage)
            ->offset($offset)
            ->get()->map(function ($item) {
                $kelasMk = $item->kelasMk ?? new KelasMk();
                $mataKuliah = $kelasMk->mataKuliah;

                $pengampus = [];

                foreach ($kelasMk->pengampuMk as $pengampuMk) {
                    $pengampus[] = [
                        'id_pengampu_mk' => $pengampuMk->id_pengampu_mk,
                        'id_dosen' => $pengampuMk->dosen?->id_dosen ?? null,
                        'nama_dosen' => $pengampuMk->dosen?->pengguna?->nama_lengkap ?? '',
                    ];
                }


                $jadwal = !empty($kelasMk->jadwalKelas) ? $kelasMk->jadwalKelas : new JadwalKelas();

                // jadwal kelas is array then we need to get all jadwal
                $jadwalAll = [];
                foreach ($jadwal as $jadwalKelas) {
                    $ruangan = !empty($jadwalKelas->ruangan) ? $jadwalKelas->ruangan : new Ruangan();
                    $jam = !empty($jadwalKelas->jadwalJam) ?  $jadwalKelas->jadwalJam : new JadwalJam();
                    $jadwalAll[] = [
                        'nama_ruangan' => $ruangan->nm_ruangan ?? '',
                        'waktu_mulai' => $jam->waktu_mulai ?? '',
                        'waktu_selesai' => $jam->waktu_selesai ?? '',
                        'id_jadwal_kelas' => $jadwalKelas->id_jadwal_kelas ?? null,
                        'nama_hari' => $jadwalKelas->nama_hari,
                    ];
                }

                // TODO: add field for terisi kelasmk
                return [
                    'id_kelas_mk' => $kelasMk->id_kelas_mk,
                    'kapasitas_kelas_mk' => $kelasMk->kapasitas_kelas_mk,
                    'no_kelas_mk' => $kelasMk->no_kelas_mk,
                    'id_mata_kuliah' => $kelasMk->id_mata_kuliah,
                    'nama_kelas' => $kelasMk->nama->nama_kelas ?? '',
                    'nm_mata_kuliah' => $mataKuliah->nm_mata_kuliah ?? '',
                    'kd_mata_kuliah' => $mataKuliah->kd_mata_kuliah ?? '',
                    'sks' => (int)$kelasMk->kredit_semester ?? 0,
                    'pengampu_mk' => $pengampus,
                    'jadwal_kelas' => $jadwalAll,
                    'sudah_diambil' => count($kelasMk->pengambilanMkKprs) > 0 ? true : false,
                    'telah_disetujui' => count($kelasMk->pengambilanMkKprs) > 0 ? $kelasMk->pengambilanMkKprs[0]->status_apv_pengambilan_mk == 1 : false,
                ];
            });
        // Assuming there's a method to get courses by semester
        return [
            'total_count' => $totalCount,
            'current_page' => $page,
            'per_page' => $perPage,
            'data' => $listMk,
            'total_pages' => ceil($totalCount / $perPage)
        ];
    }

    public function takeCourse($id_kelas_mks = [], $id_mhs)
    {

        $semesterAktif = Semester::aktif();
        if (!$semesterAktif) {
            throw new \Exception("Tidak ada semester aktif yang ditemukan.");
        }

        // validate kredit semester with limit
        $this->validatingKreditSemsesterWithLimit($id_mhs, $semesterAktif->id_semester);


        DB::beginTransaction();

        foreach ($id_kelas_mks as $id_kelas_mk) {
            $pengambilanMkKprs = PengambilanMkKprs::where('id_kelas_mk', $id_kelas_mk)
                ->where('id_mhs', $id_mhs)
                ->first();

            if ($pengambilanMkKprs) {
                throw new \Exception("Anda sudah mengambil mata kuliah ini.");
            }


            $pengambilanMkKprs = new PengambilanMkKprs();
            $pengambilanMkKprs->id_kelas_mk = $id_kelas_mk;
            $pengambilanMkKprs->id_mhs = $id_mhs;
            $pengambilanMkKprs->id_semester = $semesterAktif->id_semester;
            $pengambilanMkKprs->status_apv_pengambilan_mk = 0; // Not approved yet
            $pengambilanMkKprs->save();

            //TODO: store to new table krs.
        }

        DB::commit();

        return true;
    }

    public function leaveCourse($id_kelas_mks = [], $id_mhs)
    {

        $semesterAktif = Semester::aktif();
        if (!$semesterAktif) {
            throw new \Exception("Tidak ada semester aktif yang ditemukan.");
        }

        DB::beginTransaction();

        foreach ($id_kelas_mks as $id_kelas_mk) {
            $pengambilanMkKprs = PengambilanMkKprs::where('id_kelas_mk', $id_kelas_mk)
                ->where('id_mhs', $id_mhs)
                ->first();

            if (empty($pengambilanMkKprs)) {
                throw new \Exception("Tidak ada pengambilan mata kuliah ini atau sudah .");
            }

            if ($pengambilanMkKprs->status_apv_pengambilan_mk == 1) {
                throw new \Exception("Anda tidak dapat membatalkan pengambilan mata kuliah yang sudah disetujui.");
            }

            $pengambilanMkKprs->delete();
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
            throw new \Exception("Tidak ada pengambilan MK KRS yang ditemukan.");
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

    public function listMahasiswaNeedApproval($id_dosen) {
        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 10);
        $offset = ($page - 1) * $perPage;

        $semesterAktif = Semester::aktif();
        if (!$semesterAktif) {
            throw new \Exception("No active semester found.");
        }

        $dosenWali = DosenWali::where('id_dosen', $id_dosen)
            ->where('id_semester', $semesterAktif->id_semester)
            ->pluck('id_mhs');

        $query = PengambilanMkKprs::whereIn('id_mhs', $dosenWali)
            ->where('id_semester', $semesterAktif->id_semester)
            // ->where('status_apv_pengambilan_mk', 0) // Only those not approved
            ->with(['kelasMk.mataKuliah']);

        $totalCount = $query->count();

        $result = $query->limit($perPage)
            ->offset($offset)
            ->get()
            ->map(function ($item) {
                return [
                    'id_pengambilan_mk_kprs' => $item->id_pengambilan_mk_kprs,
                    'id_mhs' => $item->id_mhs,
                    'nm_mahasiswa' => optional($item->mahasiswa->pengguna)->nm_pengguna,
                    'id_kelas_mk' => $item->id_kelas_mk,
                    'no_kelas_mk' => optional($item->kelasMk)->no_kelas_mk,
                    'nm_mata_kuliah' => optional($item->kelasMk->mataKuliah)->nm_mata_kuliah,
                    'status_approval' => $item->status_apv_pengambilan_mk,
                ];
            });

        return [
            'total_count' => $totalCount,
            'current_page' => $page,
            'per_page' => $perPage,
            'data' => $result,
            'total_pages' => ceil($totalCount / $perPage)
        ];
    }

    public function listCourse($id_dosen, $id_mhs = null)
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
        $query = PengambilanMkKprs::whereIn('id_mhs', $idMahasiswa)
            ->where('id_semester', $semesterAktif->id_semester)
            ->with([
                'kelasMk' => function ($q) {
                    $q->select([
                        'id_kelas_mk',
                        'no_kelas_mk',
                        'id_mata_kuliah',
                    ])
                        ->with([
                            'pengampuMk',
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
                                    ->select(['id_jadwal_jam', 'id_ruangan', 'id_kelas_mk', 'id_jadwal_kelas', 'id_jadwal_hari']);
                            },
                        ]);
                },
                'mahasiswa' => function ($q2) {
                    $q2->select([
                        'id_mhs',
                        'id_pengguna',
                        'id_program_studi',
                    ])
                        ->with([

                            'programStudi.fakultas',
                            'pengguna' => function ($q3) {
                                $q3->select([
                                    'id_pengguna',
                                    'nm_pengguna'
                                ]);
                            }
                        ]);
                }
            ]);

        if (!empty($id_mhs)) {
            $query = $query->where('id_mhs', $id_mhs);
        }
        $pengambilanMkKprs = $query->limit($perPage)
            ->offset($offset)
            ->get()
            ->map(function ($item) {

                $kelasMk = $item->kelasMk ?? new KelasMk();
                $jadwal = $kelasMk->jadwalKelas ?? new JadwalKelas();

                $jadwalAll = [];

                foreach ($jadwal as $jadwalKelas) {
                    $ruangan = !empty($jadwalKelas->ruangan) ? $jadwalKelas->ruangan : new Ruangan();
                    $jadwalJam = !empty($jadwalKelas->jadwalJam) ? $jadwalKelas->jadwalJam : new JadwalJam();
                    $jadwalAll[] = [
                        'nama_ruangan' => $ruangan->nm_ruangan ?? '',
                        'waktu_mulai' => $jadwalJam->waktu_mulai ?? '',
                        'waktu_selesai' => $jadwalJam->waktu_selesai ?? '',
                        'id_jadwal_kelas' => $jadwalKelas->id_jadwal_kelas ?? null,
                        'nama_hari' => $jadwalKelas->nama_hari,
                    ];
                }

                $pengampus = [];
                foreach ($kelasMk->pengampuMk as $pengampuMk) {
                    $pengampus[] = [
                        'id_pengampu_mk' => $pengampuMk->id_pengampu_mk,
                        'id_dosen' => $pengampuMk->dosen?->id_dosen ?? null,
                        'nama_dosen' => $pengampuMk->dosen?->pengguna?->nama_lengkap ?? '',
                    ];
                }

                return [
                    'id_pengambilan_mk_kprs' => $item->id_pengambilan_mk_kprs,
                    'id_mhs' => $item->id_mhs,
                    'id_semester' => $item->id_semester,
                    'nm_mahasiswa' => optional($item->mahasiswa->pengguna)->nm_pengguna,
                    'id_kelas_mk' => optional($item->kelasMk)->id_kelas_mk,
                    'no_kelas_mk' => optional($item->kelasMk)->no_kelas_mk,
                    'jadwal_kelas' => $jadwalAll,
                    'pengampu_mk' => $pengampus,
                    'fakultas' => $item->mahasiswa->programStudi->fakultas->nm_fakultas ?? '-',
                    'program_studi' => $item->mahasiswa->programStudi->nm_program_studi ?? '-',
                    'status_approval' => $item->status_apv_pengambilan_mk,
                    'id_mata_kuliah' => optional($item->kelasMk)->id_mata_kuliah,
                    'nm_mata_kuliah' => !empty($mataKuliah) ? optional($item->kelasMk->mataKuliah)->nm_mata_kuliah : '-',
                    'kredit_tatap_muka' => !empty($mataKuliah) ? optional($item->kelasMk->mataKuliah)->kredit_tatap_muka : '-',
                    'nama_kelas' => !empty($mataKuliah) ? optional($item->kelasMk->nama)->nama_kelas : '-',
                ];
            });;

        return $pengambilanMkKprs;
    }

    public function getHistoryKrsByIdMhs($id_mhs, $id_semester)
    {
        // get dosen wali by id mhs
        $dosenWali = DosenWali::where('id_mhs', $id_mhs)
            ->where('id_semester', $id_semester)
            ->with(['dosen' => function ($q) {
                $q->select(['id_dosen', 'id_pengguna'])
                    ->with(['pengguna' => function ($q2) {
                        $q2->select(['id_pengguna', DB::raw("gelar_depan || ' ' || nm_pengguna || ' ' || gelar_belakang as nama_lengkap")]);
                    }]);
            }])
            ->first();

        // get semester by id semester
        $semester = Semester::find($id_semester);
        if (!$semester) {
            throw new \Exception("Semester with id $id_semester not found.");
        }

        $q = PengambilanMkKprs::where('id_mhs', $id_mhs)
            ->with([
                'kelasMk.programStudi',
                'kelasMk.mataKuliah',
                'kelasMk.jadwalKelas',
                'kelasMk.jadwalKelas.jadwalJam',
                'kelasMk.jadwalKelas.ruangan',
                'kelasMk.jadwalKelas.ruangan.gedung',
                'semester',
            ]);

        if (!empty($id_semester)) {
            $q = $q->whereHas('semester', function ($q2) use ($id_semester) {
                $q2->where('id_semester', $id_semester);
            });
        }

        $data = $q->get()->map(function ($item) {
            $semester = $item->semester ?? new Semester();
            $kelasMk = $item->kelasMk ?? new KelasMk();
            $programStudi = $kelasMk->programStudi ?? new ProgramStudi();
            $mataKuliah = $kelasMk->mataKuliah ?? new MataKuliah();

            $pengampus = [];

            foreach ($kelasMk->pengampuMk as $pengampuMk) {
                $pengampus[] = [
                    'id_pengampu_mk' => $pengampuMk->id_pengampu_mk,
                    'id_dosen' => $pengampuMk->dosen?->id_dosen ?? null,
                    'nama_dosen' => $pengampuMk->dosen?->pengguna?->nama_lengkap ?? '',
                ];
            }

            $jadwal = $kelasMk->jadwalKelas ?? new JadwalKelas();
            $jadwalAll = [];
            foreach ($jadwal as $jadwalKelas) {
                $ruangan = !empty($jadwalKelas->ruangan) ? $jadwalKelas->ruangan : new Ruangan();
                $jadwalJam = !empty($jadwalKelas->jadwalJam) ? $jadwalKelas->jadwalJam : new JadwalJam();;
                $gedung = $ruangan->gedung ?? new Ruangan();
                $jadwalAll[] = [
                    'nama_ruangan' => $ruangan->nm_ruangan ?? '',
                    'waktu_mulai' => $jadwalJam->waktu_mulai ?? '',
                    'waktu_selesai' => $jadwalJam->waktu_selesai ?? '',
                    'id_jadwal_kelas' => $jadwalKelas->id_jadwal_kelas ?? null,
                    'nama_hari' => $jadwalKelas->nama_hari,
                    'nama_gedung' => $gedung->nm_gedung ?? '',
                ];
            }

            return [
                'id_kelas_mk' => $item->id_kelas_mk,
                'status_apv' => $item->status_apv_pengambilan_mk,
                'semester' => $semester->nm_semester,
                'th_semester' => $semester->thn_akademik_semester,
                'program_studi' => $programStudi->nm_program_studi,
                'mata_kuliah' => $mataKuliah->nm_mata_kuliah,
                'jadwal_kelas' => $jadwalAll,
                'pengampu_mk' => $pengampus,
                'sks' => $kelasMk->kredit_semester ?? 0,
            ];
        });

        // get limit for spefic semester
        $limitSks = $this->getLimitSksPerSemester($id_mhs, $id_semester - 1);
        $countKreditSemster = $this->countKreditSemester($id_mhs, $id_semester);

        $mhs = Mahasiswa::find($id_mhs);
        if (!$mhs) {
            throw new \Exception("Mahasiswa with id $id_mhs not found.");
        }

        $krsSubmitting = MahasiswaKrsApprovalSign::where('id_mhs', $id_mhs)
            ->where('id_semester', $id_semester)
            ->first();

        // populate dosen wali and riwayat krs
        $data = [
            'nama' => $mhs->pengguna?->nama_lengkap ?? '',
            'nim' => $mhs->nim ?? '',
            'semester' => $semester->nm_semester,
            'th_semester' => $semester->thn_akademik_semester,
            'program_studi' => $semester->programStudi?->nm_program_studi,
            'status_approval' => !empty($krsSubmitting) && !empty($krsSubmitting->sign_path),
            'limit_sks' => $limitSks,
            'count_kredit_semester' => $countKreditSemster,
            'dosen_wali' => [
                'id_dosen' => $dosenWali->dosen?->id_dosen ?? null,
                'nama_dosen' => $dosenWali->dosen?->pengguna?->nama_lengkap ?? '',
            ],
            'riwayat_krs' => $data,
        ];

        return $data;
    }

    public function validateMahasiswaCanKrsByActiveSemesterAndPrevSemester($id_mhs)
    {
        $semesterAktif = Semester::aktif();
        if (!$semesterAktif) {
            throw new \Exception("Tidak ada semester aktif yang ditemukan.");
        }

        $krsActiveSemester = $this->getTagihanBySemester($id_mhs, $semesterAktif->id_semester);


        $isTrueActiveSemster = empty($krsActiveSemester); //tidak ada tagihan mhs untuk semester aktif

        if (!empty($krsActiveSemester)) {
            // check if the total biaya and denda is less than or equal to total terbayar
            if ($krsActiveSemester->total_besar_biaya + $krsActiveSemester->total_denda_biaya > $krsActiveSemester->total_terbayar) {
                return false;
            }
            $isTrueActiveSemster = true; // ada tagihan mhs untuk semester aktif
        }

        $prevSemester = Semester::prevAktif();
        if (!$prevSemester) {
            // it means he semester awal;
            return true;
        }

        $prevKrsProdi = $this->getTagihanBySemester($id_mhs, $prevSemester->id_semester);

        $isTruePrevSemester = empty($prevKrsProdi); //tidak ada tagihan mhs untuk semester sebelumnya

        if (!empty($prevKrsProdi)) {
            // check if the total biaya and denda is less than or equal to total terbayar
            if ($prevKrsProdi->total_besar_biaya + $prevKrsProdi->total_denda_biaya > $prevKrsProdi->total_terbayar) {
                return false;
            }
            $isTruePrevSemester = true; // ada tagihan mhs untuk semester sebelumnya
        }

        return true && $isTrueActiveSemster && $isTruePrevSemester;
    }

    private function getTagihanBySemester($id_mhs, $id_semester)
    {
        return TagihanMhs::where('id_mhs', $id_mhs)
            ->where('id_semester', $id_semester)
            ->first();
    }

    public function getLimitSksPerSemester($id_mhs, $id_semester)
    {
        $mahasiswa = Mahasiswa::where('id_mhs', $id_mhs)->whereHas(
            'historyNilai',
            function ($query) use ($id_semester) {
                $query->where('id_semester', $id_semester);
            }
        )->first();

        if ($mahasiswa === null) {
            // throw new \Exception("Mahasiswa dengan ID $id_mhs tidak ditemukan atau tidak memiliki riwayat nilai untuk semester $id_semester.");
            Log::error("Mahasiswa dengan ID $id_mhs tidak ditemukan atau tidak memiliki riwayat nilai untuk semester $id_semester.");
            return 24;
        }

        if ($mahasiswa->historyNilai->isEmpty()) {
            return 24; // No grades, so no SKS limit
        }

        $bebanSks = BebanSks::where('ipk_minimum', '<=', $mahasiswa->historyNilai->first()->ipk)
            ->where('id_program_studi', $mahasiswa->id_program_studi)
            ->where('id_fakultas', $mahasiswa->programStudi->id_fakultas)
            ->orderBy('ipk_minimum', 'desc')
            // ->where('id_semester', $id_semester)
            ->first();

        $limitSks = (int)$bebanSks->sks_maksimal ?? 0;
        return $limitSks;
    }

    public function countKreditSemester($id_mhs, $id_semester)
    {
        $countKreditSemster = 0;

        // get kredit semester from pengambilan mk relation kelas mk
        PengambilanMkKprs::where('id_mhs', $id_mhs)
            ->where('id_semester', $id_semester)
            ->with(['kelasMk' => function ($query) {
                $query->select('id_kelas_mk', 'kredit_semester');
            }])
            ->get()
            ->each(function ($pengambilanMk) use (&$countKreditSemster) {
                $kelasMk = $pengambilanMk->kelasMk ?? new KelasMk();

                $countKreditSemster += $kelasMk->kredit_semester;
            });
        return $countKreditSemster;
    }


    public function validatingKreditSemsesterWithLimit($id_mhs, $id_semester)
    {
        $limitSks = $this->getLimitSksPerSemester($id_mhs, $id_semester);
        $countKreditSemster = $this->countKreditSemester($id_mhs, $id_semester);

        if ($countKreditSemster > $limitSks) {
            throw new \Exception("Batas maksimal SKS per semester adalah $limitSks SKS. Anda sudah mengambil $countKreditSemster SKS.");
        }

        return true;
    }
}
