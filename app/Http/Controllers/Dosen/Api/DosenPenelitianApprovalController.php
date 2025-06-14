<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\DosenWali;
use App\Models\Message;
use App\Models\Penelitian;
use App\Models\Semester;
use App\Services\Mahasiswa\KrsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

class DosenPenelitianApprovalController extends Controller
{
    public function __construct() {}

    public function approvalProdi(Request $request)
    {
        try {
            $validatedData = request()->validate([
                'id_penelitian' => 'required|numeric',
                'approval' => 'required|boolean',
            ]);

            $penelitian = Penelitian::find($validatedData['id_penelitian']);
            if (!$penelitian) {
                return response()->json([
                    'message' => 'Penelitian not found.',
                ], 404);
            }

            if ($penelitian->tgl_approve_departemen) {
                return response()->json([
                    'message' => 'Penelitian has already been approved by the department.',
                ], 400);
            }

            if ($penelitian->tgl_reject_departement) {
                return response()->json([
                    'message' => 'Penelitian has already been rejected by the department.',
                ], 400);
            }

            $updateValues = [

            ];

            if ($validatedData['approval']) {
                $updateValues['tgl_approve_departemen'] = now();
            } else {
                $updateValues['tgl_reject_departemen'] = now();
            }


            Penelitian::where('id_penelitian', $validatedData['id_penelitian'])
                ->update($updateValues);

            return response()->json([
                'message' =>  'Penelitian approval successfully.',
                'data' => $penelitian
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve penelitian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approvalDekan(Request $request)
    {
        try {
            $validatedData = request()->validate([
                'id_penelitian' => 'required|numeric',
                'approval' => 'required|boolean',
            ]);

            $penelitian = Penelitian::find($validatedData['id_penelitian']);
            if (!$penelitian) {
                return response()->json([
                    'message' => 'Penelitian not found.',
                ], 404);
            }

            if ($penelitian->tgl_approve_dekan) {
                return response()->json([
                    'message' => 'Penelitian has already been approved by the dekan.',
                ], 400);
            }

            if ($penelitian->tgl_reject_dekan) {
                return response()->json([
                    'message' => 'Penelitian has already been rejected by the dekan.',
                ], 400);
            }

            $updateValues = [

            ];

            if ($validatedData['approval']) {
                $updateValues['tgl_approve_dekan'] = now();
            } else {
                $updateValues['tgl_reject_dekan'] = now();
            }

            Penelitian::where('id_penelitian', $validatedData['id_penelitian'])
                ->update($updateValues);

            return response()->json([
                'message' =>  'Penelitian approval successfully.',
                'data' => $penelitian
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve penelitian.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approvalAkademik(Request $request)
    {
        try {
            $validatedData = request()->validate([
                'id_penelitian' => 'required|numeric',
                'approval' => 'required|boolean',
            ]);

            $penelitian = Penelitian::find($validatedData['id_penelitian']);
            if (!$penelitian) {
                return response()->json([
                    'message' => 'Penelitian not found.',
                ], 404);
            }

            if ($penelitian->tgl_approve_akademik) {
                return response()->json([
                    'message' => 'Penelitian has already been approved by the akademik.',
                ], 400);
            }

            if ($penelitian->tgl_reject_akademik) {
                return response()->json([
                    'message' => 'Penelitian has already been rejected by the akademik.',
                ], 400);
            }

            $updateValues = [

            ];

            if ($validatedData['approval']) {
                $updateValues['tgl_approve_akademik'] = now();
            } else {
                $updateValues['tgl_reject_akademik'] = now();
            }

            Penelitian::where('id_penelitian', $validatedData['id_penelitian'])
                ->update($updateValues);

            return response()->json([
                'message' =>  'Penelitian approval successfully.',
                'data' => $penelitian
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve penelitian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

     public function approvalLPPM(Request $request)
    {
        try {
            $validatedData = request()->validate([
                'id_penelitian' => 'required|numeric',
                'approval' => 'required|boolean',
            ]);

            $penelitian = Penelitian::find($validatedData['id_penelitian']);
            if (!$penelitian) {
                return response()->json([
                    'message' => 'Penelitian not found.',
                ], 404);
            }

            if ($penelitian->tgl_approve_lppm) {
                return response()->json([
                    'message' => 'Penelitian has already been approved by the LPPM.',
                ], 400);
            }

            if ($penelitian->tgl_reject_lppm) {
                return response()->json([
                    'message' => 'Penelitian has already been rejected by the LPPM.',
                ], 400);
            }

            $updateValues = [

            ];

            if ($validatedData['approval']) {
                $updateValues['tgl_approve_lppm'] = now();
            } else {
                $updateValues['tgl_reject_lppm'] = now();
            }

            Penelitian::where('id_penelitian', $validatedData['id_penelitian'])
                ->update($updateValues);

            return response()->json([
                'message' =>  'Penelitian approval successfully.',
                'data' => $penelitian
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve penelitian',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
