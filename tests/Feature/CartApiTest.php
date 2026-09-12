<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class CartApiTest extends TestCase
{
    use RefreshDatabase; 

    protected function setUp(): void
    {
        parent::setUp();

        // Không cần gán cứng 'id' => 1 nữa, cứ để DB tự tăng
        User::create([
            'name' => 'Nguyễn Văn An',
            'email' => 'an.nguyen@example.com',
            'password' => Hash::make('password123'),
            'phone' => '0901234567',
        ]);

        $category = Category::create([
            'name' => 'Trái Cây',
            'slug' => 'trai-cay'
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Cam Vinh',
            'slug' => 'cam-vinh',
            'sku' => 'CAM-01',
            'price' => 45000,
            'unit' => 'kg',
            'status' => 'active'
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'quantity' => 100
        ]);
    }

    public function test_get_cart_successfully()
    {
        $user = User::first(); // Lấy user đầu tiên

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

    public function test_add_item_to_cart_success()
    {
        $user = User::first();
        $product = Product::first(); // Lấy sản phẩm đầu tiên thay vì id = 1

        $response = $this->actingAs($user)->postJson('/api/v1/cart/items', [
            'product_id' => $product->id, 
            'quantity' => 2
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Thêm sản phẩm vào giỏ thành công.'
            ]);
    }

    public function test_add_item_invalid_quantity_returns_422()
    {
        $user = User::first();
        $product = Product::first();

        $response = $this->actingAs($user)->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => -5 
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'VALIDATION_ERROR'
            ]);
    }

    public function test_update_cart_item_quantity()
    {
        $user = User::first();
        $product = Product::first();

        $addResponse = $this->actingAs($user)->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
        
        $cartItemId = $addResponse->json('data.items.0.id');

        $response = $this->actingAs($user)->patchJson("/api/v1/cart/items/{$cartItemId}", [
            'quantity' => 5
        ]);
        $response->dump();


        $response->assertStatus(200)
            ->assertJsonPath('data.items.0.quantity', 5) 
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật số lượng trong giỏ thành công.'
            ]);
    }

    public function test_delete_cart_item()
    {
        $user = User::first();
        $product = Product::first();

        $addResponse = $this->actingAs($user)->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
        
        $cartItemId = $addResponse->json('data.items.0.id');

        $response = $this->actingAs($user)->deleteJson("/api/v1/cart/items/{$cartItemId}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xóa sản phẩm khỏi giỏ thành công.'
            ]);
    }
}