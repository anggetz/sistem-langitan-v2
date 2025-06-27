<?php

namespace App\Http\Controllers;

use App\Models\KegiatanAkdEks;
use App\Models\Message;
use App\Models\Pengguna;
use App\Models\Pengumuman;
use Exception;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function Index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            $offset = ($page - 1) * $perPage;


            $total = Pengumuman::count();
            $pengumumans = Pengumuman::offset($offset)
                ->limit($perPage)
                ->all();


            $json = [
                'data' => $pengumumans,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
            ];

            return response()->json($json, 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage()
            ]);
        }
    }

    public function Create(Request $request)
    {
        try {

            $data = $request->validate([
                'judul' => 'required|string|max:100',
                'konten' => 'required|string|max:2000',
                'tanggal_expired' => ''
            ]);

            $pengumuman = new Pengumuman($data);

            return response()->json(['message' => 'Pengumuman berhasil dibuat', 'data' => $pengumuman], 201);
        } catch (Exception $err) {
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'judul' => 'required|string|max:100',
                'konten' => 'required|string|max:2000',
                'tanggal_expired' => 'nullable|date'
            ]);

            $pengumuman = Pengumuman::where('id_pengumuman', $id)->first();

            if (!$pengumuman) {
                return response()->json(['message' => 'Pengumuman tidak ditemukan'], 404);
            }

            $pengumuman->update($data);

            return response()->json(['message' => 'Pengumuman berhasil diupdate', 'data' => $pengumuman], 200);
        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $pengumuman = Pengumuman::where('id_pengumuman', $id)->first();

            if (!$pengumuman) {
                return response()->json(['message' => 'Pengumuman tidak ditemukan'], 404);
            }

            $pengumuman->delete();

            return response()->json(['message' => 'Pengumuman berhasil dihapus'], 200);
        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage()
            ], 500);
        }
    }


    public function getOne($id)
    {
        try {
            $pengumuman = Pengumuman::where('id_pengumuman', $id)->first();

            if (!$pengumuman) {
                return response()->json(['message' => 'Pengumuman tidak ditemukan'], 404);
            }

            return response()->json(['data' => $pengumuman], 200);
        } catch (\Exception $err) {
            return response()->json([
                'status' => 'error',
                'message' => $err->getMessage()
            ], 500);
        }
    }
}
