<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssignTraceId
{
    public function handle(Request $request, Closure $next): Response
    {
        $traceId = $request->header('X-Request-ID') ?: (string) Str::uuid();
        $request->attributes->set('trace_id', $traceId);
        
        // Cấu hình ghi log có chứa context chuẩn (Mục 5: Logging và Tracking)
        Context::add('trace_id', $traceId);
        Context::add('url', $request->url());
        if ($request->user()) {
            Context::add('user_id', $request->user()->id);
        }

        $response = $next($request);
        $response->headers->set('X-Trace-Id', $traceId);

        return $response;
    }
}
