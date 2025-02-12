<?php

use App\Http\Controllers\MetronicDemo\ComponentsController;
use App\Http\Controllers\MetronicDemo\CrudController;
use App\Http\Controllers\MetronicDemo\MetronicDemoController;

Route::prefix('/metronic-demo')->group(function() {
    Route::get('/get-profile', [MetronicDemoController::class, 'getProfile']);
    Route::get('/dashboard', [MetronicDemoController::class, 'dashboard']);
    Route::resource('/crud', CrudController::class);
    Route::get('/components/accordion', [ComponentsController::class, 'accordion']);
});
