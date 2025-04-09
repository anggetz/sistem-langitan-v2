<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Dummy Role, Menu, & Sub-Menu
        $role = ['name' => 'Role Name', 'prefix_url' => 'role'];

        $menus = collect([
            ['name' => 'Menu 1', 'path' => 'menu-1', 'icon' => 'ki-filled ki-abstract-1', 'subMenus' => [
                ['name' => 'Sub Menu 1', 'path' => 'sub-menu-1', 'url' => '/role/menu-1/sub-menu-1'],
                ['name' => 'Sub Menu 2', 'path' => 'sub-menu-2', 'url' => '/role/menu-1/sub-menu-2'],
                ['name' => 'Sub Menu 3', 'path' => 'sub-menu-3', 'url' => '/role/menu-1/sub-menu-3'],
            ]],
            ['name' => 'Menu 2', 'path' => 'menu-2', 'icon' => 'ki-filled ki-abstract-2', 'subMenus' => [
                ['name' => 'Sub Menu 1', 'path' => 'sub-menu-1', 'url' => '/role/menu-2/sub-menu-1'],
                ['name' => 'Sub Menu 2', 'path' => 'sub-menu-2', 'url' => '/role/menu-2/sub-menu-2'],
                ['name' => 'Sub Menu 3', 'path' => 'sub-menu-3', 'url' => '/role/menu-2/sub-menu-3'],
            ]],
            ['name' => 'Menu 3', 'path' => 'menu-3', 'icon' => 'ki-filled ki-abstract-3', 'subMenus' => [
                ['name' => 'Sub Menu 1', 'path' => 'sub-menu-1', 'url' => '/role/menu-3/sub-menu-1'],
                ['name' => 'Sub Menu 2', 'path' => 'sub-menu-2', 'url' => '/role/menu-3/sub-menu-2'],
                ['name' => 'Sub Menu 3', 'path' => 'sub-menu-3', 'url' => '/role/menu-3/sub-menu-3'],
            ]],
        ]);

        session()->put('role', $role);
        session()->put('menus', $menus);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
