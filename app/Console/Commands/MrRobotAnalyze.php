<?php

namespace App\Console\Commands;

use App\Services\BybitService;
use App\Services\StrategyService;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class MrRobotAnalyze extends Command
{
    protected $signature = 'mrrobot:analyze {symbol=BTCUSDT} {category=linear} {--telegram}';
    protected $description = 'Run MrRobot market analysis and generate a signal';

    protected $bybit;
    protected $strategy;
    protected $telegram;

    public function __construct(BybitService $bybit, StrategyService $strategy, TelegramService $telegram)
    {
        parent::__construct();
        $this->bybit = $bybit;
        $this->strategy = $strategy;
        $this->telegram = $telegram;
    }

    public function handle()
    {
        $symbol = $this->argument('symbol');
        $category = $this->argument('category');

        // Check if API keys are set
        if (empty(config('services.bybit.key')) || empty(config('services.bybit.secret'))) {
            $this->error("❌ API Keys are missing!");
            $this->info("Please add your BYBIT_API_KEY and BYBIT_API_SECRET to your .env file.");
            return 1;
        }

        $this->info("🤖 MrRobot is analyzing $symbol ($category)...");

        // 1. Fetch historical data (Klines)
        // We need at least 200 candles for SMA 200
        $response = $this->bybit->makeRequest('GET', '/v5/market/kline', [
            'category' => $category,
            'symbol' => $symbol,
            'interval' => '60', // 1 hour candles
            'limit' => 250
        ]);

        if (!$response['success']) {
            $this->error("Failed to fetch data: " . $response['message']);
            return 1;
        }

        $klines = $response['data']['list'] ?? [];
        
        // Bybit returns newest first, we need oldest first for analysis
        $klines = array_reverse($klines);

        // 2. Run Strategy Analysis
        $analysis = $this->strategy->analyze($symbol, $klines);

        // 3. Display Report
        $this->newLine();
        $this->line("========================================");
        $this->line("       MRROBOT SIGNAL REPORT            ");
        $this->line("========================================");
        $this->line("Asset:      " . $analysis['symbol']);
        $this->line("Price:      $" . number_format($analysis['price'], 2));
        $this->newLine();
        
        $color = $analysis['signal'] === 'BUY' ? 'info' : ($analysis['signal'] === 'SELL' ? 'error' : 'comment');
        $this->line("Signal:     <{$color}>" . $analysis['signal'] . "</{$color}>");
        $this->line("Reason:     " . $analysis['reason']);
        $this->newLine();

        $this->line("Indicators:");
        $this->line("- RSI:      " . ($analysis['indicators']['rsi'] ? number_format($analysis['indicators']['rsi'], 2) : 'N/A'));
        $this->line("- SMA 50:   $" . ($analysis['indicators']['sma50'] ? number_format($analysis['indicators']['sma50'], 2) : 'N/A'));
        $this->line("- SMA 200:  $" . ($analysis['indicators']['sma200'] ? number_format($analysis['indicators']['sma200'], 2) : 'N/A'));
        $this->line("========================================");
        $this->newLine();

        // 4. Send to Telegram if requested
        if ($this->option('telegram')) {
            $this->info("Sending signal to Telegram...");
            if ($this->telegram->sendSignalReport($analysis)) {
                $this->info("✅ Signal sent successfully!");
            } else {
                $this->error("❌ Failed to send Telegram signal. Check your Chat ID.");
            }
        }

        if ($analysis['signal'] !== 'WAIT') {
            $this->info("TIP: You can now use this signal to place a trade manually or automate it!");
        }

        return 0;
    }
}
