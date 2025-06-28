<?php

use App\Http\Controllers\Dosen\Api\DosenController;
use App\Http\Controllers\Dosen\Api\DosenJadwalController;
use App\Http\Controllers\Dosen\Api\DosenKrsController;
use App\Http\Controllers\Dosen\Api\DosenPenelitianApprovalController;
use App\Http\Controllers\Dosen\Api\DosenPenelitianController;
use App\Http\Controllers\Dosen\Api\DosenPenelitianMasterController;
use App\Http\Controllers\Dosen\Api\DosenPresensiController;
use App\Http\Controllers\Dosen\Api\DosenQrController;
use App\Http\Controllers\Rektor\AkreditasController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'rektor',
        // 'middleware' => 'role:' . Role::DOSEN <== suppose be rektor id
    ],
    function () {

        Route::get('/akreditas', [AkreditasController::class, 'Index']);
    }
);


