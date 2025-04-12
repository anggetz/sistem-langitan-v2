<?php

namespace App\Providers;

use Illuminate\Http\Response;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Vite;
use App\Services\Auth\ApiAuthService;
use App\Services\Auth\WebAuthService;
use Illuminate\Support\ServiceProvider;
use App\Services\Auth\SessionAuthService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (request()->is('api/*')) {
            $this->app->bind(AuthService::class, ApiAuthService::class);
        } else if (request()->has('id_session')) {
            $this->app->bind(AuthService::class, SessionAuthService::class);
        } else {
            $this->app->bind(AuthService::class, WebAuthService::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Response::macro('success', function ($data, $message = 'OK', $statusCode = 200) {
            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        });

        Response::macro('error', function ($message, $statusCode = 400) {
            return response()->json([
                'status' => false,
                'message' => $message,
            ], $statusCode);
        });
    }
}
