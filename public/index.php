<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
try {
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';

    // Force bootstrapping here to catch early boot exceptions!
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    header('Content-Type: text/plain', true, 500);
    echo "PRIMARY BOOTSTRAP ERROR: " . $e->getMessage() . "\n\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n\n";
    echo "Trace:\n" . $e->getTraceAsString();
    exit(1);
}
