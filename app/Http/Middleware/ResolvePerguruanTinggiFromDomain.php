<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PerguruanTinggi;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ResolvePerguruanTinggiFromDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('app.env') != 'production') {
            $idPt = config('app.id_perguruan_tinggi_default');
            $pt = PerguruanTinggi::find($idPt);
        } else {

            // Ambil host/domain saat ini
            $host = $request->getHost();

            // Key untuk cache
            $cacheKey = 'perguruan_tinggi:domain:' . $host;
            $masaCache = 60 * 60 * 24 * 7; // 1 minggu

            // Cek ke database berdasarkan domain
            $pt = Cache::remember($cacheKey, $masaCache, function () use ($host) {
                return PerguruanTinggi::byDomain($host)->first();
            });
        }

        if (!$pt) {
            // Jika tidak ditemukan, bisa redirect atau abort
            abort(404, 'Perguruan Tinggi tidak ditemukan');
        }

        app()->instance('pt', $pt);
        return $next($request);
    }
}
