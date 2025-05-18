<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function __construct() {}

    public function profile()
    {
        try {
            $user = \App\Models\Pengguna::with([
                'dosen',
                'dosen.penghargaan',
                'dosen.pengampuMk.kelas_mk' => function ($query) {
                    $query->whereHas('semester', function ($q) {
                        $q->where('status_aktif_semester', 'True');
                    });
                },
                // 'dosen.prestasi',
                'kotaLahir'
            ])->find(auth()->id());

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $dosen = $user->dosen;

            $data = [
                'id'             => $user->id_pengguna,
                'nama_lengkap'   => $user->nm_asli,
                'nip'            => $dosen->nip_dosen,
                'foto'           => $user->foto_pengguna,
                'tempat_lahir'   => $user->kotaLahir->nm_kota ?? '-',
                'tanggal_lahir'  => $dosen->tgl_lahir_pengguna,
                'alamat'         => $dosen->alamat_rumah_dosen,
                'no_hp'          => $dosen->mobile_dosen,
                'status_dosen'   => $dosen->status_dosen,
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
}
