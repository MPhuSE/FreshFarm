<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_addresses(): void
    {
        $user = User::factory()->create();
        UserAddress::factory()->count(2)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/user/addresses');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'addresses');
    }

    public function test_user_can_create_address(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/user/addresses', [
                'recipient_name' => 'John Doe',
                'phone' => '0123456789',
                'address' => '123 Main St',
                'is_default' => true,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('address.recipient_name', 'John Doe');

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $user->id,
            'address' => '123 Main St',
        ]);
    }

    public function test_user_can_update_address(): void
    {
        $user = User::factory()->create();
        $address = UserAddress::factory()->create(['user_id' => $user->id, 'address' => 'Old Address']);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/user/addresses/{$address->id}", [
                'address' => 'New Address',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('address.address', 'New Address');
    }

    public function test_user_can_delete_address(): void
    {
        $user = User::factory()->create();
        $address = UserAddress::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/user/addresses/{$address->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('user_addresses', ['id' => $address->id]);
    }
}
