<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function sendOTPForgotPassword(Request $request)
    {
        $request->validate([
                'username' => 'required|string',
                'email' => 'required|email',
                'tgllahir' => 'required|date_format:Y-m-d'
            ]);

        $user = Pengguna::where('username', $request->username)
            ->where('email_pengguna', $request->email)
            ->where('tgl_lahir_pengguna', $request->tgllahir)
            ->first();
        if (!$user) {
            Log::error('User not found for forgot password', ['username' => $request->username, 'email' => $request->email, 'tgllahir' => $request->tgllahir]);
             return response()->json([
                'status' => Message::OK,
                'message' => 'OTP has been sent to your email.'
            ]);
        }

        // send the otp to email
        $otp = rand(100000, 999999);
        $user->otp_forgot_password = $otp;
        $user->expired_otp_forgot_password = now()->addMinutes(10);
        $user->save();

        try {
            Mail::to($user->email_pengguna)->send(new \App\Mail\ForgotPasswordOtpEmail($otp));
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Failed to send OTP email. Please try again later.'
            ], 500);
        }
        return response()->json([
            'status' => Message::OK,
            'message' => 'OTP has been sent to your email.'
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

        if (!$user) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Invalid OTP or OTP has expired.'
            ], 400);
        }



        return response()->json([
            'status' => Message::OK,
            'message' => 'OTP is valid.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
            'new_password_confirmation' => 'required|string|min:6',
            'old_password' => 'required|required|string|min:6',
        ]);

        $user = Pengguna::
            where('username', $request->username)
            ->where('otp_forgot_password', $request->otp)
            ->where('expired_otp_forgot_password', '>', now())
            ->first();
        if (!$user) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'User not found or OTP is not valid or OTP is Expired.'
            ], 404);
        }
        $old_password_hash = sha1($request->old_password);

        // validate the old password
        if ($user->password_hash !== $old_password_hash) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Old password is incorrect.'
            ], 400);
        }

        // update the otp and expired_otp_forgot_password to null
        $user->otp_forgot_password = null;
        $user->expired_otp_forgot_password = null;
        $user->password_hash = sha1($request->new_password);
        $user->save();

        return response()->json([
            'status' => Message::OK,
            'message' => 'Password has been reset successfully.'
        ]);
    }
}
