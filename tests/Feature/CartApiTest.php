<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartApiTest extends TestCase
{
    /**
     * Bật Transaction TRƯỚC mỗi bài test để ghi nhận thay đổi
     */
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    /**
     * Hoàn tác (Rollback) SAU MỖI bài test để giữ database sạch sẽ
     */
    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    /**
     * Test 1: Khách hàng xem giỏ hàng thành công
     */
    public function test_get_cart_successfully()
    {
        $user = User::find(1); // User Nguyễn Văn An từ hi.sql

        $response = $this->actingAs($user)->getJson('/api/v1/cart');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'items',
                    'summary' => ['subtotal', 'discount', 'shipping_fee', 'grand_total']
                ],
                'meta',
                'errors'
            ]);
    }

    /**
     * Test 2: Khách hàng thêm sản phẩm vào giỏ thành công
     */
    public function test_add_item_to_cart_success()
    {
        $user = User::find(1);

        $response = $this->actingAs($user)->postJson('/api/v1/cart/items', [
            'product_id' => 1, // Cam Vinh
            'quantity' => 2
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Thêm sản phẩm vào giỏ thành công.'
            ]);
    }

    /**
     * Test 3: Bắt lỗi Validation khi khách nhập số lượng âm
     */
    public function test_add_item_invalid_quantity_returns_422()
    {
        $user = User::find(1);

        $response = $this->actingAs($user)->postJson('/api/v1/cart/items', [
            'product_id' => 1,
            'quantity' => -5 // Số lượng âm (Không hợp lệ)
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'VALIDATION_ERROR'
            ]);
    }

    /**
     * Test 4: Cập nhật số lượng sản phẩm trong giỏ
     */
    public function test_update_cart_item_quantity()
    {
        $user = User::find(1);

        // Bước 1: Gọi API tạo trước 1 item trong giỏ
        $addResponse = $this->actingAs($user)->postJson('/api/v1/cart/items', [
            'product_id' => 1,
            'quantity' => 2
        ]);
        
        // Trích xuất cart_item_id vừa được tạo ra từ JSON
        $cartItemId = $addResponse->json('data.items.0.id');

        // Bước 2: Gọi API cập nhật item đó lên 5 kg
        $response = $this->actingAs($user)->patchJson("/api/v1/cart/items/{$cartItemId}", [
            'quantity' => 5
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.items.0.quantity', 5) // Kiểm tra giá trị đã lên 5 chưa
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật số lượng trong giỏ thành công.'
            ]);
    }

    /**
     * Test 5: Xóa sản phẩm khỏi giỏ hàng
     */
    public function test_delete_cart_item()
    {
        $user = User::find(1);

        // Bước 1: Gọi API tạo trước 1 item trong giỏ
        $addResponse = $this->actingAs($user)->postJson('/api/v1/cart/items', [
            'product_id' => 1,
            'quantity' => 2
        ]);
        
        $cartItemId = $addResponse->json('data.items.0.id');

        // Bước 2: Gọi API xóa item đó
        $response = $this->actingAs($user)->deleteJson("/api/v1/cart/items/{$cartItemId}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xóa sản phẩm khỏi giỏ thành công.'
            ]);
    }
}