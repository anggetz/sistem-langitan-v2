<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentUserId = auth('api')->user()?->id_pengguna;

        return [
            'id_message' => $this->id_message,
            'pengirim' => [
                'id_pengguna' => $this->pengirim->id_pengguna,
                'nm_pengguna' => $this->pengirim->nm_pengguna,
            ],
            'penerima' => [
                'id_pengguna' => $this->penerima->id_pengguna,
                'nm_pengguna' => $this->penerima->nm_pengguna,
            ],
            'tema' => $this->tema,
            'isi_pesan' => $this->isi_pesan,
            'id_replay' => $this->id_replay,
            'pesan_direply' => $this->when($this->pesanDireply, function () {
                return [
                    'id_message' => $this->pesanDireply->id_message,
                    'isi_pesan' => $this->pesanDireply->isi_pesan,
                    'pengirim' => $this->pesanDireply->pengirim->nm_pengguna,
                    'waktu_kirim' => $this->pesanDireply->waktu_kirim->format('Y-m-d H:i:s'),
                ];
            }),
            'status_terbaca' => $this->status_terbaca,
            'waktu_kirim' => $this->waktu_kirim->format('Y-m-d H:i:s'),
            'waktu_baca' => $this->when($this->waktu_baca, function () {
                return $this->waktu_baca->format('Y-m-d H:i:s');
            }),
            'is_from_me' => $this->id_pengirim == $currentUserId,
            'replies_count' => $this->when($this->relationLoaded('replies'), function () {
                return $this->replies->count();
            }),
        ];
    }
}
