<?php

use Illuminate\Contracts\Console\Kernel;
use Tests\Feature\CartApiTest;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();
$test = new CartApiTest('test_get_cart_successfully');
// run through PHPUnit programmatically, or just run php artisan test --filter=CartApiTest
