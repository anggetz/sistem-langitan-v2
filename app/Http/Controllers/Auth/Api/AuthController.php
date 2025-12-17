<?php

namespace App\Http\Controllers\Auth\Api;

use Carbon\Carbon;
use App\Models\Message;
use App\Models\Pengguna;
use Illuminate\Support\Str;
use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use App\Models\PerguruanTinggi;
use App\Services\Auth\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;

class AuthController extends Controller
{

    public function __construct(protected AuthService $authService)
    {
    }
    public function login(LoginRequest $request)
    {

        $resAuth = $this->authService->authenticate($request);

        // Convert object to array
        $data = json_decode(json_encode($resAuth), true)['original'];

        // Rename the key
        if (isset($data['expired_at'])) {
            $data['expired_time'] = $data['expired_at'];
            unset($data['expired_at']);
        }

        return  $data;


        // $perguruanTinggi = pt();
        // $credentials = $request->credentials();

        // $pengguna = Pengguna::where([
        //     'id_perguruan_tinggi' => $perguruanTinggi->id_perguruan_tinggi,
        //     'username'             => $credentials['username']
        // ])->first();

        // if (!$pengguna) {
        //     return $this->sendFailedResponse('Username atau Password tidak sesuai', 401);
        // }

        // if (!$this->isValidPassword($pengguna, $perguruanTinggi, $credentials['hashed_password'])) {
        //     return $this->sendFailedResponse('Username atau Password tidak sesuai', 401);
        // }

        // if ($pengguna->password_must_change == 1) {
        //     return $this->sendFailedResponse('Password harus diganti', 400);
        // }

        // $token = auth()->guard('api')->login($pengguna);

        // return response()->json([
        //     'status'     => Message::OK,
        //     'token'      => $token,
        //     'expired_at' => auth()->guard('api')->factory()->getTTL() * 60,
        // ]);
    }


    public function destroy()
    {

        auth()->logout();
        return response()->json([
            'status' => Message::OK,
            'message' => 'Berhasil Logout'
        ], 200);
    }

    public function refreshToken(Request $request)
    {
        return response()->json([
            'status' => Message::OK,
            'token' => auth()->refresh(),
            'expired_at' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'tanggal_lahir' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Terjadi kesalahan input',
                'errors' => $validator->errors()
            ], 400);
        }

        // cek data pengguna berdasarkan username dan tgl lahir
        $pengguna = Pengguna::where([
            'id_perguruan_tinggi' => $request->id_perguruan_tinggi,
            'username' => $request->input('username'),
            'tgl_lahir_pengguna' => $request->input('tanggal_lahir')
        ])->first();

        // jika data tidak sesuai / tidak ditemukan
        if (!$pengguna) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Username / tanggal lahir tidak sesuai'
            ], 400);
        }

        // jika belum mengisi email alternatif
        if (!$pengguna->email_alternate) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Email belum diset. Silahkan hubungi DSI'
            ], 400);
        }

        // Proses Reset Password
        $password = Str::random(6);
        $pengguna->password_hash_temp = sha1($password);
        $pengguna->password_must_change = 1;
        $pengguna->password_hash_temp_expired = Carbon::now()->modify('+60 minutes')->unix();
        $pengguna->save();

        $token = Crypt::encryptString($pengguna->id_pengguna);

        $host = config('app.host_front_end');
        $url = "$host/auth/forgot-password?id=$token";
        Mail::to($pengguna->email_alternate)->send(new ForgotPassword($url));

        return response()->json([
            'status' => Message::OK,
            'message' => 'Password sudah direset. Silahkan buka email ' . $pengguna->email_alternate
        ]);
    }

    public function getInfoResetPassword(Request $request)
    {
        $validator = Validator::make($request->only("id"), ['id' => 'required']);
        if ($validator->fails())
            return response()->json([
                'status' => Message::FAIL,
                'errors' => $validator->messages()
            ], 400);

        try {
            $id = $request->get("id");
            $id_pengguna = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => "id tidak valid"
            ], 400);
        }
        $dataPengguna = Pengguna::select("nm_pengguna", "username", "email_alternate")
            ->where("id_pengguna", $id_pengguna)->first();

        if (!$dataPengguna)
            return response()->json([
                'status' => Message::FAIL,
                'message' => "Pengguna tidak ditemukan"
            ], 400);

        return response()->json([
            'status' => Message::OK,
            'data' => $dataPengguna
        ], 200);
    }

    public function resetPassword(Request $request)
    {

        $data = $request->only('id', 'username', 'new_password', 'confirm_password');
        $validator = Validator::make($data, [
            'id' => 'required',
            'username' => 'required',
            'new_password' => 'required|min:6|max:50|different:username',
            'confirm_password' => 'required|min:6|max:50|same:new_password'
        ]);

        // jika validasi gagal/error
        if ($validator->fails()) {
            return response()->json([
                'status' => Message::FAIL,
                'errors' => $validator->messages()
            ], 400);
        }

        $id = Crypt::decryptString($request->input("id"));
        $username = $request->input("username");
        $new_password = sha1($request->input('new_password'));
        $pengguna = Pengguna::select("nm_pengguna", "username", "email_alternate")
            ->where([
                'id_perguruan_tinggi' => $request->id_perguruan_tinggi,
                'username' => $username,
                "id_pengguna" => $id
            ]);
        $dataPengguna = $pengguna->first();

        // jika pengguna tidak ada
        if (!$dataPengguna) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => "Pengguna tidak ditemukan"
            ], 400);
        }

        try {
            // update password
            $pengguna->update(['password_must_change' => 0, 'password_hash' => $new_password]);
            return response()->json([
                'status' => Message::OK,
                'message' => 'Passsword baru berhasil disimpan',
                'data' => $pengguna->get()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Message::FAIL,
                'errors' => $e->getMessage()
            ], 400);
        }
    }

    // fitur ganti password ketika sudah login
    public function gantiPassword(Request $request)
    {
        $data = $request->only('old_password', 'new_password', 'confirm_password');
        $validator = Validator::make($data, [
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|min:6|max:50|same:new_password'
        ]);

        // jika validasi gagal/error
        if ($validator->fails()) {
            return response()->json([
                'status' => Message::FAIL,
                'errors' => $validator->messages()
            ], 400);
        }

        $pengguna = $request->user();
        $old_password = sha1($request->input('old_password'));
        $new_password = sha1($request->input('new_password'));

        // jika password lama tidak sama dengan yg tersimpan di database
        if ($pengguna->password_hash != $old_password) {
            return response()->json([
                'status' => Message::FAIL,
                'errors' => "Password lama salah"
            ], 400);
        }

        try {
            $pengguna->update(['password_hash' => $new_password]);
            return response()->json([
                'status' => Message::OK,
                'message' => 'Passsword baru berhasil disimpan',
                'data' => $pengguna
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Message::FAIL,
                'errors' => $e->getMessage()
            ], 400);
        }
    }
}
