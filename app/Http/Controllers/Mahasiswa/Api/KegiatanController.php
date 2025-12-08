<?php

namespace App\Http\Controllers\Mahasiswa\Api;

use App\Http\Controllers\Controller;
use App\Models\KegiatanBobot;
use App\Models\KegiatanKemahasiswaan;
use App\Models\Message;
use App\Services\Mahasiswa\KegiatanService;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    private $kegiatanService;

    public function __construct(KegiatanService $kegiatanService)
    {
        $this->kegiatanService = $kegiatanService;
    }

    public function listKegiatan()
    {
        try {
            $data = $this->kegiatanService->listKegiatan();
            // tambahkan total point kegiatan yang appoved dan yang belum approved
            $totalPointApproved = $data->whereNotNull('approved_at')->sum('point_kegiatan');
            $totalPointPending = $data->whereNull('approved_at')->sum('point_kegiatan');

            return response()->json([
                'status' => Message::OK,
                'data' => $data,
                'total_point' => [
                    'approved' => $totalPointApproved,
                    'pending' => $totalPointPending,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function listJenisKegiatan()
    {
        try {
            $data = $this->kegiatanService->listJenisKegiatan();

            return response()->json([
                'status' => Message::OK,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function listTingkatKegiatan($golongan)
    {
        try {
            $data = $this->kegiatanService->listTingkatKegiatan($golongan);

            return response()->json([
                'status' => Message::OK,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function listPrestasiKegiatan($golongan, $tingkat = null)
    {
        try {
            $data = $this->kegiatanService->listPrestasiKegiatan($golongan, $tingkat);

            return response()->json([
                'status' => Message::OK,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // fungsi untuk menyimpan kegiatan kemahasiswaan baru bisa ditambahkan di sini
    public function store(Request $request)
    {
        // validasi inputan
        $request->validate([
            'id_bobot' => 'required|exists:kegiatan_bobot,id_kegiatan_bobot',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
        ]);

        $bobot = KegiatanBobot::find($request->id_bobot);
        if (! $bobot) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Spesifikasi kegiatan tidak ditemukan',
            ], 404);
        }

        // simpan data kegiatan kemahasiswaan
        $kegiatan = new \App\Models\KegiatanKemahasiswaan;
        $kegiatan->id_mhs = $idMhs = auth()->user()->mahasiswa->id_mhs;
        $kegiatan->id_kegiatan_bobot = $request->id_bobot;
        $kegiatan->nm_kegiatan = $request->nama;
        $kegiatan->deskripsi_kegiatan = $request->deskripsi;
        $kegiatan->tgl_kegiatan = $request->tanggal;
        $kegiatan->point_kegiatan = $bobot->point_kegiatan_bobot;
        
        // Simpan file upload jika ada
        if ($request->hasFile('dokumen')) {
            $request->validate([
                'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            ]);

            $file = $request->file('dokumen');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension;
            $folderPath = 'sk3/' . $idMhs;
            
            // Simpan file ke storage/app/public/sk3/{id_mhs}/{timestamp}.{extension}
            $path = $file->storeAs($folderPath, $fileName, 'public');
            $kegiatan->dokumen_kegiatan_path = $path;
        }
        
        $kegiatan->save();

        return response()->json([
            'status' => Message::OK,
            'data' => $kegiatan->load([
                'kegiatanBobot.kegiatanGolongan.kegiatanJenis',
                'kegiatanBobot.kegiatanGolongan.kegiatanDokumenType',
                'kegiatanBobot.kegiatanTingkat', 
                'kegiatanBobot.kegiatanPrestasi']),
        ], 201);
    }

    // fungsi untuk mengupdate kegiatan kemahasiswaan bisa ditambahkan di sini
    public function update(Request $request, $id)
    {

        $kegiatan = auth()->user()->mahasiswa->kegiatanKemahasiswaan()->where('id_kegiatan_kemahasiswaan', $id)->first();

        if (! $kegiatan) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Kegiatan tidak ditemukan',
            ], 404);
        }

        if ($kegiatan->approved_at) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Kegiatan yang sudah disetujui tidak dapat diubah',
            ], 400);
        }

        // validasi inputan
        $validated = $request->validate([
            'id_bobot' => 'required|exists:kegiatan_bobot,id_kegiatan_bobot',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
        ]);
        
        $bobot = KegiatanBobot::find($request->id_bobot);
        if (! $bobot) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Spesifikasi kegiatan tidak ditemukan',
            ], 404);
        }

        // update data kegiatan kemahasiswaan
        $kegiatan->id_kegiatan_bobot = $request->id_bobot;
        $kegiatan->nm_kegiatan = $request->nama;
        $kegiatan->deskripsi_kegiatan = $request->deskripsi;
        $kegiatan->tgl_kegiatan = $request->tanggal;
        $kegiatan->point_kegiatan = $bobot->point_kegiatan_bobot;
        $kegiatan->is_rejected = false;
        $kegiatan->rejected_message = null;
        $idMhs = auth()->user()->mahasiswa->id_mhs;
        if ($request->hasFile('dokumen')) {
            $request->validate([
                'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            ]);

            $file = $request->file('dokumen');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension;
            $folderPath = 'sk3/' . $idMhs;
            
            // Simpan file ke storage/app/public/sk3/{id_mhs}/{timestamp}.{extension}
            $path = $file->storeAs($folderPath, $fileName, 'public');
            $kegiatan->dokumen_kegiatan_path = $path;
        }
        $kegiatan->save();

        return response()->json([
            'status' => Message::OK,
            'data' => $kegiatan->load(['kegiatanBobot.kegiatanGolongan.kegiatanJenis','kegiatanBobot.kegiatanGolongan.kegiatanDokumenType', 'kegiatanBobot.kegiatanTingkat', 'kegiatanBobot.kegiatanPrestasi']),
        ], 200);
    }

    // fungsi untuk menghapus kegiatan kemahasiswaan bisa ditambahkan di sini
    public function destroy($id)
    {
        $kegiatan = auth()->user()->mahasiswa->kegiatanKemahasiswaan()->where('id_kegiatan_kemahasiswaan', $id)->first();
        if (! $kegiatan) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Kegiatan tidak ditemukan',
            ], 404);
        }
        if ($kegiatan->approved_at) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Kegiatan yang sudah disetujui tidak dapat dihapus',
            ], 400);
        }
        $kegiatan->delete();

        return response()->json([
            'status' => Message::OK,
            'message' => 'Kegiatan berhasil dihapus',
        ], 200);
    }

    // fungsi untuk melihat detail kegiatan kemahasiswaan bisa ditambahkan di sini
    public function show($id)
    {
        $kegiatan = auth()->user()->mahasiswa->kegiatanKemahasiswaan()->with(['kegiatanBobot.kegiatanGolongan.kegiatanJenis', 'kegiatanBobot.kegiatanTingkat', 'kegiatanBobot.kegiatanPrestasi', 'kegiatanBobot.kegiatanGolongan.kegiatanDokumenType', 'approver'])->where('id_kegiatan_kemahasiswaan', $id)->first();
        if (! $kegiatan) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Kegiatan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => Message::OK,
            'data' => $kegiatan,
        ], 200);
    }

    // fungsi approve kegiatan kemahasiswaan bisa ditambahkan di sini
    public function approve($id)
    {
        $kegiatan = KegiatanKemahasiswaan::find($id);
        if (! $kegiatan) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Kegiatan tidak ditemukan',
            ], 404);
        }
        if ($kegiatan->approved_at) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Kegiatan sudah disetujui',
            ], 400);
        }
        $kegiatan->approved_at = now();
        $kegiatan->id_approver = auth()->user()->id_pengguna;
        $kegiatan->save();

        return response()->json([
            'status' => Message::OK,
            'data' => $kegiatan,
        ], 200);
    }

    public function listKegiatanGolongan($search=null)
    {
        try {
            $data = $this->kegiatanService->listKegiatanGolongan($search);

            return response()->json([
                'status' => Message::OK,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
