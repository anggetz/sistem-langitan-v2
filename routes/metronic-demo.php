<?php

use App\Http\Controllers\MetronicDemo\ComponentsController;
use App\Http\Controllers\MetronicDemo\CrudController;
use App\Http\Controllers\MetronicDemo\MetronicDemoController;
use App\Http\Controllers\MetronicDemo\TemplatesController;
use Illuminate\Support\Facades\Route;

Route::prefix('/metronic-demo')->group(function() {
    Route::get('/get-profile', [MetronicDemoController::class, 'getProfile']);
    Route::get('/dashboard', [MetronicDemoController::class, 'dashboard']);
    Route::get('/templates/{page}', TemplatesController::class);
    Route::get('/components/{component}', ComponentsController::class);
    Route::resource('crud', CrudController::class);
});
