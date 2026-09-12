<?php

namespace Tests\Feature\Api\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_get_summary_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(2)->create();

        Order::factory()->count(3)->create(['status' => 'completed', 'grand_total' => 100]);
        Order::factory()->count(1)->create(['status' => 'pending', 'grand_total' => 50]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/reports/summary');

        $response->assertStatus(200)
            ->assertJson([
                'total_users' => User::count(),
                'total_orders' => 4,
                'total_revenue' => 300,
            ]);
    }
}
