<?php

use App\Models\Role;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::group(
        ['prefix' => 'mahasiswa', 'middleware' => 'role:' . Role::MAHASISWA],
        function () {
                Route::get('/', function () {

                        return view('welcome');
                });
        }
);
