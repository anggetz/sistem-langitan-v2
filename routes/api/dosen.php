<?php

use App\Http\Controllers\Dosen\Api\DosenController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::group(
        ['prefix' => 'dosen', 'middleware' => 'role:' . Role::DOSEN],
        function () {
            Route::get('profile', [DosenController::class, 'profile']);
            Route::put('profile', [DosenController::class, 'EditProfile']);
            Route::post('profile-photo', [DosenController::class, 'EditPhotoProfile']);
        }
);
