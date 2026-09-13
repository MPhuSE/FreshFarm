<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Str;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $code = 500;
                $errorCode = 'INTERNAL_ERROR';
                $message = 'Có lỗi xảy ra.';
                $errors = null;

                if ($e instanceof ValidationException) {
                    $code = 422;
                    $errorCode = 'VALIDATION_ERROR';
                    $message = 'Dữ liệu không hợp lệ.';
                    $errors = $e->errors();
                } elseif ($e instanceof AuthenticationException) {
                    $code = 401;
                    $errorCode = 'UNAUTHENTICATED';
                    $message = 'Chưa xác thực hoặc token hết hạn.';
                } elseif ($e instanceof AccessDeniedHttpException) {
                    $code = 403;
                    $errorCode = 'FORBIDDEN';
                    $message = 'Thiếu quyền truy cập.';
                } elseif ($e instanceof NotFoundHttpException) {
                    $code = 404;
                    $errorCode = 'NOT_FOUND';
                    $message = 'Không tìm thấy tài nguyên.';
                } else {
                    $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                    $errorCode = 'SERVER_ERROR';
                    $message = env('APP_DEBUG') ? $e->getMessage() : 'Lỗi hệ thống.';
                }

                // Nếu controller ném exception HTTP với JSON response thì ưu tiên response đó.
                // Thường các lỗi HTTP có thể được trả về dạng khác, nhưng đây ta override chuẩn.

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'data' => null,
                    'error_code' => $errorCode,
                    'errors' => $errors,
                    'trace_id' => Str::uuid()->toString(),
                ], $code);
            }
        });
    })->create();
