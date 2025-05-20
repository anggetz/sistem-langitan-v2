<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\PengambilanMk;
use App\Models\PresensiMhs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;

class DosenPresensiController extends Controller
{
    public function __construct() {}

    public function MahasiswaKelas(Request $request, $id_kelas)
    {


        try {
            $now = Carbon::now();
            $minutesAgo = $now->subMinutes(5)->format('Y-m-d H:i:s');

            $mahasiswas = PengambilanMk::select('persen_presensi', 'id_kelas_mk', 'id_mhs')
                ->where('id_kelas_mk', $id_kelas)
                ->with([
                    'mahasiswa' => function ($q) {
                        $q->select('id_mhs', 'id_pengguna')->with([
                            'pengguna' => function ($qPengguna) {
                                $qPengguna->select('id_pengguna', 'nm_pengguna');
                            }
                        ]);
                    }
                ])
                ->get()
                ->map(function ($item) use ($minutesAgo, $id_kelas) {
                    // Cek apakah mahasiswa ini sudah presensi dalam 5 menit terakhir
                    $sudahPresensi = PresensiMhs::where('id_mhs', $item->id_mhs)
                        ->whereHas('presensiKelas', function ($q) use ($id_kelas, $minutesAgo) {
                            $q->where('id_kelas_mk', $id_kelas);
                                // ->where('created_at', '>=', $minutesAgo);
                        })
                        ->exists();

                    $item->sudah_presensi = $sudahPresensi;
                    return $item;
                });

            return response()->json([
                'status' => true,
                'message' => 'Data Mahasiswa Kelas',
                'data' => $mahasiswas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mendapatkan data mahasiswa kelas',
                'error' => $e->getMessage()
            ]);
        }
    }
}
