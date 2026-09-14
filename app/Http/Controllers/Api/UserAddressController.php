<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreAddressRequest;
use App\Http\Requests\User\UpdateAddressRequest;
use App\Models\UserAddress;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserAddressController extends Controller
{
    use ApiResponse;

    #[OA\Get(
        path: '/api/v1/user/addresses',
        summary: 'Lấy danh sách địa chỉ',
        tags: ['Address'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Thành công'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->orderBy('is_default', 'desc')->get();

        return $this->respondSuccess($addresses, 'Lấy danh sách địa chỉ thành công.');
    }

    #[OA\Post(
        path: '/api/v1/user/addresses',
        summary: 'Thêm địa chỉ giao hàng mới',
        tags: ['Address'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['recipient_name', 'phone', 'address'],
                properties: [
                    new OA\Property(property: 'recipient_name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'phone', type: 'string', example: '0123456789'),
                    new OA\Property(property: 'address', type: 'string', example: '123 Main St'),
                    new OA\Property(property: 'is_default', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Thành công'),
        ]
    )]
    public function store(StoreAddressRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($request->validated('is_default')) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create($request->validated());

        return $this->respondSuccess($address, 'Thêm địa chỉ giao hàng thành công.', 201);
    }

    #[OA\Put(
        path: '/api/v1/user/addresses/{address}',
        summary: 'Cập nhật địa chỉ giao hàng',
        tags: ['Address'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'address', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'address', type: 'string', example: 'New Address'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Thành công'),
            new OA\Response(response: 403, description: 'Không có quyền'),
        ]
    )]
    public function update(UpdateAddressRequest $request, UserAddress $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return $this->respondError('Không có quyền', 'FORBIDDEN', null, 403);
        }

        if ($request->validated('is_default')) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($request->validated());

        return $this->respondSuccess($address, 'Cập nhật địa chỉ giao hàng thành công.');
    }

    #[OA\Delete(
        path: '/api/v1/user/addresses/{address}',
        summary: 'Xóa địa chỉ giao hàng',
        tags: ['Address'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'address', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Xóa thành công'),
            new OA\Response(response: 403, description: 'Không có quyền'),
        ]
    )]
    public function destroy(Request $request, UserAddress $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return $this->respondError('Không có quyền', 'FORBIDDEN', null, 403);
        }

        $address->delete();

        return $this->respondSuccess(null, 'Xóa địa chỉ giao hàng thành công.');
    }
}
