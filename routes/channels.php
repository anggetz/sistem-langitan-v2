<?php

use App\Events\QrGenerateEvent;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Broadcast;

// Channel definition Anda yang sudah ada
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('qr-generator-{id_presensi}-{id_kelas_mk}', function () {
    return true; // Anyone can listen to this public channel
});
