<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeRoleRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    use ApiResponse;

    #[OA\Patch(
        path: '/api/v1/admin/users/{user}/status',
        summary: 'Khóa hoặc mở khóa tài khoản',
        tags: ['Admin'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(property: 'status', type: 'string', enum: ['active', 'locked'], example: 'locked'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Thành công'),
            new OA\Response(response: 403, description: 'Không có quyền'),
            new OA\Response(response: 409, description: 'Không thể tự khóa tài khoản của mình'),
        ]
    )]
    public function lock(Request $request, User $user): JsonResponse
    {
        if ($request->user()->id === $user->id) {
            return $this->respondError('Không thể tự khóa/mở khóa tài khoản của mình.', 'CANNOT_LOCK_SELF', null, 409);
        }

        $validated = $request->validate([
            'status' => 'required|in:active,locked',
        ]);

        $user->update(['status' => $validated['status']]);

        return $this->respondSuccess($user, 'Khóa/mở khóa tài khoản thành công.');
    }

    #[OA\Patch(
        path: '/api/v1/admin/users/{user}/role',
        summary: 'Đổi quyền người dùng',
        tags: ['Admin'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['role'],
                properties: [
                    new OA\Property(property: 'role', type: 'string', enum: ['customer', 'staff', 'admin'], example: 'staff'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Thành công'),
            new OA\Response(response: 403, description: 'Không có quyền'),
            new OA\Response(response: 409, description: 'Không thể tự thay đổi vai trò của mình'),
        ]
    )]
    public function changeRole(ChangeRoleRequest $request, User $user): JsonResponse
    {
        if ($request->user()->id === $user->id) {
            return $this->respondError('Không thể tự thay đổi vai trò của mình.', 'CANNOT_CHANGE_SELF_ROLE', null, 409);
        }

        $user->update(['role' => $request->validated('role')]);

        return $this->respondSuccess($user, 'Thay đổi vai trò thành công.');
    }
}
