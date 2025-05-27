<?php

use App\Http\Controllers\Dosen\Api\DosenController;
use App\Http\Controllers\Dosen\Api\DosenJadwalController;
use App\Http\Controllers\Dosen\Api\DosenPresensiController;
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

            Route::get('mahasiswa/kelas/{id_kelas}', [DosenPresensiController::class, 'MahasiswaKelas']);
            Route::put('mahasiswa/{id_kelas}/presensi/{id_presensi}', [DosenPresensiController::class, 'MahasiswaInOut']);
        }
);
