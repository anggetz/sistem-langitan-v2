<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Events\QrGenerateEvent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\PengambilanMk;
use App\Models\PresensiKelas;
use App\Models\PresensiMhs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Tymon\JWTAuth\JWT;

class DosenQrController extends Controller
{
    public function __construct() {}

    public function GenerateQR(Request $request, $id_presensi)
    {
        try {

            $presensi = PresensiKelas::find($id_presensi);
            if (!$presensi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Presensi not found'
                ], 400);
            }

            $data =  sha1($presensi->id_presensi_kelas . $presensi->id_kelas_mk . $presensi->id_materi_mk);

            $presensi->qr_key = $data;
            $presensi->qr_expired = Carbon::now()->timezone(env("APP_TIMEZONE", "Asia/Jakarta"))->addMinutes((int)env('QR_EXPIRED', 5)); // Set QR code expiration time
            $presensi->save();

            // broadcast the message;
            // event(new QrGenerateEvent($presensi->id_presensi_kelas, $presensi->id_kelas_mk, $presensi->qr_key));
            $response = Http::post(env('WS_HOOK_ADDRESS').'/broadcast', [ // Changed endpoint to /broadcast
                'topic' => 'qr-generator-' . $presensi->id_presensi_kelas, // Use the topic for the specific presensi
                'message' => json_encode([
                    'qr_key' => $presensi->qr_key,
                    'id_presensi_kelas' => $presensi->id_presensi_kelas,
                    'id_kelas_mk' => $presensi->id_kelas_mk,
                ]),
            ]);

            // masuk queue

            Log::info("triggered event triggerQrGenerator");

            // Option 1: Direct QR Code Image in view
            $qrSvg = QrCode::format('svg')->size(200)->generate($data);

            // Encode agar tidak rusak saat dikirim via JSON
            $base64Svg = base64_encode($qrSvg);

            return response()->json([
                'status' => true,
                'message' => 'QR code generated successfully',
                'data' => 'data:image/svg+xml;base64,' . $base64Svg
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => `Error generating QR code`,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function ShowCurrentQR(Request $request, $id_presensi)
    {
        try {

            $presensi = PresensiKelas::find($id_presensi);
            if (!$presensi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Presensi not found'
                ], 400);
            }

            if ($presensi->qr_key == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'QR belum di generate'
                ], 400);
            }

            // event(new QrGenerateEvent($presensi->id_presensi_kelas, $presensi->id_kelas_mk));

            // Option 1: Direct QR Code Image in view
            $qrSvg = QrCode::format('svg')->size(200)->generate($presensi->qr_key);

            // Encode agar tidak rusak saat dikirim via JSON
            $base64Svg = base64_encode($qrSvg);

            return response()->json([
                'status' => true,
                'message' => 'QR code generated successfully',
                'data' => 'data:image/svg+xml;base64,' . $base64Svg
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => `Error generating QR code`,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function ResetQR(Request $request, $id_presensi)
    {
        try {

            $presensi = PresensiKelas::find($id_presensi);
            if (!$presensi) {
                return response()->json([
                    'status' => false,
                    'message' => 'Presensi not found'
                ], 400);
            }

            $presensi->qr_key = "";
            $presensi->save();

            return response()->json([
                'status' => true,
                'message' => 'QR code resetted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => `Error reset QR code`,
                'error' => $e->getMessage()
            ]);
        }
    }
}
