<?php

namespace App\Http\Middleware;

use App\Http\Response;
use Closure;
use Illuminate\Http\Request;

class AdminAuthorization
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
        if (!$request->payload) return Response::message("Unauthorized", 401);
        if (!isset($request->payload->role) || $request->payload->role !== "admin") return Response::message("Unauthorized", 401);

        return $next($request);
    }
}
