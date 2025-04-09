<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

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
        $menus = session('menus') ?? [];

        // Auto build breadcrumbs array
        $breadcrumbs = [
            ['Home', route('dashboard')],
        ];

        // /role/menu-1/sub-menu-1
        //   0     1      2
        // Find path from url
        foreach ($menus as $menu) {
            if ($request->segment(2) == $menu['path']) {
                $breadcrumbs[] = [$menu['name']];
                foreach ($menu['subMenus'] as $subMenu) {
                    if ($request->segment(3) == $subMenu['path']) {
                        $breadcrumbs[] = [$subMenu['name'], '/' . $role['prefix_url'] . '/' . $menu['path'] . '/' . $subMenu['path']];
                        break;
                    }
                }
                break;
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'home_url' => route('dashboard'),
                'role' => session('role') ?? null,
                'menus' => session('menus') ?? null,
            ],
            'breadcrumbs' => $breadcrumbs,
        ];
    }
}
