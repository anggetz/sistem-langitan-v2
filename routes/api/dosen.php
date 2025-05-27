<?php

use App\Http\Controllers\Dosen\Api\DosenController;
use App\Http\Controllers\Dosen\Api\DosenJadwalController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::group(
        ['prefix' => 'dosen', 'middleware' => 'role:' . Role::DOSEN],
        function () {

            Route::get('profile', [DosenController::class, 'profile']);
            Route::put('profile', [DosenController::class, 'EditProfile']);
            Route::post('profile-photo', [DosenController::class, 'EditPhotoProfile']);

            Route::get('jadwal', [DosenJadwalController::class, 'Jadwal']);
            Route::get('jadwal-hari-ini', [DosenJadwalController::class, 'JadwalHariIni']);
        }
);
