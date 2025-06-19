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

use Illuminate\Support\Facades\Storage;

class DosenPresensiController extends Controller
{
    public function __construct() {}

    public function createPresensi(Request $request)
    {
        try {
            $request->validate([
                'tgl_presensi_kelas' => 'required|date_format:Y-m-d',
                'waktu_mulai' => 'required', // format: HH:mm
                'waktu_selesai' => 'required', // format: HH:mm
                'id_materi_mk' => 'required',
                'id_kelas_mk' => 'required',
            ]);

            // check if kelas is owned by dosen
            $kelas = PengampuMk::where('id_kelas_mk', $request->id_kelas_mk)
                ->where('id_dosen', auth()->user()->dosen->id_dosen)
                ->first();

            if (empty($kelas)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kelas tidak ditemukan atau Anda tidak memiliki akses ke kelas ini.'
                ]);
            }

            $checkExist = presensiKelas::where('id_kelas_mk', $request->id_kelas_mk)
                ->whereDate('tgl_presensi_kelas', $request->tgl_presensi_kelas)
                ->where('waktu_mulai', $request->waktu_mulai)
                ->where('waktu_selesai', $request->waktu_selesai)
                ->exists();

            if ($checkExist) {
                return response()->json([
                    'status' => false,
                    'message' => 'Presensi untuk kelas ini pada tanggal dan waktu tersebut sudah ada.'
                ]);
            }

            // check id materi mk is valid

            $isMateriValid = MateriMk::where('id_materi_mk', $request->id_materi_mk)
                ->where('id_kelas_mk', $request->id_kelas_mk)
                ->exists();

            if (!$isMateriValid) {
                return response()->json([
                    'status' => false,
                    'message' => 'Materi tidak ditemukan atau tidak terkait dengan kelas ini.'
                ]);
            }

            PresensiKelas::create([
                'id_kelas_mk' => $request->id_kelas_mk,
                'tgl_entry' => now(),
                'tgl_presensi_kelas' => $request->tgl_presensi_kelas,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'id_materi_mk' => $request->id_materi_mk,
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

    public function MahasiswaKelas(Request $request, $id_kelas)
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
                    }
                ])
                ->get()
                ->map(function ($item) use ($id_kelas) {
                    $sudahPresensi = PresensiMhs::where('id_mhs', $item->id_mhs)
                        ->whereHas('presensiKelas', function ($q) use ($id_kelas) {
                            $q->where('id_kelas_mk', $id_kelas);
                        })
                        ->exists();

                    $item->sudah_presensi = $sudahPresensi;
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
            ]);
        }
    }


    public function MahasiswaInOut(Request $request, $id_mahasiswa, $id_presensi)
    {
        try {
            $request->validate([
                'kehadiran' => 'required|in:1,0',
            ]);

            $presensi = PresensiMhs::with(['mahasiswa', 'presensiKelas'])
                ->where('id_presensi_kelas', $id_presensi)
                ->where('id_mhs', $id_mahasiswa)
                ->first();

            if (!$presensi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data presensi tidak ditemukan',
                ]);
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
