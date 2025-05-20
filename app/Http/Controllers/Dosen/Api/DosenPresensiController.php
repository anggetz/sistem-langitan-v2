<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\PengambilanMk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

class DosenPresensiController extends Controller
{
    public function __construct() {}

    public function MahasiswaKelas(Request $request, $id_kelas) {
        try {
            $mahasiswas = PengambilanMk::semesterAktif()->select('persen_presensi', 'id_kelas_mk', 'id_mhs')->where('id_kelas_mk', $id_kelas)->with(['mahasiswa' => function($q) {
                $q->select('id_mhs', 'id_pengguna')->with(['pengguna' => function($qPengguna) {
                    $qPengguna->select('id_pengguna', 'nm_pengguna');
                }]);
            }])->get();

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
