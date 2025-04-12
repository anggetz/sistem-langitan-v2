<?php

namespace App\Services\Auth;

use App\Models\Pengguna;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class WebAuthService extends AuthService
{
    public function authenticate($request)
    {

        $this->ensureIsNotRateLimited($request);

        $hashed_password = $this->hashPassword($request->password);

        $pengguna = $this->getPengguna($request->username);

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

        Auth::login($pengguna);
        $request->session()->regenerate();
        RateLimiter::clear($this->throttleKey($request->username, $request->ip()));
    }
    public function getPengguna($username)
    {
        return Pengguna::query()
            ->where(['username' => $username])
            ->first();
    }
}
