<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Berita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    //
    public function dashboard(){
        $data = Berita::where('active',"True")->orderBy('pinned','desc')->orderBy('waktu_berita','desc')->limit(5)->get();
        return response()->json([
            'message' => Message::OK,
            'data' => $data
        ],200);
    }

    public function index(){
        $data = Berita::where('active',"True")->orderBy('pinned','desc')->orderBy('waktu_berita','desc')->get();
        return response()->json([
            'message' => Message::OK,
            'data' => $data
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
