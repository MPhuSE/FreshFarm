<?php

use App\Exceptions\ApiException;
use App\Http\Middleware\AssignTraceId;
use App\Http\Middleware\CheckRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Gắn trace_id cho toàn bộ route trong group "api"
        $middleware->api(prepend: [
            AssignTraceId::class,
        ]);
        // Alias middleware phân quyền role (Staff/Admin) dùng ở routes/api.php
        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $isApiRequest = fn (Request $request) => $request->is('api/*') || $request->expectsJson();

        $exceptions->shouldRenderJsonWhen($isApiRequest);

        // Helper dựng envelope lỗi chuẩn
        $envelope = function (Request $request, string $message, string $errorCode, int $status, ?array $errors = null) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
                'error_code' => $errorCode,
                'errors' => $errors,
                'trace_id' => $request->attributes->get('trace_id'),
            ], $status);
        };

        // 1. Lỗi nghiệp vụ tự throw từ Service (ApiException)
        $exceptions->render(function (ApiException $e, Request $request) use ($envelope, $isApiRequest) {
            if (! $isApiRequest($request)) {
                    return null;
            }

            return $envelope($request, $e->getMessage(), $e->getErrorCode(), $e->getStatusCode(), $e->getErrorsDetail());
        });

        // 2. Lỗi validate Form Request
        $exceptions->render(function (ValidationException $e, Request $request) use ($envelope, $isApiRequest) {
            if (! $isApiRequest($request)) {
                    return null;
            }

            return $envelope($request, 'Dữ liệu không hợp lệ.', 'VALIDATION_ERROR', 422, $e->errors());
        });

        // 3. Chưa đăng nhập / token hết hạn
        $exceptions->render(function (AuthenticationException $e, Request $request) use ($envelope, $isApiRequest) {
            if (! $isApiRequest($request)) {
                    return null;
            }

            return $envelope($request, 'Chưa đăng nhập hoặc token đã hết hạn.', 'UNAUTHENTICATED', 401);
        });

        // 4. Đã đăng nhập nhưng thiếu quyền (Policy/Gate)
        $exceptions->render(function (AuthorizationException $e, Request $request) use ($envelope, $isApiRequest) {
            if (! $isApiRequest($request)) {
                    return null;
            }

            return $envelope($request, 'Bạn không có quyền thực hiện thao tác này.', 'FORBIDDEN', 403);
        });

        // 5. Model::findOrFail() không tìm thấy
        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($envelope, $isApiRequest) {
            if (! $isApiRequest($request)) {
                    return null;
            }

            return $envelope($request, 'Không tìm thấy dữ liệu yêu cầu.', 'NOT_FOUND', 404);
        });

        // 6. Route không tồn tại
        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($envelope, $isApiRequest) {
            if (! $isApiRequest($request)) {
                    return null;
            }

            return $envelope($request, 'Không tìm thấy endpoint yêu cầu.', 'NOT_FOUND', 404);
        });

        // 7. Sai HTTP method (vd: gọi DELETE vào route chỉ có GET)
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) use ($envelope, $isApiRequest) {
            if (! $isApiRequest($request)) {
                    return null;
            }

            return $envelope($request, 'Phương thức HTTP không được hỗ trợ cho endpoint này.', 'METHOD_NOT_ALLOWED', 405);
        });

        // 8. Vượt rate limit (throttle)
        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) use ($envelope, $isApiRequest) {
            if (! $isApiRequest($request)) {
                    return null;
            }

            return $envelope($request, 'Bạn đã thực hiện quá nhiều yêu cầu, vui lòng thử lại sau.', 'TOO_MANY_ATTEMPTS', 429);
        });

        // 9. Các HttpException còn lại (413 file quá lớn, 415 sai định dạng...)
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($envelope, $isApiRequest) {
            if (! $isApiRequest($request)) {
                return null;
            }

            return $envelope(
                $request,
                $e->getMessage() ?: 'Đã xảy ra lỗi khi xử lý yêu cầu.',
                match ($e->getStatusCode()) {
                    413 => 'FILE_TOO_LARGE',
                    415 => 'UNSUPPORTED_MEDIA',
                    default => 'HTTP_ERROR',
                },
                $e->getStatusCode()
            );
        });

        // 10. Bắt tất cả lỗi hệ thống không lường trước — ẨN chi tiết khi production
        $exceptions->render(function (Throwable $e, Request $request) use ($envelope, $isApiRequest) {
            if (! $isApiRequest($request)) {
                return null;
            }

            // Ở local (APP_DEBUG=true) trả về null để Laravel hiện lỗi chi tiết, dễ debug
            if (config('app.debug')) {
                    return null;
            }

            return $envelope($request, 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.', 'INTERNAL_SERVER_ERROR', 500);
        });
    })->create();
