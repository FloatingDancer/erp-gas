<?php

use App\Models\User;
use App\Models\Delivery;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'test@example.com')->first();
if (!$user) {
    $user = User::first();
}
auth()->login($user);

$deliveries = Delivery::with(['order', 'driver'])->get();
$html = view('deliveries.index', compact('deliveries'))->render();

file_put_contents(__DIR__ . '/deliveries_render.html', $html);
echo "HTML rendered successfully!\n";
