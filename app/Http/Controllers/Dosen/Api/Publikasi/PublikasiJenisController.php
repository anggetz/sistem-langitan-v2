<?php

namespace App\Http\Controllers\Dosen\Api\Publikasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publikasi\StorePublikasiJenisRequest;
use App\Http\Requests\Publikasi\UpdatePublikasiJenisRequest;
use App\Services\Publikasi\PublikasiJenisService;
use Illuminate\Http\Request;

class PublikasiJenisController extends Controller
{
    protected $publikasiJenisService;

    public function __construct(PublikasiJenisService $publikasiJenisService)
    {
        $this->publikasiJenisService = $publikasiJenisService;
    }

    /**
     * Display a listing of the jenis publikasi
     */
    public function index(Request $request)
    {
        try {
            $result = $this->publikasiJenisService->getAll($request->all());

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created jenis publikasi in storage
     */
    public function store(StorePublikasiJenisRequest $request)
    {
        try {
            $publikasiJenis = $this->publikasiJenisService->create($request->validated());

            return response()->json([
                'message' => 'Jenis publikasi berhasil ditambahkan',
                'data' => $publikasiJenis
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified jenis publikasi
     */
    public function show($id)
    {
        try {
            $publikasiJenis = $this->publikasiJenisService->getById($id);

            return response()->json([
                'data' => $publikasiJenis
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified jenis publikasi in storage
     */
    public function update(UpdatePublikasiJenisRequest $request, $id)
    {
        try {
            $publikasiJenis = $this->publikasiJenisService->update($id, $request->validated());

            return response()->json([
                'message' => 'Jenis publikasi berhasil diupdate',
                'data' => $publikasiJenis
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified jenis publikasi from storage
     */
    public function destroy($id)
    {
        try {
            $this->publikasiJenisService->delete($id);

            return response()->json([
                'message' => 'Jenis publikasi berhasil dihapus'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
