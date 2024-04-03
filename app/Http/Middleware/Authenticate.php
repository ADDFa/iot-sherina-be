<?php

namespace App\Http\Middleware;

use App\Http\Response;
use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) return Response::message("Unauthorized", 401);

        try {
            $payload = JWT::decode($token, new Key(env("SECRET_JWT_KEY"), "HS256"));
            $request->payload = $payload;

            return $next($request);
        } catch (Exception $e) {
            return Response::message($e->getMessage(), 500);
        }
    }
}
