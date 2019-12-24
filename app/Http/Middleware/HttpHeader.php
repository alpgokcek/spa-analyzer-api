<?php

namespace App\Http\Middleware;

use Closure;

class HttpHeader
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->header('X-Api-Version', '1.0.0');
        $response->header('System-Author', 'Sr. Web Developer Ahmet Selim CIL');

        return $response;
    }
}
