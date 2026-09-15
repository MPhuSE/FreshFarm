<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithTokenCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->bearerToken() && $request->cookies->has('freshfarm_token')) {
            $request->headers->set(
                'Authorization',
                'Bearer '.$request->cookies->get('freshfarm_token')
            );
        }

        return $next($request);
    }
}
