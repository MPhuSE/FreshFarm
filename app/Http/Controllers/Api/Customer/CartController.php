<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Services\CartService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class CartController extends Controller
{
    use ApiResponse;

    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[OA\Get(
        path: '/api/v1/cart',
        summary: 'Lấy giỏ hàng',
        security: [['bearerAuth' => []]],
        tags: ['Cart']
    )]
    #[OA\Response(response: 200, description: 'Lấy giỏ hàng hiện tại thành công.')]
    public function index(): JsonResponse
    {
        $data = $this->cartService->getCartDetails(Auth::id());

        return $this->respondSuccess($data, 'Lấy giỏ hàng hiện tại thành công.');
    }

    #[OA\Post(
        path: '/api/v1/cart/items',
        summary: 'Thêm sản phẩm vào giỏ',
        security: [['bearerAuth' => []]],
        tags: ['Cart']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['product_id', 'quantity'],
            properties: [
                new OA\Property(property: 'product_id', type: 'integer', example: 1),
                new OA\Property(property: 'quantity', type: 'number', example: 1),
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Thêm sản phẩm vào giỏ thành công.')]
    public function store(AddToCartRequest $request): JsonResponse
    {
        try {
            $data = $this->cartService->addItem(
                Auth::id(),
                (int) $request->validated('product_id'),
                (float) $request->validated('quantity')
            );

            return $this->respondSuccess($data, 'Thêm sản phẩm vào giỏ thành công.', 201);
        } catch (Exception $e) {
            return $this->handleCartException($e);
        }
    }

    #[OA\Patch(
        path: '/api/v1/cart/items/{id}',
        summary: 'Cập nhật số lượng sản phẩm trong giỏ',
        security: [['bearerAuth' => []]],
        tags: ['Cart']
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['quantity'],
            properties: [
                new OA\Property(property: 'quantity', type: 'number', example: 2),
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Cập nhật số lượng trong giỏ thành công.')]
    public function update(UpdateCartItemRequest $request, $id): JsonResponse
    {
        try {
            $data = $this->cartService->updateItem(
                Auth::id(),
                (int) $id,
                (float) $request->validated('quantity')
            );

            return $this->respondSuccess($data, 'Cập nhật số lượng trong giỏ thành công.');
        } catch (Exception $e) {
            return $this->handleCartException($e);
        }
    }

    #[OA\Delete(
        path: '/api/v1/cart/items/{id}',
        summary: 'Xóa sản phẩm khỏi giỏ',
        security: [['bearerAuth' => []]],
        tags: ['Cart']
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Xóa sản phẩm khỏi giỏ thành công.')]
    public function destroy($id): JsonResponse
    {
        try {
            $this->cartService->removeItem(Auth::id(), (int) $id);

            return $this->respondSuccess(null, 'Xóa sản phẩm khỏi giỏ thành công.');
        } catch (Exception $e) {
            return $this->handleCartException($e);
        }
    }

    private function handleCartException(Exception $e): JsonResponse
    {
        $code = $e->getCode() ?: 400;
        $msg = match ($e->getMessage()) {
            'PRODUCT_NOT_FOUND' => 'Không tìm thấy sản phẩm.',
            'OUT_OF_STOCK' => 'Sản phẩm đã hết hàng.',
            'INSUFFICIENT_STOCK' => 'Số lượng vượt quá tồn kho khả dụng.',
            'CART_ITEM_NOT_FOUND' => 'Không tìm thấy sản phẩm trong giỏ hàng.',
            default => $e->getMessage(),
        };

        return $this->respondError($msg, $e->getMessage(), $code);
    }
}
