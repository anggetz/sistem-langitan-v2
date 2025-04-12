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

        $user = Auth::user();
        $cacheTime = 3600 * 24 * 30; // 30 days

        $menus = $user ? cache()->remember("menu_{$user->id_role}", $cacheTime, function () use ($user) {
            return Modul::with('menuV2Aktif')
                ->v2()
                ->aktif()
                ->where('id_role', $user->id_role)
                ->get();
        }) : [];

        // Auto build breadcrumbs array
        $breadcrumbs = $role ? [
            [$user->role->nm_role],
        ] : [];

        foreach ($menus as $menu) {
            if ($request->segment(2) == $menu->nm_modul) {
                $breadcrumbs[] = [$menu->title];
                foreach ($menu->menuV2Aktif as $subMenu) {
                    if ($request->segment(3) == $subMenu->nm_menu) {
                        $breadcrumbs[] = [$subMenu->title, '/' . $role->prefix_url . '/' . $menu->nm_modul . '/' . $subMenu->nm_menu];
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
                'menus' => $menus,
            ],
            'breadcrumbs' => $breadcrumbs,
        ];
    }
}
