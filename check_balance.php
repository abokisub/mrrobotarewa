<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\BybitService;

$service = new BybitService();
$types = ['FUND', 'UNIFIED', 'CONTRACT'];

echo "Checking all wallet balances...\n";

foreach ($types as $type) {
    echo "\nChecking $type Account:\n";
    $response = $service->getWalletBalance($type);
    if ($response['success'] && isset($response['data']['list'][0])) {
        $coins = $response['data']['list'][0]['coin'] ?? [];
        if (empty($coins)) {
            echo "No coins found in this wallet.\n";
        } else {
            foreach ($coins as $coin) {
                echo "- " . $coin['coin'] . ": " . $coin['walletBalance'] . "\n";
            }
        }
    } else {
        echo "❌ No data or failed: " . ($response['message'] ?? 'N/A') . "\n";
    }
}
