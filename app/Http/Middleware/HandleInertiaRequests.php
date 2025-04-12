<?php

namespace App\Http\Middleware;

use App\Models\Modul;
use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $role = session('role') ?? [];

        // Auto build breadcrumbs array
        $breadcrumbs = [
            ['Home', route('dashboard')],
        ];

        // /role/menu-1/sub-menu-1
        //   0     1      2
        // Find path from url
        // foreach ($menus as $menu) {
        //     if ($request->segment(2) == $menu['path']) {
        //         $breadcrumbs[] = [$menu['name']];
        //         foreach ($menu['subMenus'] as $subMenu) {
        //             if ($request->segment(3) == $subMenu['path']) {
        //                 $breadcrumbs[] = [$subMenu['name'], '/' . $role['prefix_url'] . '/' . $menu['path'] . '/' . $subMenu['path']];
        //                 break;
        //             }
        //         }
        //         break;
        //     }
        // }

        $user = Auth::user();
        $cacheTime = 3600 * 24 * 30; // 30 days

        $menus = $user ? cache()->remember("menu_{$user->id_role}", $cacheTime, function () use ($user) {
            return Modul::with('menuAktif')
                ->aktif()
                ->v2()
                ->where('id_role', $user->id_role)
                ->get();
        }) : [];


        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'home_url' => route('dashboard'),
                'role' => session('role') ?? null,
                'menus' => $menus,
            ],
            'breadcrumbs' => $breadcrumbs,
        ];
    }
}
