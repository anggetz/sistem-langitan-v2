<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Berita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    //
    public function dashboard(){
        // pagination parameter
        $page = request()->get('page', 1);
        $limit = request()->get('perPage', 5);
        $offset = ($page - 1) * $limit;

        $q = Berita::where('active',"Y");

        $total = $q->count();

        $data = $q->orderBy('pinned','desc')
            ->orderBy('waktu_berita','desc')
            ->limit($limit)
            ->offset($offset)
            ->get();
        return response()->json([
            'message' => Message::OK,
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'perPage' => $limit
        ],200);
    }

    public function index(){
        $page = request()->get('page', 1);
        $limit = request()->get('perPage', 10);
        $offset = ($page - 1) * $limit;

        $q = Berita::where('active',"Y");

        $total = $q->count();

        $data = $q->orderBy('pinned','desc')
            ->orderBy('waktu_berita','desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json([
            'message' => Message::OK,
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'perPage' => $limit
        ],200);
    }

    public function detail(Request $request,$slug){
        $data = Berita::where('slug',$slug)->first();
        return response()->json([
            'message' => Message::OK,
            'data' => $data
        ],200);
    }
}
