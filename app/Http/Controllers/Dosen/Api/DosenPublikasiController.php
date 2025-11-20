<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\PublikasiJobStatus;
use App\Models\PublikasiJurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class DosenPublikasiController extends Controller
{
    public function __construct() {}

    public function Index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            $offset = ($page - 1) * $perPage;

            $newQuery =  new PublikasiJurnal();

            $penelitian = $newQuery
                ->with('Authors');


            if ($request->has('q')) {
                $search = $request->input('q');
                $newQuery = $newQuery->where(function ($query) use ($search) {
                    $query->where('title', 'LIKE', "%$search%")
                        ->orWhere('tahun', 'LIKE', "%$search%")
                        ->orWhere('description', 'LIKE', "%$search%")
                        ->orWhere('link_artikel', 'LIKE', "%$search%");
                });
            }

            $penelitian = $newQuery
                ->orderBy('tahun', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            $total = $newQuery->count();
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

    public function getJobStatusByWebsocketTopic(Request $request) {
        try {
            $data = PublikasiJobStatus::where('WEBSOCKET_TOPIC', $request->get('websocket_topic'))->first();

            if (empty($data)) {
                return response()->json(['message' => 'data not found'], 400);
            }

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error get job status: ' . $e->getMessage()], 500);
        }
    }

    public function triggerSync(Request $request)
    {
        try {
            //dosen id is required;
            // dd(auth()->user()->dosen);
            $dosen = auth()->user()->dosen;
            if (empty($dosen)) {
                return response()->json(['message' => 'Dosen not found'], 400);
            }

            if (empty($dosen->scholar_id)) {
                return response()->json(['message' => 'ID Scholar belum di setup'], 400);
            }

            $webSocketTopic = 'sync-'.$dosen->id_dosen.'-' . (int) (microtime(true) * 1000);

            Artisan::queue('app:sync-penelitian-scopus', [
                '--id_dosen' => $dosen->id_dosen,
                '--websocket_topic' => $webSocketTopic,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Sinkronisasi publikasi sedang diproses',
                'websocketTopic' => $webSocketTopic
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }
}
