<?php

use App\Models\Role;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\Mahasiswa\Api\BerandaController;
use App\Http\Controllers\Mahasiswa\Api\AkademikController;
use App\Http\Controllers\Mahasiswa\Api\KeuanganController;
use App\Http\Controllers\Mahasiswa\Api\MahasiswaController;
use App\Http\Controllers\Mahasiswa\Api\MahasiswaKrsController;
use App\Http\Controllers\Mahasiswa\Api\MahasiswaQrPresensiController;

Route::group(['prefix' => 'mahasiswa', 'middleware' => 'role:' . Role::MAHASISWA], function () {
    Route::get('me', [PenggunaController::class, 'me']);
    Route::get('beranda', BerandaController::class);

    Route::group(
        ['prefix' => 'biodata', 'controller' => MahasiswaController::class],
        function () {
            Route::get('', 'index');
            Route::get('/{mahasiswa}', 'show');
            Route::put('/{mahasiswa}', 'update');
        }
    );

    Route::group(
        ['prefix' => 'akademik', 'controller' => AkademikController::class],
        function () {
            Route::get('kalender', 'kalender');
            Route::get('jadwal-kuliah', 'jadwalKuliah');
            Route::get('jadwal-ujian', 'jadwalUjian');
            Route::get('history-nilai', 'historyNilai');
            Route::get('khs', 'khs');
            Route::get('khs/{semester}', 'khsPerSemester');
            Route::get('khs/{semester}/mata-kuliah/{mk}', 'khsPerMataKuliah');
            Route::get('list-semester', 'listSemester');
            Route::get('rekap-absensi/{semester}', 'rekapAbsensi');
        }
    );

    Route::group(
        ['prefix' => 'keuangan', 'controller' => KeuanganController::class],
        function () {
            Route::get('riwayat', 'riwayat');
            Route::get('riwayat/{tagihanMhs}/detail', 'riwayatDetail');
            Route::get('riwayat/{tagihanMhs}/cetak', 'riwayatDetailCetak');
            Route::get('informasi', 'informasi');
        }
    );

    Route::group(
        ['prefix' => 'krs', 'controller' => MahasiswaKrsController::class],
        function () {
            Route::get('check-krs-schedule', 'CheckKRSScheduleOnCurrentSemester');
            Route::get('list_mk', 'listMataKuliahByActiveSemesterAndProdi');
            Route::post('take_krs', 'takeCourse');
        });
    Route::group(
        ['prefix' => 'presensi', 'controller' => MahasiswaQrPresensiController::class],
        function () {
            Route::post('qrPresensi', 'qrPresensi');
        }
    );
});
