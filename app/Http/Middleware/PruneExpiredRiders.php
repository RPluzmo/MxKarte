<?php

namespace App\Http\Middleware;

use App\Support\ExpiredContentPruner;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PruneExpiredRiders
{
    public function handle(Request $request, Closure $next): Response
    {
        app(ExpiredContentPruner::class)->pruneOncePerDay();

        return $next($request);
    }
}
