<?php

namespace App\Services;

use App\Jobs\CancelUnpaidOrderJob;
use App\Models\CartItem;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function preview(int $userId, int $addressId, string $paymentMethod, ?string $couponCode = null): array
    {
        $cartDetails = $this->cartService->getCartDetails($userId);

        if (empty($cartDetails['items'])) {
            throw new Exception('Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán.', 400);
        }

        // THỰC TẾ: Truy vấn địa chỉ trong DB, đảm bảo địa chỉ này thuộc về User đang thao tác
        $address = UserAddress::where('id', $addressId)->where('user_id', $userId)->first();
        if (! $address) {
            throw new Exception('Địa chỉ giao hàng không tồn tại hoặc không hợp lệ.', 404);
        }

        $subtotal = $cartDetails['summary']['subtotal'];
        $shippingFee = $cartDetails['summary']['shipping_fee'];
        $discountAmount = 0;
        $couponData = null;

        if ($couponCode === 'XANH10') {
            $discountAmount = (int) ($subtotal * 0.1);
            $couponData = ['code' => $couponCode, 'discount' => $discountAmount];
        } elseif ($couponCode) {
            throw new Exception('Mã giảm giá không hợp lệ.', 422);
        }

        $grandTotal = max(0, $subtotal + $shippingFee - $discountAmount);

        return [
            'items' => $cartDetails['items'],
            'address' => [
                'id' => $address->id,
                'recipient_name' => $address->recipient_name,
                'phone' => $address->phone,
                'full_address' => $address->address,
            ],
            'subtotal' => $subtotal,
            'discount' => $discountAmount,
            'shipping_fee' => $shippingFee,
            'grand_total' => $grandTotal,
            'coupon' => $couponData,
        ];
    }

    public function placeOrder(int $userId, array $data): array
    {
        DB::beginTransaction();
        try {
            $previewData = $this->preview($userId, $data['address_id'], $data['payment_method'], $data['coupon_code'] ?? null);

            $order = Order::forceCreate([
                'order_code' => 'NSX-'.date('Ymd').'-'.rand(1000, 9999),
                'user_id' => $userId,
                'coupon_id' => null,
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'recipient_name' => $previewData['address']['recipient_name'],
                'phone' => $previewData['address']['phone'],
                'shipping_address' => $previewData['address']['full_address'],
                'subtotal' => $previewData['subtotal'],
                'discount_amount' => $previewData['discount'],
                'shipping_fee' => $previewData['shipping_fee'],
                'grand_total' => $previewData['grand_total'],
                'note' => $data['note'] ?? null,
            ]);

            foreach ($previewData['items'] as $itemData) {
                $productId = $itemData['product']['id'];
                $quantity = $itemData['quantity'];

                $inventory = Inventory::where('product_id', $productId)->lockForUpdate()->first();

                if (! $inventory || $inventory->quantity_on_hand < $quantity) {
                    throw new Exception('Sản phẩm '.$itemData['product']['name'].' không đủ số lượng.', 409);
                }

                $inventory->quantity_on_hand -= $quantity;
                $inventory->save();

                OrderItem::forceCreate([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_name' => $itemData['product']['name'],
                    'unit' => $itemData['product']['unit'],
                    'unit_price' => $itemData['unit_price'],
                    'quantity' => $quantity,
                    'line_total' => $itemData['line_total'],
                ]);
            }

            CartItem::whereHas('cart', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->delete();

            DB::commit();
            if ($data['payment_method'] === 'vnpay') {
                CancelUnpaidOrderJob::dispatch($order->id)->delay(now()->addMinutes(10));
            }

            return [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'subtotal' => $order->subtotal,
                'discount' => $order->discount_amount,
                'shipping_fee' => $order->shipping_fee,
                'grand_total' => $order->grand_total,
                'created_at' => $order->created_at->toIso8601String(),
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cancelOrder(int $orderId, string $note = 'Hệ thống tự động hủy do quá hạn thanh toán'): bool
    {
        return DB::transaction(function () use ($orderId, $note) {
            $order = Order::with('items')->lockForUpdate()->find($orderId);

            if (! $order || $order->status === 'cancelled' || $order->payment_status === 'paid') {
                return false; // đã thanh toán hoặc đã hủy thì bỏ qua
            }

            // cập nhật trạng thái
            $order->status = 'cancelled';
            $order->note = rtrim($order->note." ($note)");
            $order->save();

            // hoàn tồn kho
            foreach ($order->items as $item) {
                $inventory = Inventory::where('product_id', $item->product_id)->lockForUpdate()->first();
                if ($inventory) {
                    // cộng lại số lượng vào quantity_on_hand
                    $inventory->quantity_on_hand += $item->quantity;
                    $inventory->save();
                }
            }

            return true;
        });
    }
}
