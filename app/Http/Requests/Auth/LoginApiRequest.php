<?php

namespace App\Http\Requests\Auth;

use App\Models\Pengguna;
use Illuminate\Support\Str;
use App\Models\PerguruanTinggi;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }
    public function credentials()
    {
        return [
            'username'      => $this->input('username'),
            // Catatan: Gunakan hashing yang lebih aman untuk production
            'hashed_password' => sha1($this->input('password')),
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $username = $this->username;
        $password = sha1($this->password);

        $user = Pengguna::query()
            ->where('username', $username)
            ->first();

        $idPerguruanTinggi = config('app.id_perguruan_tinggi_default');
        $perguruanTinggi = PerguruanTinggi::query()
            ->where(['id_perguruan_tinggi' => $idPerguruanTinggi])
            ->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

        if (
            $user->password_hash == $password ||
            $perguruanTinggi->password_general == $password
        ) {
            RateLimiter::clear($this->throttleKey());
            return $user->createToken($this->username)->plainTextToken;
        } else {

            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
