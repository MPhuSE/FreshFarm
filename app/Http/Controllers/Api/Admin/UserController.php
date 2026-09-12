<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeRoleRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Put(
        path: '/api/admin/users/{user}/lock',
        summary: 'Khóa hoặc mở khóa tài khoản',
        tags: ['Admin'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Thành công'),
            new OA\Response(response: 403, description: 'Không có quyền'),
        ]
    )]
    public function lock(User $user): JsonResponse
    {
        $newStatus = $user->status === 'active' ? 'locked' : 'active';

        $user->update(['status' => $newStatus]);

        return response()->json([
            'message' => "User status updated to {$newStatus}",
            'user' => $user,
        ]);
    }

    #[OA\Put(
        path: '/api/admin/users/{user}/role',
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
                    new OA\Property(property: 'role', type: 'string', enum: ['customer', 'admin'], example: 'admin'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Thành công'),
            new OA\Response(response: 403, description: 'Không có quyền'),
        ]
    )]
    public function changeRole(ChangeRoleRequest $request, User $user): JsonResponse
    {
        $user->update(['role' => $request->validated('role')]);

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $user,
        ]);
    }
}
