<?php

namespace App\Http\Controllers\MetronicDemo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MetronicDemoController extends Controller
{
    /**
     * Fungsi ini digunakan untuk mendapatkan data profile user secara ajax untuk kebutuhan
     * menu sidebar dan profil user
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfile()
    {
        return response()->json([
            'name' => 'Nama User',
            'email' => 'user@company.com',
            'role_id' => 1,  // Ubah nilai ini untuk melihat perubahan daftar menu sidebar
        ]);
    }

    public function dashboard()
    {
        return Inertia::render('Examples/Dashboard');
    }

}
