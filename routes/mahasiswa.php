<?php

use App\Models\Role;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group(['prefix' => 'mahasiswa', 'middleware' => 'role:' . Role::MAHASISWA], function () {

    // Handle default
    Route::get('/{menu}/{subMenu}', function ($menu, $subMenu) {
        return Inertia::render('Dashboard');
    });
});
