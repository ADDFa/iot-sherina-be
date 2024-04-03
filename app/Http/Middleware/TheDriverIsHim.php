<?php

namespace App\Http\Middleware;

use App\Http\Response;
use App\Models\Credential;
use Closure;
use Illuminate\Http\Request;

class TheDriverIsHim
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

        if (isset($request->payload->role) && $request->payload->role === "admin") return $next($request);

        $driverId = $request->payload->driver->id;
        $currentId = $request->driver->id ?? $request->driverStatus->driver_id;
        if ($driverId !== $currentId) return Response::message("Unauthorized", 401);

        return $next($request);
    }
}
