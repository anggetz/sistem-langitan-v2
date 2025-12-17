<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Pengguna;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function sendOTPForgotPassword(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'tgllahir' => 'required|date_format:Y-m-d',
        ]);

        $user = Pengguna::where('username', $request->username)
            ->where('tgl_lahir_pengguna', $request->tgllahir)
            ->first();
        if (! $user) {
            Log::error('User not found for forgot password', ['username' => $request->username, 'email' => $request->email, 'tgllahir' => $request->tgllahir]);

            return response()->json([
                'status' => Message::OK,
                'message' => 'OTP has been sent to your email.',
            ]);
        }

        $emailAddress = $user->email_pengguna ?? $user->email_alternate;

        if (! $emailAddress) {
            Log::error('Failed to send OTP email, user do not have email address');

            return response()->json([
                'status' => Message::FAIL,
                'timestamp' => Carbon::now(),
                'message' => 'Email belum diset. Hubungi Admin.',
            ], 400);
        }

        $timePart = substr($user->otp_requested_at ?? now('UTC')->addMinutes(1), 11, 8); // '05:13:07'

        // This creates a NEW Carbon instance with the desired time and timezone
        $newUtcCarbon = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            '2025-09-07 '.$timePart,
            'UTC'
        );

        $result = DB::select("
            SELECT
                FROM_TZ(CAST(otp_requested_at AS TIMESTAMP), 'UTC') AT TIME ZONE 'UTC' AS otp_requested_at_utc
            FROM
                pengguna
            WHERE
                id_pengguna = ?
                AND SYSTIMESTAMP < FROM_TZ(CAST(otp_requested_at AS TIMESTAMP), 'UTC') AT TIME ZONE 'UTC'
        ", [$user->id_pengguna]);

        if (count($result) > 0) {
            return response()->json([
                'status' => Message::FAIL,
                'timestamp' => $newUtcCarbon,
                'message' => 'You can only request OTP once every 60 seconds.',
            ], 400);
        }
        // send the otp to email
        $otp = rand(100000, 999999);
        $user->otp_forgot_password = $otp;
        $user->expired_otp_forgot_password = now()->addMinutes(10);
        $user->otp_requested_at = now('UTC')->addMinutes(1);
        $user->save();

        try {
            Mail::to($emailAddress)->queue(new \App\Mail\ForgotPasswordOtpEmail($otp));
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => Message::FAIL,
                'otp' => $otp,
                'timestamp' => now('UTC')->addMinutes(1),
                'message' => 'Failed to send OTP email. Please try again later.',
            ], 500);
        }

        return response()->json([
            'status' => Message::OK,
            'otp' => $otp,
            'timestamp' => now('UTC')->addMinutes(1),
            'message' => 'OTP has been sent to your email.',
        ]);
    }

    // validate the otp
    public function validateOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string',
        ]);

        $user = Pengguna::where('otp_forgot_password', $request->otp)
            ->where('expired_otp_forgot_password', '>', now())
            ->first();

        if (! $user) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Invalid OTP or OTP has expired.',
            ], 400);
        }

        return response()->json([
            'status' => Message::OK,
            'message' => 'OTP is valid.',
            'data' => [
                'nm_pengguna' => substr($user->nm_pengguna, 0, 5).'*************',
            ],
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
            'new_password_confirmation' => 'required|string|min:6',
        ]);

        $user = Pengguna::where('username', $request->username)
            ->where('otp_forgot_password', $request->otp)
            ->where('expired_otp_forgot_password', '>', now())
            ->first();
        if (! $user) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'User not found or OTP is not valid or OTP is Expired.',
            ], 404);
        }

        // update the otp and expired_otp_forgot_password to null
        $user->otp_forgot_password = null;
        $user->expired_otp_forgot_password = null;
        $user->password_hash = sha1($request->new_password);
        $user->save();

        return response()->json([
            'status' => Message::OK,
            'message' => 'Password has been reset successfully.',
        ]);
    }
}
