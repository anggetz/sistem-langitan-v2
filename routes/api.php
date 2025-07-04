<?php

use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\PerguruanTinggi;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\Auth\Api\AuthController;
use App\Http\Controllers\BeasiswaController;
use App\Http\Controllers\KegiatanAkdEksController;
use App\Http\Controllers\KegiatanKelompokController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\Pengumuman\PengumumanController;
use App\Models\Pengguna;

Route::get('/', function () {
    return "Laravel Version : " . app()->version();
});


Route::get('/testme', function () {
    return PerguruanTinggi::find(env('APP_ID_PERGURUAN_TINGGI_DEFAULT'));
});

Route::get('/berita/dashboard', [BeritaController::class, 'dashboard']);
Route::get('/berita/list', [BeritaController::class, 'index']);
Route::get('/berita/detail/{slug}', [BeritaController::class, 'detail']);

Route::get('/quote', function () {
    // generate quotes
    $quote = null;
    if ($file = storage_path('app/quotes.txt')) {
        $arrQuotes = file($file);
        $qIndex = rand(0, @count($arrQuotes));
        $quote = trim($arrQuotes[$qIndex]);
    }

    return response()->json([
        'status' => Message::OK,
        'quote' => $quote
    ]);
});

Route::group([
    'prefix' => 'auth',
    'controller' => AuthController::class
], function () {
    Route::post('login', 'login');
    Route::post('refresh-token', 'refreshToken');
    Route::post('forgot-password', 'forgotPassword')->name('forgot-password');
    Route::get('get-info-reset-password', 'getInfoResetPassword');
    Route::post('reset-password', 'resetPassword');
    Route::post('logout', 'destroy')->middleware("auth");
});

Route::group(['middleware' => 'auth.token'], function () {

    Route::group(['prefix' => 'pengguna'], function () {
        Route::post('ganti-password', [AuthController::class, 'gantiPassword']);
        Route::get('/', function (Request $request) {
            return Pengguna::with('role')->where('id_pengguna', $request->user()->id_pengguna)->first();
        });
    });

    Route::group(['prefix' => '/beasiswa', 'controller' => BeasiswaController::class], function () {
        Route::get('/', 'Index');
        Route::get('/{id}', 'Detail');
        Route::get('/history/{id}', 'History');
    });

    Route::group(['prefix' => '/pengumuman', 'controller' => PengumumanController::class], function () {
        Route::get('/', 'Index');
        Route::get('/{id}', 'GetById');
        Route::post('/', 'Create');
        Route::put('/{id}', 'Update');
        Route::delete('/{id}', 'Delete');
    });

    Route::group(['prefix' => '/master/kegiatan_akd_eks', 'controller' => KegiatanAkdEksController::class], function () {
        Route::get('/', 'Get');
        Route::get('/{id}', 'GetById');
        Route::post('/', 'Create');
        Route::put('/{id}', 'UpdateKegiatan');
        Route::delete('/{id}', 'DeleteKegiatan');
    });

    Route::group(['prefix' => '/master/kegiatan_kelompok_akd_eks', 'controller' => KegiatanKelompokController::class], function () {
        Route::get('/', 'Get');
        Route::get('/{id}', 'GetById');
        Route::post('/', 'Create');
        Route::put('/{id}', 'UpdateKegiatan');
        Route::delete('/{id}', 'DeleteKegiatan');
    });

    Route::group(['prefix' => '/master', 'controller' => MasterController::class], function () {
        Route::get('/ruangan', 'GetRuangan');
    });

    require_once(__DIR__ . "/api/mahasiswa.php");
    require_once(__DIR__ . "/api/rektor.php");
    require_once(__DIR__ . "/api/dosen.php");

});

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint tidak ditemukan.',
        'code' => 404,
    ], 404);
});
