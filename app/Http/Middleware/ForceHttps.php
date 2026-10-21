<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Redirect plain-HTTP requests to HTTPS in production. When TLS is
     * terminated before PHP (load balancer / reverse proxy) the
     * X-Forwarded-Proto header is honoured so no redirect loop occurs even
     * though the proxy chain itself is not trusted for IP resolution.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->isProduction()
            && ! $request->secure()
            && $request->header('X-Forwarded-Proto') !== 'https') {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
