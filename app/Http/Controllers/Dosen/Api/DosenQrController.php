<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\PengambilanMk;
use App\Models\PresensiKelas;
use App\Models\PresensiMhs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
}
