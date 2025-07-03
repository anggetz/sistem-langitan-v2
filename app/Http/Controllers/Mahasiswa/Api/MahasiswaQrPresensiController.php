<?php

namespace App\Http\Controllers\Mahasiswa\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\PengambilanMk;
use App\Models\PresensiKelas;
use App\Models\PresensiMhs;
use Carbon\Carbon;

class MahasiswaQrPresensiController extends Controller
{

    // qr key dari hasil scan qr code yang di generate oleh dosen
    public function qrPresensi() {
        try {
            $qr_key = request()->input('qr_key');
            if (empty($qr_key)) {
                return response()->json([
                    'status' => false,
                    'message' => 'QR key is required'
                ], 400);
            }

            $presensi = PresensiKelas::where('qr_key', $qr_key)
                ->where('qr_expired', '>', Carbon::now()->timezone(env("APP_TIMEZONE", "Asia/Jakarta")))
                ->first();

            if (!$presensi) {
                return response()->json([
                    'status' => false,
                    'message' => 'QR code is invalid or expired'
                ], 404);
            }

            $mhs = Mahasiswa::where('id_pengguna', auth()->user()->id_pengguna)->first();
            if (!$mhs) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mahasiswa not found'
                ], 404);
            }

            // Check if the student is registered in the class
            $pengambilanMk = PengambilanMk::where('id_kelas_mk', $presensi->id_kelas_mk)
                // ->where('id_semester', $presensi->kelasMk->id_semester)
                ->where('id_mhs', $mhs->id_mhs)
                ->with(['kelasMk'])
                ->first();

            // dd($presensi->kelasMk->id_semester, $presensi->id_kelas_mk, $mhs->id_mhs, "test");

            if (!$pengambilanMk) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda tidak terdaftar dikelas ini'
                ], 404);
            }

            $presensiMhs = PresensiMhs::with(['mahasiswa', 'presensiKelas'])
                ->where('id_presensi_kelas', $presensi->id_presensi_kelas)
                ->where('id_mhs', $mhs->id_mhs)
                ->first();

            if (empty($presensiMhs)) {
                $presensiMhs = new PresensiMhs();
                $presensiMhs->id_presensi_kelas = $presensi->id_presensi_kelas;
                $presensiMhs->id_mhs = $mhs->id_mhs;
            }

            $presensiMhs->kehadiran = 1;
            $presensiMhs->save();

            return response()->json([
                'status' => true,
                'message' => 'QR Presensi berhasil',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => `Error mem-validasi qr code`,
                'error' => $e->getMessage()
            ]);
        }
    }
}
