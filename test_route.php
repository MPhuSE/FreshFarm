<?php

use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);

$user = User::first();
if (! $user) {
    echo "No user\n";
    exit;
}
$request = Request::create('/api/v1/cart', 'GET');
$request->setUserResolver(function () use ($user) {
    return $user;
});
Auth::login($user);

$response = $kernel->handle($request);
echo 'Status: '.$response->status()."\n";
echo 'Content: '.$response->getContent()."\n";
