<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RoleAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {

        $guard = $request->expectsJson() || $request->is('api/*') ? 'api' : 'web';

        $user = Auth::guard($guard)->user();

        if ($user && in_array($user->id_role, $roles)) {
            return $next($request);
        }

        // Jika request API, kembalikan response JSON, jika bukan arahkan ke login
        return $request->expectsJson() || $request->is('api/*')
            ? response()->json(['error' => 'Unauthorized'], 403)
            : redirect()->route('login');

    }
}
