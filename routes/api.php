<?php

use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\PerguruanTinggi;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\Auth\Api\AuthController;

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
            return $request->user();
        });
    });

    require_once(__DIR__ . "/api/mahasiswa.php");
    require_once(__DIR__ . "/api/dosen.php");
});



