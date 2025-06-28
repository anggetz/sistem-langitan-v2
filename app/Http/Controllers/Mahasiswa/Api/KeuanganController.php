<?php

namespace App\Http\Controllers\Mahasiswa\Api;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TagihanMhs;
use App\Services\Mahasiswa\KeuanganService;

class KeuanganController extends Controller
{

    private $keuanganService;
    public function __construct(KeuanganService $keuanganService)
    {
        $this->keuanganService = $keuanganService;
    }
    public function riwayat(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $offset = ($page - 1) * $perPage;

        $data = $this->keuanganService->tagihan($page, $offset);
        return response()->json([
            'message' => Message::OK,
            'data' => $data
        ], 200);
    }

    public function riwayatDetail(TagihanMhs $tagihanMhs)
    {

        // return $tagihanMhs;
        $data = [];
        return response()->json([
            'message' => Message::OK,
            'data' => $tagihanMhs
        ], 200);
    }

    public function riwayatDetailCetak(TagihanMhs $tagihanMhs)
    {

        $data = [];
        return response()->json([
            'message' => Message::OK,
            'data' => $tagihanMhs
        ], 200);
    }

    public function informasi()
    {
        $mahasiswa = auth()->user()->mahasiswa;

        $dataPembayaran = Pembayaran::with([
            'tagihan.detail_biaya.biaya:id_biaya,nm_biaya,keterangan_biaya',
            'tagihan.detail_biaya:id_detail_biaya,id_biaya,besar_biaya,keterangan_biaya',
            'bank:id_bank,nm_bank',
            'tagihan:id_tagihan,besar_biaya,denda_biaya,id_detail_biaya',
        ])
            ->join('tagihan', 'tagihan.id_tagihan', 'pembayaran.id_tagihan')
            ->join('tagihan_mhs', 'tagihan_mhs.id_tagihan_mhs', 'tagihan.id_tagihan_mhs')
            ->where('tagihan_mhs.id_mhs', $mahasiswa->id_mhs)
            ->where('pembayaran.besar_pembayaran', '>', 0)
            ->get(['pembayaran.id_pembayaran', 'pembayaran.id_tagihan', 'pembayaran.tgl_bayar', 'pembayaran.id_bank']);

        $dataTunggakan = $this->keuanganService->tagihan()->where('total_tertangguh', '>', 0)->map->only(['nm_semester', 'tahun_ajaran', 'total_besar_biaya', 'total_terbayar', 'total_tertangguh', 'total_terbebas']);
        return response()->json([
            'message' => Message::OK,
            'dataTunggakan' => $dataTunggakan,
            'dataPembayaran' => $dataPembayaran,
        ], 200);
    }
}
