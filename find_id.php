<?php

require __DIR__.'/vendor/autoload.php';

$token = "8805984556:AAFoAHG16cFfY2zwF1vs6Vjrq-XMvJ4DWvE";
$url = "https://api.telegram.org/bot{$token}/getUpdates";

echo "Scanning for Group ID...\n";
$response = file_get_contents($url);
$data = json_decode($response, true);

if (empty($data['result'])) {
    echo "No recent messages found. Please make sure you added the bot to the group and sent a message there.\n";
} else {
    foreach ($data['result'] as $update) {
        if (isset($update['message']['chat'])) {
            $chat = $update['message']['chat'];
            echo "----------------------------------\n";
            echo "Chat Title: " . ($chat['title'] ?? 'Private Chat') . "\n";
            echo "Chat ID:    " . $chat['id'] . "\n";
            echo "Type:       " . $chat['type'] . "\n";
        }
    }
}
