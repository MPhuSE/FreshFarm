<?php

namespace Tests\Feature\Api\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_access_admin_orders()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $response = $this->actingAs($customer)->getJson('/api/v1/admin/orders');
        $response->assertStatus(403);
    }

    public function test_admin_can_update_order_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)->patchJson("/api/v1/admin/orders/{$order->id}/status", [
            'status' => 'confirmed',
        ]);

        $response->assertStatus(200)->assertJsonPath('data.status', 'confirmed');
    }

    public function test_admin_cannot_skip_order_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create(['status' => 'pending']);

        // Cố tình đẩy thẳng lên delivered từ pending
        $response = $this->actingAs($admin)->patchJson("/api/v1/admin/orders/{$order->id}/status", [
            'status' => 'delivered',
        ]);

        $response->assertStatus(409)->assertJsonPath('error_code', 'INVALID_ORDER_TRANSITION');
    }
}
