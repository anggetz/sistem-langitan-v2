<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DosenPenelitianController extends Controller
{
    public function __construct() {}

    public function Index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            $offset = ($page - 1) * $perPage;

            $user = \App\Models\Pengguna::with([
                'dosen.penelitian' => function ($query) {
                },
            ])->find(auth()->user()->id_pengguna);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $penelitian = $user->dosen->penelitian()
                ->with([
                    'penelitianSkim',
                    'penelitianBidang',
                    'penelitianBidangIlmu',
                    'penelitianSumberDana',
                ])
                ->where('id_peneliti', $user->dosen->id_dosen)
                ->offset($offset)
                ->limit($perPage)
                ->get();

            $total = $user->dosen->penelitian()->count();
            $penelitian = [
                'data' => $penelitian,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
            ];

            return response()->json($penelitian, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }

    public function Create(Request $request) {
        try {
            $data = $request->validate([
                'judul' => 'required|string|max:255',
                'lokasi' => 'required|string',
                'jangka_waktu' => 'required|numeric',
                'kota' => 'required|string',
                'nama_institusi' => 'required|string',
                'penelitian_ke' => 'required|integer',
                'id_penelitian_skim' => 'required|integer|exists:penelitian_skim,id_penelitian_skim',
                'id_penelitian_bidang' => 'required|integer|exists:penelitian_bidang,id_penelitian_bidang',
                'id_penelitian_bidang_ilmu' => 'required|integer|exists:penelitian_bidang_ilmu,id_penelitian_bidang_ilmu',
                'id_penelitian_sumber_dana' => 'required|integer|exists:penelitian_sumber_dana,id_penelitian_sumber_dana',
                'penelitian_bidang_lain' => 'nullable|string',
                'is_proposal' => ['required', Rule::in(['0', '1'])],
                'file_proposal' => 'nullable|file|mimes:pdf|max:2048',
            ]);

            // Handle file upload if provided
            if ($request->hasFile('file_proposal')) {
                $file = $request->file('file_proposal');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('penelitian/proposals', $filename, 'public');
                $data['nama_file'] = 'penelitian/proposals/' . $filename;
            } else {
                $data['nama_file'] = null;
            }

            $penelitian = new \App\Models\Penelitian($data);
            $penelitian->jangka_waktu_ke = $data['penelitian_ke'];
            $penelitian->id_peneliti = auth()->user()->dosen->id_dosen;
            $penelitian->save();

            return response()->json(['message' => 'Penelitian created successfully', 'data' => $penelitian], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating penelitian: ' . $e->getMessage()], 500);
        }
    }

    public function Update(Request $request)
    {
        try {

            $data = $request->validate([
                'id' => 'required|integer|exists:penelitian,id_penelitian',
                'judul' => 'required|string|max:255',
                'lokasi' => 'required|string',
                'jangka_waktu' => 'required|numeric',
                'kota' => 'required|string',
                'nama_institusi' => 'required|string',
                'penelitian_ke' => 'required|integer',
                'id_penelitian_skim' => 'required|integer|exists:penelitian_skim,id_penelitian_skim',
                'id_penelitian_bidang' => 'required|integer|exists:penelitian_bidang,id_penelitian_bidang',
                'id_penelitian_bidang_ilmu' => 'required|integer|exists:penelitian_bidang_ilmu,id_penelitian_bidang_ilmu',
                'id_penelitian_sumber_dana' => 'required|integer|exists:penelitian_sumber_dana,id_penelitian_sumber_dana',
                'penelitian_bidang_lain' => 'nullable|string',
                'is_proposal' => ['required', Rule::in(['0', '1'])],
                'file_proposal' => 'nullable|file|mimes:pdf|max:2048',
            ]);

            $penelitian = \App\Models\Penelitian::findOrFail($id);

            if ($penelitian->id_peneliti !== auth()->user()->dosen->id_dosen) {
                return response()->json(['message' => 'Unauthorized to update this penelitian'], 403);
            }

            // Handle file upload if provided
            if ($request->hasFile('file_proposal')) {
                $file = $request->file('file_proposal');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('penelitian/proposals', $filename, 'public');
                $data['nama_file'] = 'penelitian/proposals/' . $filename;
            }

            $penelitian->update($data);

            return response()->json(['message' => 'Penelitian updated successfully', 'data' => $penelitian], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating penelitian: ' . $e->getMessage()], 500);
        }
    }

    public function Delete($id)
    {
        try {
            $penelitian = \App\Models\Penelitian::findOrFail($id);

            if ($penelitian->id_peneliti !== auth()->user()->dosen->id_dosen) {
                return response()->json(['message' => 'Unauthorized to delete this penelitian'], 403);
            }

            $penelitian->delete();

            return response()->json(['message' => 'Penelitian deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting penelitian: ' . $e->getMessage()], 500);
        }
    }

    public function Show($id)
    {
        try {
            $penelitian = \App\Models\Penelitian::findOrFail($id)->load([
                'penelitianSkim',
                'penelitianBidang',
                'penelitianBidangIlmu',
                'penelitianSumberDana',
            ]);

            return response()->json($penelitian, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching penelitian: ' . $e->getMessage()], 500);
        }
    }
}
