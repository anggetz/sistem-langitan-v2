<?php

use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::group(
        ['prefix' => 'dosen', 'middleware' => 'role:' . Role::DOSEN],
        function () {
                Route::get('/', function () {
                        return "Dosen API";
                });
        }
);
