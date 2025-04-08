<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/metronic-demo', function () {
    Inertia::setRootView('metronic');
    return Inertia::render('MetronicDemo');
});
