<?php

namespace App\Http\Resources\Mahasiswa;

use Illuminate\Http\Resources\Json\JsonResource;

class JadwalKuliahResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $hari=$this->kelasMk->jadwalKelas->nama_hari;
        return [
            "kelas"=>$this->namaKelas->nama_kelas,
            "pjma"=> $this->kelasMk->pengampuMk->pengguna->nama_lengkap, 
            "mata_kuliah"=>$this->mataKuliah->nm_mata_kuliah,
            "sks"=>$this->kelasMk->mataKuliah->kredit_semester,
            "jadwal"=>$hari." - ".$this->kelasMk->jadwalKelas->jadwalJam->nm_jadwal_jam,
            "ruangan"=>$this->kelasMk->jadwalKelas->ruangan->nm_ruangan ?? "Belum Ditentukan",
        ];
    }
}
