<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();
DB::beginTransaction();
try {
    $user = User::create(['name' => 'Test', 'email' => rand().'@test.com', 'password' => '123']);
    $cat = Category::create(['name' => 'Cat', 'slug' => 'cat', 'status' => 'active']);
    $prod = Product::create(['category_id' => $cat->id, 'sku' => '1', 'name' => '1', 'slug' => '1', 'unit' => 'kg', 'price' => 10]);
    Inventory::create(['product_id' => $prod->id, 'quantity_on_hand' => 100]);
    $cart = Cart::create(['user_id' => $user->id]);
    CartItem::create(['cart_id' => $cart->id, 'product_id' => $prod->id, 'quantity' => 2]);

    $service = app(CartService::class);

    echo "Before getCartDetails\n";
    $details = $service->getCartDetails($user->id);
    echo "After getCartDetails\n";
    var_dump($details);
} finally {
    DB::rollBack();
}
