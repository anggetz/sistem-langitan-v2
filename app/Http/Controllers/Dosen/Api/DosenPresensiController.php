<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\MateriMk;
use App\Models\Message;
use App\Models\PengambilanMk;
use App\Models\PengampuMk;
use App\Models\PresensiKelas;
use App\Models\PresensiMhs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;

class DosenPresensiController extends Controller
{
    public function __construct() {}

    public function listMateriMk(Request $request, $id_kelas_mk)
    {
        try {
            $materiMks = MateriMk::where('id_kelas_mk', $id_kelas_mk)->get();
            return response()->json([
                'status' => true,
                'data' => $materiMks
            ]);
        } catch (\Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil materi mk',
                'error' => $err->getMessage()
            ]);
        }
    }

    public function createPresensi(Request $request)
    {
        try {
            $input = $request->validate([
                'tgl_presensi_kelas' => 'required|date_format:Y-m-d',
                'waktu_mulai' => 'required', // format: HH:mm
                'waktu_selesai' => 'required', // format: HH:mm
                'materi_mk' => 'required',
                'id_kelas_mk' => 'required',
            ]);

            $materi = MateriMk::firstOrCreate([
                'id_kelas_mk' => $input['id_kelas_mk'],
                'isi_materi_mk' => $input['materi_mk'],
                //'tgl_materi_mk' => Carbon::now()
            ]);

            // check if kelas is owned by dosen
            $kelas = PengampuMk::where('id_kelas_mk', $input['id_kelas_mk'])
                ->where('id_dosen', auth()->user()->dosen->id_dosen)
                ->first();

            if (empty($kelas)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kelas tidak ditemukan atau Anda tidak memiliki akses ke kelas ini.'
                ], 400);
            }

            $checkExist = presensiKelas::where('id_kelas_mk', $input['id_kelas_mk'])
                ->whereDate('tgl_presensi_kelas', $input['tgl_presensi_kelas'])
                ->where('waktu_mulai', $input['waktu_mulai'])
                ->where('waktu_selesai', $input['waktu_selesai'])
                ->exists();

            if ($checkExist) {
                return response()->json([
                    'status' => false,
                    'message' => 'Presensi untuk kelas ini pada tanggal dan waktu tersebut sudah ada.'
                ], 400);
            }

            PresensiKelas::create([
                'id_kelas_mk' => $input['id_kelas_mk'],
                'tgl_entry' => now(),
                'tgl_presensi_kelas' => $input['tgl_presensi_kelas'],
                'waktu_mulai' => $input['waktu_mulai'],
                'waktu_selesai' => $input['waktu_selesai'],
                'id_materi_mk' => $materi->id_materi_mk,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Presensi berhasil dibuat',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat presensi',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function MahasiswaKelas(Request $request, $id_kelas, $id_presensi)
    {
        try {
            $now = Carbon::now();

            $mahasiswas = PengambilanMk::select('persen_presensi', 'id_kelas_mk', 'id_mhs')
                ->where('id_kelas_mk', $id_kelas)
                ->with([
                    'mahasiswa' => function ($q) {
                        $q->select('id_mhs', 'id_pengguna')->with([
                            'pengguna' => function ($qPengguna) {
                                $qPengguna->select('id_pengguna', 'nm_pengguna');
                            }
                        ]);
                    },
                ])
                ->get()
                ->map(function ($item) use ($id_kelas, $id_presensi) {
                    $presensiMhs = PresensiMhs::where('id_mhs', $item->id_mhs)
                        ->where('kehadiran', '1')
                        ->whereHas('presensiKelas', function ($q) use ($id_kelas, $id_presensi) {
                            $q->where('id_kelas_mk', $id_kelas);
                            $q->where('id_presensi_kelas', $id_presensi);
                        })
                        ->first();

                    $item->sudah_presensi = !empty($presensiMhs);
                    $item->qr_flag = !empty($presensiMhs) ? $presensiMhs->qr_flag : false;
                    return $item;
                });

            return response()->json([
                'status' => true,
                'message' => 'Data Mahasiswa Kelas',
                'data' => $mahasiswas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data mahasiswa kelas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function listPresensiKelasByIdKelas(Request $request, $id_kelas_mk)
    {
        try {

            $limit = $request->get('perPage', 10);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $limit;

            $presensiKelas = PresensiKelas::where("id_kelas_mk", $id_kelas_mk)
                ->orderBy(DB::raw("TO_DATE(TO_CHAR(tgl_presensi_kelas, 'YYYY-MM-DD') || ' ' || waktu_selesai, 'YYYY-MM-DD HH24:MI')"), 'DESC')
                ->with(['materiMk']);

            $total = $presensiKelas->count();

            $presensiKelas = $presensiKelas->limit($limit)
                ->offset($offset)
                ->get()
                ->map(function ($item) use ($id_kelas_mk) {
                    $totalHadir = PresensiMhs::where('kehadiran', '1')
                        ->whereHas('presensiKelas', function ($q) use ($id_kelas_mk, $item) {
                            $q->where('id_kelas_mk', $id_kelas_mk);
                            $q->where('id_presensi_kelas', $item->id_presensi_kelas);
                        })
                        ->count();

                    $totalMhs = PengambilanMk::where('id_kelas_mk', $id_kelas_mk)
                        ->active()
                        ->groupBy('id_kelas_mk')
                        ->count();

                    $dateTglKelasOneWeek = Carbon::parse($item->tgl_presensi_kelas)->addWeek();

                    $item->is_more_than_one_week = $dateTglKelasOneWeek->lt(Carbon::now());
                    $item->total_hadir = $totalHadir;
                    $item->total_absen = $totalMhs - $totalHadir;
                    return $item;
                });

            return response()->json([
                'status' => true,
                'message' => 'Data Presensi Kelas',
                'data' => $presensiKelas,
                'total' => $total,
                'page' => $page,
                'per_page' => $limit
            ]);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data presensi kelas',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function editPertemuan(Request $request, $id_presensi_kelas)
    {
        try {
            $data = $request->validate([
                'tgl_presensi_kelas' => 'date|date_format:Y-m-d',
                'waktu_mulai' => 'string',
                'waktu_selesai' => 'string',
                'id_kelas_mk' => 'integer',
                'materi_mk' => 'string',
            ]);

            $materi = MateriMk::firstOrCreate([
                'id_kelas_mk' => $data['id_kelas_mk'],
                'isi_materi_mk' => $data['materi_mk'],
                //'tgl_materi_mk' => Carbon::now()
            ]);

            $now = Carbon::now();

            $presensiKelas = PresensiKelas::where('id_presensi_kelas', $id_presensi_kelas)
                                            ->first();

            if (empty($presensiKelas)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Presensi Kelas tidak ada',
                ], 400);
            }

            $tglPresensiKelasCarbon = Carbon::parse($presensiKelas->tgl_presensi_kelas);

            if ($now > $tglPresensiKelasCarbon->addWeek()) {
                 return response()->json([
                    'status' => false,
                    'message' => 'Presensi tidak boleh di edit, sudah melebihi 1 minggu',
                ], 400);
            }

            // check data integrity
            $pengampuMk = PengampuMk::where('id_dosen', auth()->user()->dosen->id_dosen)
                                    ->where('id_kelas_mk', $presensiKelas->id_kelas_mk)
                                    ->first();

            if (empty($pengampuMk)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dosen bukan pengampu mk kelas ini',
                ], 400);
            }

            $data['id_materi_mk'] = $materi->id_materi_mk;
            $presensiKelas->fill($data);
            $presensiKelas->save();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil ubah data Presensi Kelas',
                'data' => $presensiKelas
            ]);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal ubah presensi kelas',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function hapusPertemuan(Request $request, $id_presensi_kelas)
    {
        try {

            $now = Carbon::now();

            $presensiKelas = PresensiKelas::where('id_presensi_kelas', $id_presensi_kelas)
                                            ->first();

            if (empty($presensiKelas)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Presensi Kelas tidak ada',
                ], 400);
            }

            $tglPresensiKelasCarbon = Carbon::parse($presensiKelas->tgl_presensi_kelas);

            if ($now > $tglPresensiKelasCarbon->addWeek()) {
                 return response()->json([
                    'status' => false,
                    'message' => 'Presensi tidak boleh di hapus, sudah melebihi 1 minggu',
                ], 400);
            }

            // check data integrity
            $pengampuMk = PengampuMk::where('id_dosen', auth()->user()->dosen->id_dosen)
                                    ->where('id_kelas_mk', $presensiKelas->id_kelas_mk)
                                    ->first();

            if (empty($pengampuMk)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dosen bukan pengampu mk kelas ini',
                ], 400);
            }


            $presensiKelas->delete();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil hapus data Presensi Kelas',
                'data' => $presensiKelas
            ]);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal hapus data presensi kelas',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function MahasiswaInOut(Request $request, $id_mahasiswa, $id_presensi)
    {
        try {
            $request->validate([
                'kehadiran' => 'required|in:1,0',
            ]);

            // check if inside pengambilanMk;

            $presensiKelas = PresensiKelas::find($id_presensi);

            if (empty($presensiKelas)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Presensi kelas tidak ditemukan',
                ], 400);
            }

            $PengambilanMk = PengambilanMk::where('id_kelas_mk', $presensiKelas->id_kelas_mk)
                ->where('id_mhs', $id_mahasiswa)
                ->exists();

            if (!$PengambilanMk) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mahasiswa tidak mengambil kelas ini',
                ], 400);
            }

            $presensi = PresensiMhs::with(['mahasiswa', 'presensiKelas'])
                ->where('id_presensi_kelas', $id_presensi)
                ->where('id_mhs', $id_mahasiswa)
                ->first();

            if (!$presensi) {
                $presensi = new PresensiMhs();
                $presensi->id_presensi_kelas = $id_presensi;
                $presensi->id_mhs = $id_mahasiswa;
            }

            $presensi->kehadiran = $request->kehadiran;
            // $presensi->jam_presensi = Carbon::now();
            $presensi->save();

            return response()->json([
                'status' => true,
                'message' => 'Data presensi berhasil diperbarui',
                // 'data' => $presensi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data presensi mahasiswa',
                'error' => $e->getMessage()
            ]);
        }
    }
}
