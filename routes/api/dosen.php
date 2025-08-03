<?php

use App\Http\Controllers\Dosen\Api\DosenController;
use App\Http\Controllers\Dosen\Api\DosenJadwalController;
use App\Http\Controllers\Dosen\Api\DosenKrsController;
use App\Http\Controllers\Dosen\Api\DosenPenelitianApprovalController;
use App\Http\Controllers\Dosen\Api\DosenPenelitianController;
use App\Http\Controllers\Dosen\Api\DosenPenelitianMasterController;
use App\Http\Controllers\Dosen\Api\DosenPenilaianController;
use App\Http\Controllers\Dosen\Api\DosenPresensiController;
use App\Http\Controllers\Dosen\Api\DosenQrController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::group(
    ['prefix' => 'dosen', 'middleware' => 'role:' . Role::DOSEN],
    function () {

        Route::get('profile', [DosenController::class, 'profile']);
        Route::put('profile', [DosenController::class, 'EditProfile']);

        Route::post('profile-photo', [DosenController::class, 'EditPhotoProfile']);

        Route::get('jadwal', [DosenJadwalController::class, 'Jadwal']);
        Route::get('jadwal-ujian-uts', [DosenJadwalController::class, 'JadwalUjianUTS']);
        Route::get('jadwal-ujian-uas', [DosenJadwalController::class, 'JadwalUjianUAS']);
        Route::get('jadwal-hari-ini', [DosenJadwalController::class, 'JadwalHariIni']);

        Route::get('mahasiswa/kelas/{id_kelas}/presensi/{id_presensi}', [DosenPresensiController::class, 'MahasiswaKelas']);

        Route::post('buat_presensi', [DosenPresensiController::class, 'createPresensi']);
        Route::get('list_presensi_kelas/{id_kelas_mk}', [DosenPresensiController::class, 'listPresensiKelasByIdKelas']);
        Route::put('edit_presensi/{id_presensi_kelas}', [DosenPresensiController::class, 'editPertemuan']);
        Route::delete('hapus_presensi/{id_presensi_kelas}', [DosenPresensiController::class, 'hapusPertemuan']);

        Route::get('list_materi_mk/{id_kelas_mk}', [DosenPresensiController::class, 'listMateriMk']);

        Route::put('mahasiswa/{id_kelas}/presensi/{id_presensi}', [DosenPresensiController::class, 'MahasiswaInOut']);

        Route::group(
            ['prefix' => 'krs', 'controller' => DosenKrsController::class],
            function () {
                Route::post('approve_course', 'approveKprsMk');
                Route::get('list_course_approval', 'listCourseApproval');
                Route::get('list_student_approval', 'listStudentNeedApproval');
            }
        );

        Route::group(
            ['prefix' => 'penelitian', 'controller' => DosenPenelitianController::class],
            function () {
                Route::get('/', 'Index');
                Route::post('/', 'Create');
                Route::post('/{id}', 'Create');
                Route::get('/{id}', 'Show');
                Route::delete('/{id}', 'Delete');
            }
        );

        Route::group(
            ['prefix' => 'penelitian_approval', 'controller' => DosenPenelitianApprovalController::class],
            function () {
                Route::post('approval_prodi', 'approvalProdi');
                Route::post('approval_dekan', 'approvalDekan');
                Route::post('approval_akademik', 'approvalAkademik');
                Route::post('approval_lppm', 'approvalLPPM');
            }
        );

        Route::group(
            ['prefix' => 'penilaian', 'controller' => DosenPenilaianController::class],
            function () {
                Route::get('get_komponen_mk/{id_kelas_mk}', 'getKomponenByIdKelasMk');
                Route::put('update_komponen/{id_kelas_mk}', 'updateKomponen');
                Route::get('nilai_akhir/{id_kelas_mk}', 'calculatingNilaiAkhir');
                Route::post('save_nilai', 'saveNilaiMk');
                Route::get('get_nilai/{id_kelas_mk}', 'getNilai');
            }
        );

        Route::group(
            ['prefix' => 'penelitian_master', 'controller' => DosenPenelitianMasterController::class],
            function () {
                Route::get('/skim', 'GetSkim');
                Route::get('/bidang', 'GetBidang');
                Route::get('/bidang-ilmu', 'GetBidangIlmu');
                Route::get('/sumber-dana', 'GetSumberDana');
            }
        );

        Route::post('qr-presensi/{id_presensi}', [DosenQrController::class, 'GenerateQR']);
        Route::post('qr-presensi/show/{id_presensi}', [DosenQrController::class, 'ShowCurrentQR']);
        Route::post('qr-presensi/reset/{id_presensi}', [DosenQrController::class, 'ResetQR']);
    }
);


