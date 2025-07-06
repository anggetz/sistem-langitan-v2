<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QrGenerateEvent implements ShouldBroadcast // Penting! Implementasikan ini
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Properti yang akan dikirim ke client untuk membuat QR Code
    public $id_presensi;
    public $id_kelas_mk;
    public $qr_key;
    public $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct($id_presensi, $id_kelas_mk, $qr_key)
    {
        $this->id_presensi = $id_presensi;
        $this->id_kelas_mk = $id_kelas_mk;
        $this->qr_key = $qr_key;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Event ini akan dibroadcast ke channel publik bernama 'qr-generation'
        // Semua client yang subscribe ke 'qr-generation' akan menerimanya.
        return [
            new Channel('qr-generator-'.$this->id_presensi.'-'.$this->id_kelas_mk),
        ];
    }

    /**
     * The event's broadcast name. (Opsional)
     *
     * Ini akan mengubah nama event yang diterima di JavaScript dari 'QrGenerateEvent'
     * menjadi 'triggerQrGenerate'.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'triggerQr';
    }
}
