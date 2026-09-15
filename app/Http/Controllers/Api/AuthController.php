<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    use ApiResponse;

    private const AUTH_COOKIE = 'freshfarm_token';
    private const AUTH_MARKER_COOKIE = 'freshfarm_auth';
    private const AUTH_COOKIE_MINUTES = 10080;

    #[OA\Post(
        path: '/api/v1/auth/register',
        summary: 'Đăng ký tài khoản',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Nguyen Van A'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'phone', type: 'string', example: '0123456789'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Đăng ký thành công'),
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'password' => Hash::make($request->validated('password')),
        ]);

        return $this->respondAuthenticated($request, $user, 'Đăng ký tài khoản thành công.', 201);
    }

    #[OA\Post(
        path: '/api/v1/auth/login',
        summary: 'Đăng nhập',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Đăng nhập thành công'),
            new OA\Response(response: 401, description: 'Sai thông tin đăng nhập'),
            new OA\Response(response: 403, description: 'Tài khoản bị khóa'),
            new OA\Response(response: 422, description: 'Sai thông tin'),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return $this->respondError('Tài khoản hoặc mật khẩu không chính xác.', 'INVALID_CREDENTIALS', null, 401);
        }

        if ($user->status === 'locked') {
            return $this->respondError('Tài khoản của bạn đã bị khóa.', 'ACCOUNT_LOCKED', null, 403);
        }

        return $this->respondAuthenticated($request, $user, 'Đăng nhập thành công.');
    }

    private function respondAuthenticated(Request $request, User $user, string $message, int $status = 200): JsonResponse
    {
        $plainTextToken = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'user' => $user,
            'token_type' => 'Bearer',
        ];

        if (! $request->boolean('cookie_auth') && $request->header('X-Use-Cookie-Auth') !== '1') {
            $data['token'] = $plainTextToken;
        }

        return $this->respondSuccess($data, $message, $status)
            ->cookie(
                self::AUTH_COOKIE,
                $plainTextToken,
                self::AUTH_COOKIE_MINUTES,
                '/',
                null,
                $request->isSecure(),
                true,
                false,
                'Lax'
            )
            ->cookie(
                self::AUTH_MARKER_COOKIE,
                '1',
                self::AUTH_COOKIE_MINUTES,
                '/',
                null,
                $request->isSecure(),
                false,
                false,
                'Lax'
            );
    }

    #[OA\Post(
        path: '/api/v1/auth/logout',
        summary: 'Đăng xuất',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Đăng xuất thành công'),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->respondSuccess(null, 'Đăng xuất thành công.')
            ->withoutCookie(self::AUTH_COOKIE)
            ->withoutCookie(self::AUTH_MARKER_COOKIE);
    }

    #[OA\Get(
        path: '/api/v1/me',
        summary: 'Lấy thông tin tài khoản hiện tại',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Thông tin user'),
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        return $this->respondSuccess($request->user(), 'Lấy hồ sơ người dùng hiện tại thành công.');
    }
}
