<?php

namespace App\Http\Controllers;

use App\Models\KegiatanAkdEks;
use App\Models\Message;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class KegiatanAkdEksController extends Controller
{
    public function Create(Request $request)
    {
        try {
            $request->validate([
                'nm_kegiatan' => 'required|string|max:255',
                'id_kelompok_kegiatan' => 'numeric',
                'id_semester' => 'required|numeric|exists:semester,id_semester',
                'per_fakultas' => 'required|string|max:1',
                'id_fakultas' =>  'required_if:per_fakultas,Y|numeric|exists:fakultas,id_fakultas',
            ]);

            KegiatanAkdEks::create([
                'nm_kegiatan' => $request->nm_kegiatan,
                'id_kelompok_kegiatan' => $request->id_kelompok_kegiatan,
                'id_semester' => $request->id_semester,
                'per_fakultas' => $request->per_fakultas,
                'id_fakultas' => $request->id_fakultas,
            ]);
            return response()->json([
                'message' => Message::OK,
                'data' => 'Kegiatan Berhasil Dibuat',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal  membuat kegiatan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function Get(Request $request) {
        try {

            $limit = $request->get('perPage', 10);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $limit;
            $id_fakultatas = $request->get('id_fakultas');
            $id_semester = $request->get('id_semester');


            $qkegiatan = KegiatanAkdEks::with(['kelompokKegiatan', 'fakultas']);

            if ($id_fakultatas) {
                $qkegiatan->where('id_fakultas', $id_fakultatas);
            }
            if ($id_semester) {
                $qkegiatan->where('id_semester', $id_semester);
            }
            $kegiatan = $qkegiatan
                // ->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get();

            return response()->json([
                'message' => Message::OK,
                'data' => $kegiatan,
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mendapatkan data kegiatan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function GetById($id)
    {
        try {
            $kegiatan = KegiatanAkdEks::with(['kelompokKegiatan', 'fakultas'])
                ->findOrFail($id);

            return response()->json([
                'message' => Message::OK,
                'data' => $kegiatan,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mendapatkan data kegiatan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function DeleteKegiatan($id)
    {
        try {
            $kegiatan = KegiatanAkdEks::findOrFail($id);
            $kegiatan->delete();

            return response()->json([
                'message' => Message::OK,
                'data' => 'Kegiatan Berhasil Dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus kegiatan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function UpdateKegiatan(Request $request, $id)
    {
        try {
            $request->validate([
                'nm_kegiatan' => 'required|string|max:255',
                'id_kelompok_kegiatan' => 'required|numeric|exists:kelompok_kegiatan,id_kelompok_kegiatan',
                'id_semester' => 'required|numeric|exists:semester,id_semester',
                'per_fakultas' => 'required|string|max:1',
                'id_fakultas' =>  'required_if:per_fakultas,Y|numeric|exists:fakultas,id_fakultas',
            ]);

            $kegiatan = KegiatanAkdEks::findOrFail($id);
            $kegiatan->update([
                'nm_kegiatan' => $request->nm_kegiatan,
                'id_kelompok_kegiatan' => $request->id_kelompok_kegiatan,
                'id_semester' => $request->id_semester,
                'per_fakultas' => $request->per_fakultas,
                'id_fakultas' => $request->id_fakultas,
            ]);

            return response()->json([
                'message' => Message::OK,
                'data' => 'Kegiatan Berhasil Diupdate',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengupdate kegiatan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
