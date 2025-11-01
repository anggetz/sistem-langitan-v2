<?php

namespace App\Services\Mahasiswa;

class KegiatanService
{
    public function __construct() {}

    // get list kegiatan kemahasiswaan for mahasiswa
    public function listKegiatan()
    {
        return auth()->user()->mahasiswa->kegiatanKemahasiswaan()
            ->with(['kegiatanBobot.kegiatanJenis', 'kegiatanBobot.kegiatanTingkat', 'kegiatanBobot.kegiatanPrestasi', 'kegiatanBobot.kegiatanDokumenType', 'approver'])
            ->orderByRaw('CASE WHEN approved_at IS NULL THEN 0 ELSE 1 END')
            ->orderBy('tgl_kegiatan', 'asc')
            ->get();
    }

    // get list jenis kegiatan kemahasiswaan
    public function listJenisKegiatan()
    {
        return \App\Models\KegiatanJenis::orderBy('nm_kegiatan_jenis', 'asc')->get();
    }

    // get list tingkat kegiatan kemahasiswaan berdasarkan jenis kegiatan dan data ada di bobot kegiatan
    public function listTingkatKegiatan($jenisKegiatanId)
    {
        return \App\Models\KegiatanTingkat::whereHas('kegiatanBobots', function ($query) use ($jenisKegiatanId) {
            $query->where('id_kegiatan_jenis', $jenisKegiatanId);
        })->orderBy('id_kegiatan_tingkat', 'asc')->get();
    }

    // get list prestasi kegiatan kemahasiswaan berdasarkan jenis kegiatan, tingkat kegiatan dan data ada di bobot kegiatan untuk jenis kegiatan yang tidak ada tingkatan bisa skip tingkat kegiatan
    public function listPrestasiKegiatan($jenisKegiatanId, $tingkatKegiatanId = null)
    {
        return \App\Models\KegiatanBobot::where('id_kegiatan_jenis', $jenisKegiatanId)
            ->when($tingkatKegiatanId, function ($query) use ($tingkatKegiatanId) {
                return $query->where('id_kegiatan_tingkat', $tingkatKegiatanId);
            })->with(['kegiatanPrestasi', 'kegiatanDokumenType'])->get()->map(function ($item) {
                return [
                    'id_kegiatan_bobot' => $item->id_kegiatan_bobot,
                    'id_kegiatan_prestasi' => $item->kegiatanPrestasi->id_kegiatan_prestasi,
                    'nm_kegiatan_prestasi' => $item->kegiatanPrestasi->nm_kegiatan_prestasi,
                    'id_kegiatan_dokumen_type' => $item->kegiatanDokumenType->id_kegiatan_dokumen_type,
                    'nm_kegiatan_dokumen_type' => $item->kegiatanDokumenType->nm_kegiatan_dokumen_type,
                    'point_kegiatan_bobot' => $item->point_kegiatan_bobot,
                ];
            });
    }
}
