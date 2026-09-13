<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Category;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class CheckoutApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $address;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Tạo User
        $this->user = User::create([
            'name' => 'Nguyễn Văn An',
            'email' => 'an.nguyen@example.com',
            'password' => Hash::make('password123'),
            'phone' => '0901234567',
        ]);

        // 2. TẠO ĐỊA CHỈ THẬT CHO USER NÀY
        $this->address = UserAddress::create([
            'user_id' => $this->user->id,
            'recipient_name' => 'Nguyễn Văn An',
            'phone' => '0901234567',
            'address' => '12 Nguyễn Huệ, Quận 1, TP.HCM'
        ]);

        // 3. Tạo Sản phẩm & Tồn kho
        $category = Category::create(['name' => 'Trái Cây', 'slug' => 'trai-cay']);
        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Cam Vinh',
            'slug' => 'cam-vinh',
            'sku' => 'CAM-01',
            'price' => 45000,
            'unit' => 'kg',
            'status' => 'active'
        ]);

        Inventory::create([
            'product_id' => $this->product->id,
            'quantity' => 100
        ]);

        // 4. Khởi tạo Giỏ hàng
        $cart = Cart::create(['user_id' => $this->user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
    }

    public function test_checkout_preview_success()
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/checkout/preview', [
            'address_id' => $this->address->id, // Truyền ID địa chỉ thật
            'payment_method' => 'cod',
            'coupon_code' => 'XANH10'
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.subtotal', 90000)
            ->assertJsonPath('data.discount', 9000)
            ->assertJsonPath('data.shipping_fee', 30000)
            ->assertJsonPath('data.grand_total', 111000);
    }

    public function test_checkout_preview_fails_if_cart_is_empty()
    {
        CartItem::query()->delete();

        $response = $this->actingAs($this->user)->postJson('/api/v1/checkout/preview', [
            'address_id' => $this->address->id,
            'payment_method' => 'cod'
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error_code' => 'CART_CHANGED'
            ]);
    }

    public function test_checkout_preview_fails_with_invalid_coupon()
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/checkout/preview', [
            'address_id' => $this->address->id,
            'payment_method' => 'cod',
            'coupon_code' => 'MA_HET_HAN'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'INVALID_COUPON'
            ]);
    }

    public function test_place_order_success_and_verifies_database_transaction()
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/orders', [
            'address_id' => $this->address->id,
            'payment_method' => 'cod',
            'coupon_code' => 'XANH10',
            'note' => 'Giao hàng cẩn thận',
            'idempotency_key' => 'unique-req-123'
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('inventories', [
            'product_id' => $this->product->id,
            'quantity' => 98
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'product_id' => $this->product->id
        ]);
        
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'grand_total' => 111000,
            'payment_method' => 'cod'
        ]);
    }

    public function test_place_order_fails_due_to_insufficient_stock()
    {
        Inventory::where('product_id', $this->product->id)->update(['quantity' => 1]);

        $response = $this->actingAs($this->user)->postJson('/api/v1/orders', [
            'address_id' => $this->address->id,
            'payment_method' => 'cod',
            'idempotency_key' => 'unique-req-456'
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'error_code' => 'OUT_OF_STOCK'
            ]);

        $this->assertDatabaseMissing('orders', [
            'user_id' => $this->user->id
        ]);
    }

    public function test_place_order_validation_fails_missing_payment_method()
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/orders', [
            'address_id' => $this->address->id,
            'idempotency_key' => 'unique-req-789'
        ]);

        $response->assertStatus(422);
    }
}