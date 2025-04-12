<?php

use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/metronic-demo', function () {
    Inertia::setRootView('metronic');
    return Inertia::render('MetronicDemo');
});

Route::get('/metronic-demo/data-table', function () {
    // Inertia::setRootView('metronic');

    $users = Pengguna::query()
    ->select(['id_pengguna', 'id_role', 'nm_pengguna', 'username'])
    ->addSelect(['nm_role' => Role::select('nm_role')->whereColumn('id_role', 'pengguna.id_role')])
    ->paginate(10);

    // return $users;

    return Inertia::render('MetronicDemo/Components/ServerDataTable', [
        'users' => $users
    ]);
})->name('metronic-demo.data-table.index');

Route::get('api/metronic-demo/data-table', function () {});
