<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\PengambilanMk;
use App\Models\PresensiMhs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;

class DosenPresensiController extends Controller
{
    public function __construct() {}

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
