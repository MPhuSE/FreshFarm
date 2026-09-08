<?php

namespace App\Services;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Exception;

class CartService
{

    //lấy giỏ hàng và tổng tiền
    public function getCartDetails(int $userId): array {
        //tìm giỏ hàng của user, nếu chưa có dùng firstOrCreate để tạo mới
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        //load các bảng liên quan 
        $items = CartItem::with('product.category', 'product.inventory', 'product.primaryImage')
            ->where('cart_id', $cart->id)
            ->get();

        $subtotal = 0;
        $formattedItems = [];
        foreach ($items as $item) {
            $product = $item->product;
            //xử lý giá tiền
            $unitPrice  = (int) $product->price; 
            $linePrice = (int) round($unitPrice * $item->quantity); //lam tròn giá tiền
            $subtotal += $linePrice; //cộng dồn subtotal


             //tính tồn kho
            $availableStock = $product->inventory
            ? max (0, (float)($product->inventory->quantity_on_hand - $product->inventory->quantity_reserved) )
            : 0;

            $primaryImg = $product->primaryImage ? '/storage/' . $product->primaryImage->path : null;

            //api V1 format response
            $formattedItems[] = [
                'id' => $item->id,
                'product' => [
                    'id' => $product -> id,
                    'category' => $product -> category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                        'status' => $product->category->status,
                    ] : null,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'unit' => $product->unit,
                    'price' => $unitPrice,
                    'origin' => $product->origin,
                    'available_quantity' => $availableStock,
                    'status' => $product->status,
                    'primary_image_url' => $primaryImg,
                ],
                'quantity' => (float) $item->quantity,
                'unit_price' => $unitPrice,
                'line_total' => $linePrice,
            ];
        }

        //tính tổng 
        $shippingFee = $subtotal > 0 ? 30000 : 0; //phí ship cố định 30k
        $discount = 0; //giảm giá
        $grand_total = $subtotal + $shippingFee - $discount;

        return [
            'id' => $cart->id,
            'items' => $formattedItems,
            'summary' => [
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount' => $discount,
                'total' => max (0, $grand_total), //đảm bảo tổng >= 0
            ],
        ];

    }

    //thêm sản phẩm vào giỏ hàng
    public function addItem (int $userId, int $productId, float $quantity): array {
        $product = Product::with('inventory')->find($productId);

        if (!$product) {
            throw new Exception('Sản phẩm không tồn tại', 404);
        }

        $availableStock = $product->inventory
            ? ($product->inventory->quantity_on_hand - $product->inventory->quantity_reserved)
            : 0;
        
        if ($availableStock < $quantity) {
            throw new Exception('Số lượng sản phẩm vượt quá tồn kho', 400);
        }

        $cart = Cart::firstOrCreate(['user_id' => $userId]);
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();
        
        $newQuantity = $cartItem ? ($cartItem->quantity + $quantity) : $quantity; //check tồn kho, chua có thì thêm mới, có rồi thì cộng dồn
        if ($newQuantity > $availableStock) {
            throw new Exception('Số lượng sản phẩm vượt quá tồn kho', 400);
        }

        if ($cartItem){
            $cartItem -> update (['quantity' => $newQuantity]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $newQuantity,
            ]);
        }

        //trả về giỏ hàng mới nhất
        return $this->getCartDetails($userId);
    }


    //cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateItemQuantity(int $userId, int $cartItemId, float $quantity): array {
        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart) {
            throw new Exception('Giỏ hàng không tồn tại', 404);
        }

        $cartItem = CartItem::where('id', $cartItemId)
            ->where('cart_id', $cart->id)
            ->first();
        if (!$cartItem) {
            throw new Exception('Sản phẩm trong giỏ hàng không tồn tại', 404);
        }

        $product = Product::with('inventory')->find($cartItem->product_id);
        $availableStock = $product && $product->inventory
            ? ($product->inventory->quantity_on_hand - $product->inventory->quantity_reserved)
            : 0;
        if ($quantity > $availableStock) {
            throw new Exception('Số lượng sản phẩm vượt quá tồn kho', 409);
        }

        $cartItem->update(['quantity' => $quantity]);
        return $this->getCartDetails($userId);

    }

    //xóa sản phẩm khỏi giỏ hàng
    public function removeItem(int $userId, int $cartItemId): void {
        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart) {
            throw new Exception('Giỏ hàng không tồn tại', 404);
        }

        $cartItem = CartItem::where('id', $cartItemId)
            ->where('cart_id', $cart->id)
            ->first();
        if (!$cartItem) {
            throw new Exception('Sản phẩm trong giỏ hàng không tồn tại', 404);
        }

        $cartItem->delete();
    }

}

