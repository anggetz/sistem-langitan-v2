<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

class DosenController extends Controller
{
    public function __construct() {}

    /**
     * this dev only password please coment this function in production
     */
    // public function resetPasswordDev() {
    //     try {
    //         $pengguna = Pengguna::where('username', '0706045501')->first();
    //         if ($pengguna) {

    //             $pengguna->password_hash = sha1('12345678');
    //             $pengguna->save();
    //         }
    //         return response()->json(['message' => 'Password reset successfully']);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Error resetting password: ' . $e->getMessage()], 500);
    //     }
    // }

    public function profile()
    {
        try {
            $user = \App\Models\Pengguna::with([
                'dosen',
                'dosen.penghargaan',
                'dosen.departemen.departemen',
                'dosen.penelitian',
                'dosen.pengampuMk.kelas_mk' => function ($query) {
                    $query->whereHas('semester', function ($q) {
                        $q->where('status_aktif_semester', 'True');
                    });
                },
                // 'dosen.prestasi',
                'kotaLahir'
            ])->find(auth()->user()->id_pengguna);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $dosen = $user->dosen;

            $data = [
                'id'             => $user->id_pengguna,
                'id_dosen'        => $dosen->id_dosen,
                'nama_lengkap'   => $user->nama_lengkap,
                'nip'            => $dosen->nip_dosen,
                'foto'           => $user->foto_pengguna,
                'tempat_lahir'   => $user->kotaLahir->nm_kota ?? '-',
                'tanggal_lahir'  => $user->tgl_lahir_pengguna,
                'alamat'         => $dosen->alamat_rumah_dosen,
                'no_hp'          => $dosen->mobile_dosen,
                'status_dosen'   => $dosen->status_dosen,
                'departemen'   => $dosen->departemen->departemen->nm_departemen ?? '-',
                'penelitian'    => $dosen->penelitian->map(function ($item) {
                    return [
                        'judul_penelitian' => $item->judul,
                        'tahun'            => $item->tahun,
                        'tgl_input'            => $item->tgl_input,
                    ];
                })->values(),
                'pengampu_mk'    => $dosen->pengampuMk->filter(function ($item) {
                    return $item->kelas_mk
                        && $item->kelas_mk->semester
                        && $item->kelas_mk->semester->status_aktif_semester;
                })->map(function ($item) {
                    $mataKuliah = $item->kelas_mk->mataKuliah;
                    return [
                        'nama_mk' => $mataKuliah->nm_mata_kuliah ?? '-',
                    ];
                })->values(),
                'penghargaan'    => $dosen->penghargaan,
            ];

            return response()->json([
                'message' => Message::OK,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function EditProfile(Request $request)
    {
        try {
            $request->validate([
                'nm_pengguna' => 'required|string|max:255',
                'no_hp' => 'required|string|max:15',
                'alamat_rumah_dosen' => 'required|string|max:255',
                "kota_lahir" => 'required|exists:kota,id_kota',
                'tgl_lahir_pengguna' => 'required|date',
            ]);
            $user = \App\Models\Pengguna::with(['dosen'])->find(auth()->user()->id_pengguna);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            DB::beginTransaction();

            $dosen = $user->dosen;

            $user->nm_pengguna = $request->input('nm_pengguna');
            $dosen->mobile_dosen = $request->input('no_hp');
            $dosen->alamat_rumah_dosen = $request->input('alamat_rumah_dosen');
            $user->id_kota_lahir = $request->input('kota_lahir');
            $user->tgl_lahir_pengguna = $request->input('tgl_lahir_pengguna');

            $dosen->save();
            $user->save();

            DB::commit();
            return response()->json([
                'message' => Message::OK,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function EditPhotoProfile(Request $request)
    {
        try {
            $request->validate([
                'foto_pengguna' => 'required|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
            ]);
            $user = \App\Models\Pengguna::with(['dosen'])->find(auth()->id());
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Handle file upload
            if ($request->hasFile('foto_pengguna')) {
                // Hapus foto lama jika ada
                if ($user->foto_pengguna && Storage::disk('public')->exists($user->foto_pengguna)) {
                    Storage::disk('public')->delete($user->foto_pengguna);
                }

                $path = $request->file('foto_pengguna')->store('photos', 'public');
                $user->foto_pengguna = $path;
            }

            $user->save();

            return response()->json([
                'message' => Message::OK,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
