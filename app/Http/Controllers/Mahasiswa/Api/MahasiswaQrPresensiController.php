<?php

namespace App\Http\Controllers\Mahasiswa\Api;

use App\Events\QrGenerateEvent;
use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\PengambilanMk;
use App\Models\PresensiKelas;
use App\Models\PresensiMhs;
use App\Models\Semester;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MahasiswaQrPresensiController extends Controller
{

    public function HistoryAbsenByIdKelas(Request $request, $id_kelas_mk) {
        try {
            $semester = $request->input('semester', null);

            $limit = $request->get('perPage', 10);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $limit;

            $presensiKelas = PresensiKelas::where("id_kelas_mk", $id_kelas_mk)
                ->orderBy(DB::raw("TO_DATE(TO_CHAR(tgl_presensi_kelas, 'YYYY-MM-DD') || ' ' || waktu_selesai, 'YYYY-MM-DD HH24:MI')"), 'DESC')
                ->with(['materiMk']);

            if (!empty($semester)) {
                $presensiKelas->whereHas('kelasMk', function($q) use ($semester) {
                    $q->where('id_semester', $semester);
                });
            }

            $total = $presensiKelas->count();

            $presensiKelas = $presensiKelas
                ->offset($offset)
                ->limit($limit)
                ->get()
                ->map(function ($item) use ($id_kelas_mk) {
                    $presensiMhs = PresensiMhs::where('id_mhs', auth()->user()->mahasiswa->id_mhs)
                        ->where('kehadiran', '1')
                        ->where('id_presensi_kelas', $item->id_presensi_kelas)
                        ->first();

                    $item->sudah_presensi = !empty($presensiMhs);
                    $item->qr_flag = !empty($presensiMhs) ? $presensiMhs->qr_flag : false;
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

            DB::beginTransaction();

            $presensi = PresensiKelas::where('qr_key', $qr_key)
                ->where('qr_expired', '>', Carbon::now()->timezone(env("APP_TIMEZONE", "Asia/Jakarta")))
                ->lockForUpdate()
                ->first();

            if (!$presensi) {
                return response()->json([
                    'status' => false,
                    'message' => 'QR code is invalid or expired'
                ], 404);
            }

            $mhs = Mahasiswa::where('id_pengguna', auth()->user()->id_pengguna)->first();
            if (!$mhs) {
                DB::rollBack();
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
                DB::rollBack();
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
            $presensiMhs->qr_flag = 1;
            $presensiMhs->save();

            // auto regenarete the qr
            $presensi->qr_key = sha1($presensi->id_presensi_kelas . $presensi->id_kelas_mk . $presensi->id_materi_mk . uniqid('qr-uniqid'));
            $presensi->qr_expired = Carbon::now()->timezone(env("APP_TIMEZONE", "Asia/Jakarta"))->addMinutes((int)env('QR_EXPIRED', 5)); // Set QR code expiration time
            $presensi->save();

            // event(new QrGenerateEvent($presensi->id_presensi_kelas, $presensi->id_kelas_mk, $presensi->qr_key));
            $response = Http::post(env('WS_HOOK_ADDRESS').'/broadcast', [ // Changed endpoint to /broadcast
                'topic' => 'qr-generator-' . $presensi->id_presensi_kelas, // Use the topic for the specific presensi
                'message' => 'triggerQrGenerator',
            ]);


            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'QR Presensi berhasil',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => `Error mem-validasi qr code`,
                'error' => $e->getMessage()
            ]);
        }
    }
}
