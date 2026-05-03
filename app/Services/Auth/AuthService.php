<?php

namespace App\Services\Auth;

use App\Models\Message;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

abstract class AuthService
{

    /**
     * Melakukan hashing password.
     * Kamu bisa menggantinya dengan algoritma lain (misalnya bcrypt) jika diperlukan.
     */
    public function hashPassword(string $password): string
    {
        return sha1($password);
    }

    /**
     * Method abstract untuk otentikasi.
     * Kelas turunannya harus mengimplementasikan method ini.
     *
     * @param array $credentials
     * @return mixed
     */
    abstract public function authenticate(Request $request);

    // logout
    public function logout(Request $request)
    {
        $request->session()->invalidate();
    }
    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited($request): void
    {
        $username = $request->input('username');
        $ip = $request->ip();
        if (! RateLimiter::tooManyAttempts($this->throttleKey($username, $ip), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($username, $ip));

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey($username, $ip): string
    {
        return Str::transliterate(Str::lower($username) . '|' . $ip);
    }

    protected function isValidPassword($pengguna, $hashedPassword)
    {
        $perguruanTinggi = pt();
        return $pengguna->password_hash === $hashedPassword ||
               $pengguna->password_hash_temp === $hashedPassword ||
               $perguruanTinggi->password_general === $hashedPassword;
    }

    protected function sendFailedResponse($message, $statusCode)
    {
        throw ValidationException::withMessages([
            'username' => 'username atau password tidak sesuai',
        ]);
    }

}
