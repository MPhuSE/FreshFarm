<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\ApiException;

/**
 * Middleware phân quyền theo role, dùng dạng tham số trên route:
 *   ->middleware('role:staff,admin')  → cho phép cả staff và admin
 *   ->middleware('role:admin')        → chỉ admin
 *
 * LUÔN đứng SAU 'auth:sanctum' trong route group, vì cần $request->user()
 * đã được resolve trước đó.
 */

class CheckRole
{
    // THÊM: string ...$roles vào tham số của hàm handle
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Phòng hờ: nếu vì lý do gì đó middleware này chạy mà chưa qua
        // auth:sanctum (cấu hình route sai thứ tự), tránh lỗi null->role.
        if (! $user) {
            throw new ApiException('Chưa đăng nhập hoặc token đã hết hạn.', 'UNAUTHENTICATED', 401);
        }

        if ($user->status === 'locked') {
            throw new ApiException('Tài khoản đã bị khóa.', 'ACCOUNT_LOCKED', 403);
        }

        if (! in_array($user->role, $roles, true)) {
            throw new ApiException('Bạn không có quyền thực hiện thao tác này.', 'FORBIDDEN', 403);
        }

        return $next($request);
    }
}