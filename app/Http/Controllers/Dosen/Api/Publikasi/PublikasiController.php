<?php

namespace App\Http\Controllers\Dosen\Api\Publikasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publikasi\StorePublikasiRequest;
use App\Http\Requests\Publikasi\UpdatePublikasiRequest;
use App\Services\Publikasi\PublikasiService;
use Illuminate\Http\Request;

class PublikasiController extends Controller
{
    protected $publikasiService;

    public function __construct(PublikasiService $publikasiService)
    {
        $this->publikasiService = $publikasiService;
    }

    /**
     * Display a listing of the publikasi for authenticated dosen
     */
    public function index(Request $request)
    {
        try {
            // Get authenticated user's dosen ID
            $dosenId = auth()->user()->dosen->id_dosen ?? null;
            
            if (!$dosenId) {
                return response()->json(['message' => 'Dosen tidak ditemukan'], 404);
            }

            // dd(auth()->user(), auth()->user()->dosen,$dosenId); 
            
            // Merge dosen ID into request parameters
            $params = $request->all();
            $params['id_dosen'] = $dosenId;
            
            $result = $this->publikasiService->getAll($params);

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created publikasi in storage
     */
    public function store(StorePublikasiRequest $request)
    {
        try {
            // Get authenticated user's dosen ID before validation
            $idDosen = auth()->user()->dosen?->id_dosen;
            
            if (!$idDosen) {
                return response()->json(['message' => 'Dosen tidak ditemukan'], 404);
            }
            
            // Merge id_dosen into request
            $request->merge(['id_dosen' => $idDosen]);
            
            $publikasi = $this->publikasiService->create($request->validated());

            return response()->json([
                'message' => 'Publikasi berhasil ditambahkan',
                'data' => $publikasi
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified publikasi
     */
    public function show($id)
    {
        try {
            $publikasi = $this->publikasiService->getById($id);

            return response()->json([
                'data' => $publikasi
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified publikasi in storage
     */
    public function update(UpdatePublikasiRequest $request, $id)
    {
        try {
            // Get authenticated user's dosen ID before validation
            $idDosen = auth()->user()->dosen?->id_dosen;
            
            if (!$idDosen) {
                return response()->json(['message' => 'Dosen tidak ditemukan'], 404);
            }
            
            // Merge id_dosen into request
            $request->merge(['id_dosen' => $idDosen]);
            
            $publikasi = $this->publikasiService->update($id, $request->validated());

            return response()->json([
                'message' => 'Publikasi berhasil diupdate',
                'data' => $publikasi
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified publikasi from storage
     */
    public function destroy($id)
    {
        try {
            $this->publikasiService->delete($id);

            return response()->json([
                'message' => 'Publikasi berhasil dihapus'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Approve publikasi
     */
    public function approve(Request $request, $id)
    {
        try {
            $userId = auth()->user()->id_pengguna;
            $publikasi = $this->publikasiService->approve($id, $userId);

            return response()->json([
                'message' => 'Publikasi berhasil disetujui',
                'data' => $publikasi
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error approving publikasi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Reject publikasi
     */
    public function reject(Request $request, $id)
    {
        try {
            $userId = $request->user()->id_pengguna;
            $publikasi = $this->publikasiService->reject($id, $userId);

            return response()->json([
                'message' => 'Publikasi berhasil ditolak',
                'data' => $publikasi
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error rejecting publikasi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get list of status options
     */
    public function statusOptions()
    {
        return response()->json([
            'data' => \App\Models\Publikasi::statusOptions()
        ]);
    }

    /**
     * Get list of SJR Kuartil options
     */
    public function sjrKuartilOptions()
    {
        return response()->json([
            'data' => \App\Models\Publikasi::sjrKuartilOptions()
        ]);
    }

    /**
     * Get list of SINTA options
     */
    public function sintaOptions()
    {
        return response()->json([
            'data' => \App\Models\Publikasi::sintaOptions()
        ]);
    }
}
