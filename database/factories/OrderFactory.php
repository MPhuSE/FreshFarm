<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_code' => Str::random(10),
            'user_id' => User::factory(),
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'recipient_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'shipping_address' => fake()->address(),
            'subtotal' => 100,
            'grand_total' => 100,
        ];
    }
}
