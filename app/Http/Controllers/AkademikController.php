<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Berita;
use App\Services\Mahasiswa\AkademikService;
use Illuminate\Http\Request;

class AkademikController extends Controller
{
    //
    public function JadwalPenilaian(){
        $akademikService = new AkademikService();

        return $akademikService->ValidateInputNilaiScheduleByActiveSemester();
    }


}
