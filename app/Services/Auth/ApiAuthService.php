<?php

namespace App\Services\Auth;

use App\Models\Message;
use App\Models\Pengguna;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\RateLimiter;

class ApiAuthService extends AuthService
{
    public function authenticate($request)
    {
        $this->ensureIsNotRateLimited($request);

        $hashed_password = $this->hashPassword($request->password);

        $pengguna = Pengguna::query()
            ->where(['username' => $request->username])
            ->first();

        if (!$pengguna) {
            RateLimiter::hit($this->throttleKey($request->username, $request->ip()));
            $this->sendFailedResponse('Username atau Password tidak sesuai', 401);
        }

        if (!$this->isValidPassword($pengguna, $hashed_password)) {
            RateLimiter::hit($this->throttleKey($request->username, $request->ip()));
            $this->sendFailedResponse('Username atau Password tidak sesuai', 401);
        }

        if ($pengguna->password_must_change == 1) {
            $this->sendFailedResponse('Password harus diganti', 400);
        }

        $token = auth()->guard('api')->login($pengguna);
        RateLimiter::clear($this->throttleKey($request->username, $request->ip()));

        return response()->json([
            'status'     => true,
            'token'      => $token,
            'expired_at' => auth()->guard('api')->factory()->getTTL() * 60,
        ]);
    }

}
