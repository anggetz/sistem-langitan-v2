<?php

namespace App\Services\Mahasiswa;

use App\Models\Message;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKegiatanSemester;
use App\Models\Pembayaran;

class KeuanganService{

    public function __construct()
    {

    }

    public function tagihan($page, $offset){
        $mahasiswa=Auth::user()->mahasiswa;
        // $mahasiswa->tagihan_mhs()
        //         ->with([
        //             "tagihan.pembayaran"=>function($pembayaran){$pembayaran->withSum("besar_pembayaran");},
        //             "semester",
        //             "pengembalian_pembayaran"
        //         ])->get();
        // return $mahasiswa->selectRaw("
        //         TM.ID_TAGIHAN_MHS,
        //         TM.ID_MHS")
        //     ->join("TAGIHAN_MHS TM","MAHASISWA.ID_MHS", "=", "TM.ID_MHS")
        //     ->where("NIM_MHS",$mahasiswa->nim_mhs)->get();

        return Mahasiswa::selectRaw("
                TM.ID_TAGIHAN_MHS,
                TM.ID_MHS,
                TM.NO_VA,
                TM.TRX_ID,
                TM.AWAL_PERIODE,
                TM.AKHIR_PERIODE,
                TM.KETERANGAN,
                TM.ID_SEMESTER,
                S.NM_SEMESTER,
                S.TAHUN_AJARAN,
                TM.TOTAL_BESAR_BIAYA,
                COALESCE(SUM(CASE WHEN PEM.ID_STATUS_PEMBAYARAN=1 THEN PEM.BESAR_PEMBAYARAN ELSE 0 END),0) TOTAL_TERBAYAR,
                COALESCE(SUM(CASE WHEN PEM.ID_STATUS_PEMBAYARAN=3 THEN PEM.BESAR_PEMBAYARAN ELSE 0 END),0) TOTAL_TERTANGGUH,
                COALESCE(SUM(CASE WHEN PEM.ID_STATUS_PEMBAYARAN=4 THEN PEM.BESAR_PEMBAYARAN ELSE 0 END),0) TOTAL_TERBEBAS,
                COALESCE(PEMBALI.BESAR_PENGEMBALIAN,0) TOTAL_PENGEMBALIAN,
                (SELECT SUM(PAYMENT_AMOUNT) FROM PEMBAYARAN_VA_BANK WHERE TRX_ID=TM.TRX_ID) TOTAL_TERBAYAR_VA,
                AVG(PEM.ID_STATUS_PEMBAYARAN) STATUS_PEMBAYARAN")
            ->join("TAGIHAN_MHS TM","MAHASISWA.ID_MHS", "=", "TM.ID_MHS")
            ->leftJoin("TAGIHAN TAG","TAG.ID_TAGIHAN_MHS", "=", "TM.ID_TAGIHAN_MHS")
            ->leftJoin("PEMBAYARAN PEM","PEM.ID_TAGIHAN", "=", "TAG.ID_TAGIHAN")
            ->leftJoin(DB::raw("(
                SELECT ID_TAGIHAN_MHS,SUM(BESAR_PENGEMBALIAN) BESAR_PENGEMBALIAN
                FROM PENGEMBALIAN_PEMBAYARAN
                GROUP BY ID_TAGIHAN_MHS
            )PEMBALI"),"PEMBALI.ID_TAGIHAN_MHS", "=", "TM.ID_TAGIHAN_MHS")
            ->Join("SEMESTER S","S.ID_SEMESTER", "=", "TM.ID_SEMESTER")
            ->groupBy("TM.ID_TAGIHAN_MHS","TM.ID_MHS","MAHASISWA.NIM_MHS","TM.NO_VA","TM.TRX_ID","TM.AWAL_PERIODE","TM.AKHIR_PERIODE","TM.KETERANGAN","TM.ID_SEMESTER","S.NM_SEMESTER","S.TAHUN_AJARAN","PEMBALI.BESAR_PENGEMBALIAN","TM.TOTAL_BESAR_BIAYA")
            ->where("NIM_MHS",$mahasiswa->nim_mhs)
            ->whereNotNull("PEM.TGL_BAYAR")
            ->orderByDesc("S.TAHUN_AJARAN","S.NM_SEMESTER")->get();
    }

}
