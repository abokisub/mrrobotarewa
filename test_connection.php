<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\BybitService;

$service = new BybitService();
echo "Testing Bybit Connection...\n";
$balance = $service->getWalletBalance();

if ($balance['success']) {
    echo "✅ Success! Connection established.\n";
    print_r($balance['data']);
} else {
    echo "❌ Failed: " . $balance['message'] . "\n";
    if (isset($balance['code'])) echo "Error Code: " . $balance['code'] . "\n";
}
