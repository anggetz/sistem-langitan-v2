<?php

namespace App\Http\Controllers\Dosen\Api\Publikasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publikasi\StorePublikasiPengindeksRequest;
use App\Http\Requests\Publikasi\UpdatePublikasiPengindeksRequest;
use App\Services\Publikasi\PublikasiPengindeksService;
use Illuminate\Http\Request;

class PublikasiPengindeksController extends Controller
{
    protected $publikasiPengindeksService;

    public function __construct(PublikasiPengindeksService $publikasiPengindeksService)
    {
        $this->publikasiPengindeksService = $publikasiPengindeksService;
    }

    /**
     * Display a listing of the publikasi pengindeks
     */
    public function index(Request $request)
    {
        try {
            $result = $this->publikasiPengindeksService->getAll($request->all());

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created publikasi pengindeks in storage
     */
    public function store(StorePublikasiPengindeksRequest $request)
    {
        try {
            $publikasiPengindeks = $this->publikasiPengindeksService->create($request->validated());

            return response()->json([
                'message' => 'Publikasi pengindeks berhasil ditambahkan',
                'data' => $publikasiPengindeks
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified publikasi pengindeks
     */
    public function show($id)
    {
        try {
            $publikasiPengindeks = $this->publikasiPengindeksService->getById($id);

            return response()->json([
                'data' => $publikasiPengindeks
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified publikasi pengindeks in storage
     */
    public function update(UpdatePublikasiPengindeksRequest $request, $id)
    {
        try {
            $publikasiPengindeks = $this->publikasiPengindeksService->update($id, $request->validated());

            return response()->json([
                'message' => 'Publikasi pengindeks berhasil diupdate',
                'data' => $publikasiPengindeks
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified publikasi pengindeks from storage
     */
    public function destroy($id)
    {
        try {
            $this->publikasiPengindeksService->delete($id);

            return response()->json([
                'message' => 'Publikasi pengindeks berhasil dihapus'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
