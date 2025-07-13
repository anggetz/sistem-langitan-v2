<?php
namespace App\Http\Controllers\Firebase\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Pengumuman;
use App\Services\FcmService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FcmController extends Controller
{

    protected FcmService $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    // store the fcm token for the user when user after login!
    public function storeFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string|max:255',
        ]);

        // check if fcm token already exists
        $penggunaWithSameFcmToken = Pengguna::where('fcm_token', $request->input('fcm_token'));
        // if exists, update the token to empty
        if ($penggunaWithSameFcmToken->exists()) {
            $penggunaWithSameFcmToken->update(['fcm_token' => null]);
        }

        $user = auth()->user();
        $user->fcm_token = $request->input('fcm_token');
        $user->save();

        return response()->json(['message' => 'FCM token updated successfully.'], 200);
    }

    public function sendNotificationByPengumumanId(Request $request)
    {
        $id = $request->input('id');

        if (empty($id)) {
             return response()->json([
                'message' => 'id is required',
                'error' => 'id is required'
            ], 400);
        }

        try {
            $pengumuman = Pengumuman::findOrFail($id);

            $title = $pengumuman->title;
            $body = $pengumuman->deskripsi;

            // get all pengguna with fcm token is not null;
            Pengguna::whereNotNull('fcm_token')
                ->chunk(100, function ($users) use ($title, $body, $pengumuman) {
                    foreach ($users as $user) {
                        Log::info("Sending notification to user: {$user->id_pengguna} with FCM token: {$user->fcm_token}");
                        $this->fcmService->send(
                            $user->fcm_token,
                            'token',
                            $title,
                            $body,
                            [
                                "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                                "pengumuman_id" => (string)$pengumuman->id_pengumuman,
                            ]
                        );
                    }
                });

            return response()->json([
                'status' => 'success',
                'message' => 'Notification sent successfully.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Could not send notification: ' . $e->getMessage(),
            ], 500);
        }


    }
}
