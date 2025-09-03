<?php

use App\Events\QrGenerateEvent;
use App\Http\Controllers\AkademikController;
use App\Http\Controllers\Firebase\Api\FcmController;
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
use App\Models\Semester;

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

    Route::get('akademik/jadwal_input_nilai', [AkademikController::class, 'JadwalPenilaian']);

    Route::group(['prefix' => 'pengguna'], function () {
        Route::post('ganti-password', [AuthController::class, 'gantiPassword']);
        Route::get('/', function (Request $request) {
            return Pengguna::with('role')->where('id_pengguna', $request->user()->id_pengguna)->first();
        });
        Route::post('fcm-token', [FcmController::class, 'storeFcmToken']);
        Route::post('fcm-send-pengumuman', [FcmController::class, 'sendNotificationByPengumumanId']);
    });

    // Message API Routes
    Route::group(['prefix' => 'messages', 'controller' => \App\Http\Controllers\Api\MessageController::class], function () {
        Route::get('/', 'index');                    // Get all conversations
        Route::get('/unread-count', 'unreadCount');  // Get unread messages count
        Route::get('/search', 'search');             // Search messages
        Route::get('/{partnerId}', 'show');          // Get messages with specific user
        Route::post('/', 'store');                   // Send new message
        Route::patch('/{messageId}/read', 'markAsRead'); // Mark message as read
        Route::delete('/{messageId}', 'destroy');    // Delete message
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
        Route::get('/semester', 'GetSemester');
    });

    // get active semester
    Route::get('/semester/aktif', function () {
        $semester = Semester::aktif();
        if ($semester) {
            return response()->json([
                'status' => Message::OK,
                'data' => $semester
            ]);
        } else {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Semester aktif tidak ditemukan.'
            ], 404);
        }
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
