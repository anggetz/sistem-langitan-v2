<?php

namespace App\Services\Auth;

use App\Models\Message;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use App\Models\SessionPengguna;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Auth;

class SessionAuthService extends AuthService
{
    public function authenticate(Request $request)
    {

        $id_session = $request->id_session;
        $sessionPengguna = SessionPengguna::find($id_session);

        if (!$sessionPengguna) {
            $this->sendFailedResponse('Username atau Password tidak sesuai', 401);
        }

        $perguruanTinggi = pt();

        $pengguna = $sessionPengguna->pengguna()
            ->where("id_perguruan_tinggi", $perguruanTinggi->id_perguruan_tinggi)
            ->first();

        if (!$pengguna) {
            $this->sendFailedResponse('Pengguna tidak ditemukan ', 401);
        }


        if ($pengguna->password_must_change == 1) {
            $this->sendFailedResponse('Password harus diganti', 400);
        }

        Auth::login($pengguna);
        $request->session()->regenerate();
    }
}
