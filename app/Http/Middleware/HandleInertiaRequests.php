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
        return [
            ...parent::share($request),

            // TODO: Hapus bagian ini jika sudah diimplementasi
            'auth' => [
                'user' => [
                    'name' => 'Nama User',
                    'email' => 'user@company.com',
                    'role' => 'User' // Ganti ke 'User' atau Lainnya jika ingin melihat menu lain
                ]
            ]

            // TODO: Aktifkan bagian ini jika sudah diimplementasi
            // 'auth' => [
            //     'user' => $request->user(),
            // ],
        ];
    }
}
