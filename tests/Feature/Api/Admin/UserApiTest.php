<?php

namespace Tests\Feature\Api\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_lock_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/admin/users/{$user->id}/lock");

        $response->assertStatus(200)
            ->assertJsonPath('user.status', 'locked');
    }

    public function test_admin_can_change_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/admin/users/{$user->id}/role", [
                'role' => 'admin',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.role', 'admin');
    }

    public function test_customer_cannot_access_admin_routes(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $user = User::factory()->create();

        $response = $this->actingAs($customer, 'sanctum')
            ->putJson("/api/admin/users/{$user->id}/lock");

        $response->assertStatus(403);
    }
}
