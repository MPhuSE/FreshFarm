<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    #[OA\Put(
        path: '/api/user/profile',
        summary: 'Cập nhật thông tin cá nhân',
        tags: ['Profile'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'New Name'),
                    new OA\Property(property: 'phone', type: 'string', example: '0987654321'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Cập nhật thành công'),
        ]
    )]
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update($request->validated());

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->refresh(),
        ]);
    }
}
