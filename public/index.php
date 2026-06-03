<?php

if (isset($_GET['debug_db'])) {
    header('Content-Type: text/plain');
    try {
        $pdo = new PDO("mysql:host=bbxep7rxv8hul594p6jc-mysql.services.clever-cloud.com;port=3306;dbname=bbxep7rxv8hul594p6jc", "unpxbjtqthzw7cfe", "MMhhmU8R6zpxyhytFH7h");
        echo "Database connection to Clever Cloud successful!\n";
    } catch (\Throwable $e) {
        echo "Database connection failed: " . $e->getMessage() . "\n";
    }
    exit;
}

if (isset($_GET['debug_env'])) {
    header('Content-Type: text/plain');
    $env = $_ENV;
    if (isset($env['DB_PASSWORD'])) $env['DB_PASSWORD'] = '******';
    print_r($env);
    exit;
}

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
