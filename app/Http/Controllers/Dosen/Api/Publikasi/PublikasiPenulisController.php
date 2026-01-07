<?php

namespace App\Http\Controllers\Dosen\Api\Publikasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publikasi\StorePublikasiPenulisRequest;
use App\Http\Requests\Publikasi\UpdatePublikasiPenulisRequest;
use App\Services\Publikasi\PublikasiPenulisService;
use Illuminate\Http\Request;

class PublikasiPenulisController extends Controller
{
    protected $publikasiPenulisService;

    public function __construct(PublikasiPenulisService $publikasiPenulisService)
    {
        $this->publikasiPenulisService = $publikasiPenulisService;
    }

    /**
     * Display a listing of the publikasi penulis
     */
    public function index(Request $request)
    {
        try {
            $result = $this->publikasiPenulisService->getAll($request->all());

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created publikasi penulis in storage
     */
    public function store(StorePublikasiPenulisRequest $request)
    {
        try {
            $publikasiPenulis = $this->publikasiPenulisService->create($request->validated());

            return response()->json([
                'message' => 'Publikasi penulis berhasil ditambahkan',
                'data' => $publikasiPenulis
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified publikasi penulis
     */
    public function show($id)
    {
        try {
            $publikasiPenulis = $this->publikasiPenulisService->getById($id);

            return response()->json([
                'data' => $publikasiPenulis
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified publikasi penulis in storage
     */
    public function update(UpdatePublikasiPenulisRequest $request, $id)
    {
        try {
            $publikasiPenulis = $this->publikasiPenulisService->update($id, $request->validated());

            return response()->json([
                'message' => 'Publikasi penulis berhasil diupdate',
                'data' => $publikasiPenulis
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified publikasi penulis from storage
     */
    public function destroy($id)
    {
        try {
            $this->publikasiPenulisService->delete($id);

            return response()->json([
                'message' => 'Publikasi penulis berhasil dihapus'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
