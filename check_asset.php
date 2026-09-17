<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Simulate request from ngrok
$request = Illuminate\Http\Request::create(
    'https://sitting-trophy-tanned.ngrok-free.dev/',
    'GET',
    [],
    [],
    [],
    [
        'HTTP_HOST' => 'sitting-trophy-tanned.ngrok-free.dev',
        'HTTP_X_FORWARDED_FOR' => '1.2.3.4',
        'HTTP_X_FORWARDED_PROTO' => 'https',
        'HTTP_X_FORWARDED_HOST' => 'sitting-trophy-tanned.ngrok-free.dev',
        'HTTP_X_FORWARDED_PORT' => '443',
        'SERVER_PORT' => 80,
    ]
);

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . PHP_EOL;
echo "URL generated: " . url('/orders') . PHP_EOL;
echo "Asset generated: " . asset('tv4/assets/css/style.css') . PHP_EOL;

