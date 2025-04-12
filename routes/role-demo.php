<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/{role}/{menu}/{subMenu}', function (string $menu, string $subMenu) {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified']);
