<?php

namespace App\Services\Mahasiswa;

class KegiatanService
{
    public function __construct() {}

    // get list kegiatan kemahasiswaan for mahasiswa
    public function listKegiatan()
    {
        return auth()->user()->mahasiswa->kegiatanKemahasiswaan()
            ->with([
                'kegiatanBobot.kegiatanGolongan.kegiatanJenis', 
                'kegiatanBobot.kegiatanGolongan.kegiatanDokumenType',
                'kegiatanBobot.kegiatanTingkat', 
                'kegiatanBobot.kegiatanPrestasi', 
                'approver'])
            ->orderByRaw('CASE WHEN approved_at IS NULL THEN 0 ELSE 1 END')
            ->orderBy('tgl_kegiatan', 'asc')
            ->get();
    }

    // get list jenis kegiatan kemahasiswaan
    public function listJenisKegiatan()
    {
        return \App\Models\KegiatanJenis::orderBy('nm_kegiatan_jenis', 'asc')->get();
    }

    // get list tingkat kegiatan kemahasiswaan berdasarkan golongan kegiatan yang ada di bobot kegiatan
    public function listTingkatKegiatan($golonganKegiatanId)
    {
        return \App\Models\KegiatanTingkat::whereHas('kegiatanBobots', function ($query) use ($golonganKegiatanId) {
            $query->where('id_kegiatan_golongan', $golonganKegiatanId);
        })->orderBy('id_kegiatan_tingkat', 'asc')->get();
    }

    // get list prestasi kegiatan kemahasiswaan berdasarkan golongan kegiatan, tingkat kegiatan dan data ada di bobot kegiatan untuk jenis kegiatan yang tidak ada tingkatan bisa skip tingkat kegiatan
    public function listPrestasiKegiatan($golonganKegiatanId, $tingkatKegiatanId = null)
    {
        return \App\Models\KegiatanBobot::where('id_kegiatan_golongan', $golonganKegiatanId)
            ->when($tingkatKegiatanId, function ($query) use ($tingkatKegiatanId) {
                return $query->where('id_kegiatan_tingkat', $tingkatKegiatanId);
            })->with(['kegiatanPrestasi'])->get()->map(function ($item) {
                return [
                    'id_kegiatan_bobot' => $item->id_kegiatan_bobot,
                    'id_kegiatan_prestasi' => $item->kegiatanPrestasi->id_kegiatan_prestasi,
                    'nm_kegiatan_prestasi' => $item->kegiatanPrestasi->nm_kegiatan_prestasi,
                    'point_kegiatan_bobot' => $item->point_kegiatan_bobot,
                ];
            });
    }

    public function listKegiatanGolongan($search=null){
        $query = \App\Models\KegiatanGolongan::query()->with('kegiatanDokumenType','kegiatanJenis');

        if ($search) {
            $query->whereLike('nm_kegiatan_golongan', '%' . $search . '%');
        }

        return $query->orderBy('nm_kegiatan_golongan', 'asc')->get();
    }
}
