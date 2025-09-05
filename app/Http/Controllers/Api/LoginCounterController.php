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

class LoginCounterController extends Controller
{
    public function setLoginCounter(Request $request)
    {
        $user = Pengguna::find(Auth::id());

        if ($user->login_counter === null) {
            $user->login_counter = 0;
            $user->save();
        }

        if ($user->login_counter == 0) {
            return response()->json([
                'status' => Message::OK,
                'redirect' => 'change-password',
                'message' => 'Please change your password first',
            ], 200);
        }

        // Increment the login counter
        $user->login_counter = $user->login_counter ? $user->login_counter + 1 : 1;
        $user->save();

        return response()->json([
            'status' => Message::OK,
            'message' => 'Login counter updated',
            'login_counter' => $user->login_counter
        ]);
    }

    public function changePasswordLoginCounterZero(Request $request)
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

        if ($user->login_counter === null) {
            $user->login_counter = 0;
            $user->save();
        }

        if ($user->login_counter > 0) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Password change not allowed. Login counter is not zero.',
            ], 400);
        }

        // Update the user's password
        $user->password_hash = sha1($request->password);
        // Set login counter to 1 after password change
        $user->login_counter = 1;
        $user->save();

        return response()->json([
            'status' => Message::OK,
            'message' => 'Password changed successfully. You can now log in.',
        ]);
    }
}
