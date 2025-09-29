<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FreshLoginController extends Controller
{
    public function validateDefaultPassword(Request $request)
    {
        $user = Pengguna::find(Auth::id());

        $defExpectPasswordValue = null;

        if ($user->mahasiswa) {
            $defExpectPasswordValue = sha1($user->mahasiswa->nim_mhs);
        } elseif ($user->dosen) {
            $defExpectPasswordValue = sha1($user->dosen->nidn_dosen);
        }

        if (!$defExpectPasswordValue) {
            return response()->json([
                'status' => Message::OK,
                'message' => 'User role not recognized for default password check.',
            ], 200);
        }

        if ($user->password_hash === $defExpectPasswordValue) {
             return response()->json([
                'status' => Message::OK,
                'redirect' => 'change-password',
                'message' => 'Please change your password first',
            ], 200);
        }

        return response()->json([
            'status' => Message::OK,
            'message' => 'Login is valid',
        ]);
    }

    public function changePasswordDefault(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $user = Pengguna::find(Auth::id());

        $mahasiswa = $user->mahasiswa;

        if ($user->password_hash != sha1($mahasiswa->nim_mhs)) {
              return response()->json([
                'status' => Message::FAIL,
                'message' => 'Password change not allowed. Password is not default.',
            ], 400);
        }

        // Update the user's password
        $user->password_hash = sha1($request->password);
        $user->save();

        return response()->json([
            'status' => Message::OK,
            'message' => 'Password changed successfully. You can now log in.',
        ]);
    }
}
