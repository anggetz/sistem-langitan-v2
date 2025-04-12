<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Tymon\JWTAuth\Contracts\Providers\JWT;

class AuthenticateWithToken extends Middleware
{
   
       /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }

    public function handle($request, Closure $next, ...$guards)
    {

        $guards[0] = 'api';

        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenInvalidException $e) {
           return $this->responseMessage('Token is Invalid');
        } catch (TokenExpiredException $e) {
           return $this->responseMessage('Token is Expired');
        } catch (JWTException $e) {
           return $this->responseMessage('Authorization Token not found');
        }

        if ($this->auth->guard($guards[0])->guest()) {
           return $this->responseMessage('Unauthenticated');
        }

        return $next($request);
    }

    private function responseMessage($message){
        return response()->json([
            'message' => $message,
            "status" => false,
            "data" => null
        ], Response::HTTP_UNAUTHORIZED);
    }
}
