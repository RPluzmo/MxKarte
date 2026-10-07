<?php

namespace App\Http\Middleware;

use App\Models\Rider;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PruneExpiredRiders
{
    public function handle(Request $request, Closure $next): Response
    {
        Rider::deleteExpiredOncePerDay();

        return $next($request);
    }
}
