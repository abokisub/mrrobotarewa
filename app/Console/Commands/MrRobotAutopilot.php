<?php

namespace App\Console\Commands;

use App\Services\BybitService;
use App\Services\StrategyService;
use App\Services\ExecutionService;
use App\Services\TelegramService;
use App\Services\RiskManagementService;
use App\Models\Signal;
use App\Models\Trade;
use App\Models\BotLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MrRobotAutopilot extends Command
{
    protected $signature = 'mrrobot:autopilot';
    protected $description = 'Start the MrRobot 24/7 Automated Trading Heartbeat with Dynamic Market Scanning';

    protected $bybit;
    protected $strategy;
    protected $execution;
    protected $telegram;
    protected $risk;

    public function __construct(
        BybitService $bybit, 
        StrategyService $strategy, 
        ExecutionService $execution,
        TelegramService $telegram,
        RiskManagementService $risk
    ) {
        parent::__construct();
        $this->bybit = $bybit;
        $this->strategy = $strategy;
        $this->execution = $execution;
        $this->telegram = $telegram;
        $this->risk = $risk;
    }

    public function handle()
    {
        $this->info("🚀 MrRobot Autopilot Heartbeat Started: " . now()->toDateTimeString());

        // 1. Check Safety Switch (Daily Loss)
        if ($this->isSafetySwitchTriggered()) {
            $this->error("🛑 SAFETY SWITCH ACTIVE: Daily loss limit reached. Trading paused.");
            return;
        }

        // 2. Fetch All Active USDT Perpetuals from Bybit
        $this->line("Fetching active USDT Perpetual markets...");
        $tickerResponse = $this->bybit->getTickers('linear');

        if (!$tickerResponse['success']) {
            $this->error("Failed to fetch market tickers. Aborting heartbeat.");
            return;
        }

        $tickers = $tickerResponse['data']['list'] ?? [];

        // Filter: Only USDT pairs, and Daily Turnover > $10,000,000 (USDT)
        $filteredTickers = array_filter($tickers, function ($t) {
            return str_ends_with($t['symbol'], 'USDT') && (float) ($t['turnover24h'] ?? 0) >= 10000000;
        });

        // Sort by turnover24h DESC to rank by most active markets first
        usort($filteredTickers, fn($a, $b) => (float) $b['turnover24h'] <=> (float) $a['turnover24h']);

        // Limit to top 15 most liquid assets to remain safely within Bybit V5 API rate limits
        $filteredTickers = array_slice($filteredTickers, 0, 15);

        $this->info("Found " . count($filteredTickers) . " active liquid markets ranked by 24h volume.");

        $qualifiedSignals = [];

        // 3. Scan & Score remaining candidates
        foreach ($filteredTickers as $ticker) {
            $symbol = $ticker['symbol'];
            $fundingRate = (float) ($ticker['fundingRate'] ?? 0.0);

            // Fetch historical candle data (1 hour interval)
            $response = $this->bybit->makeRequest('GET', '/v5/market/kline', [
                'category' => 'linear',
                'symbol' => $symbol,
                'interval' => '60',
                'limit' => 250 // Grab enough history for EMA 200
            ]);

            if (!$response['success']) {
                continue;
            }

            $klines = array_reverse($response['data']['list'] ?? []);

            // Analyze with 5-Layer Stack
            $analysis = $this->strategy->analyze($symbol, $klines, $fundingRate);

            // Save Telemetry Signal to Database (Only if it's BUY/SELL to avoid database bloat)
            if ($analysis['signal'] !== 'WAIT') {
                Signal::create([
                    'symbol' => $symbol,
                    'signal_type' => $analysis['signal'],
                    'rsi_value' => $analysis['indicators']['rsi'] ?? null,
                    'macd_value' => $analysis['indicators']['macd']['histogram'] ?? null,
                    'confidence_score' => $analysis['risk_score'] ?? 0,
                    'market_condition' => $analysis['reason'] ?? 'Unknown',
                ]);

                $qualifiedSignals[] = $analysis;
            }
        }

        if (empty($qualifiedSignals)) {
            $this->info("💤 Heartbeat cycle complete. No A+ setups found (Score >= 4).");
            return;
        }

        // 4. Rank candidates by Signal Score (Confidence Score) Descending
        usort($qualifiedSignals, fn($a, $b) => $b['risk_score'] <=> $a['risk_score']);

        $this->info("Ranked " . count($qualifiedSignals) . " A+ opportunities. Executing top setups...");

        // Take the top 2 opportunities
        $topOpportunities = array_slice($qualifiedSignals, 0, 2);

        foreach ($topOpportunities as $analysis) {
            $symbol = $analysis['symbol'];
            $this->info("🎯 Target Selected: $symbol (Score " . ($analysis['risk_score'] / 20) . "/5)");

            // 5. Risk Engine Validation
            $riskCheck = $this->risk->validateTrade($symbol, $analysis['signal']);

            if (!$riskCheck['approved']) {
                $this->warn("🛑 TRADE BLOCKED BY RISK ENGINE: " . $riskCheck['reason']);
                $this->telegram->sendRiskAlert($symbol, $riskCheck['reason'], $riskCheck['type']);
                continue;
            }

            $this->info("🟢 RISK CLEARED: Executing trade on $symbol...");

            $result = $this->execution->executeSignal($analysis);

            if ($result['success']) {
                $this->info("✅ Trade executed successfully!");
                
                // Map computed swing parameters back for the alert and DB ledger
                $analysis['tp'] = $result['data']['tp'] ?? $analysis['tp'];
                $analysis['sl'] = $result['data']['sl'] ?? $analysis['sl'];
                $analysis['qty'] = $result['data']['qty'] ?? 0.0;

                $this->telegram->sendSignalReport($analysis, true);
                $this->logTrade($symbol, $analysis['signal'], $analysis['price'], $analysis, $result['data']['orderId'] ?? null);
            } else {
                $this->error("❌ Execution failed: " . $result['message']);
                BotLog::create([
                    'level' => 'error',
                    'action' => 'Trade Execution Failed',
                    'message' => "Failed to execute $symbol: " . $result['message'],
                    'context' => json_encode($analysis)
                ]);
            }
        }

        $this->info("💤 Heartbeat cycle complete.");
    }

    protected function isSafetySwitchTriggered()
    {
        $today = now()->format('Y-m-d');
        $logFile = "trading_logs/daily_stats_{$today}.json";
        
        if (!Storage::exists($logFile)) return false;

        $stats = json_decode(Storage::get($logFile), true);
        $lossLimit = 3.0; // 3% Max Daily Loss

        return ($stats['total_loss_percent'] ?? 0) >= $lossLimit;
    }

    protected function logTrade($symbol, $side, $price, $analysis = null, $orderId = null)
    {
        $dryRun = env('MRROBOT_DRY_RUN', true);

        // 1. Save Trade to DB
        Trade::create([
            'symbol' => $symbol,
            'side' => $side,
            'leverage' => 3, // Beginner standard
            'entry_price' => $price,
            'take_profit' => $analysis['tp'] ?? null,
            'stop_loss' => $analysis['sl'] ?? null,
            'quantity' => $analysis['qty'] ?? 0.0,
            'status' => $dryRun ? 'DRY_RUN' : 'OPEN',
            'strategy_used' => '5-Layer Hunter Stack',
            'exchange_order_id' => $orderId,
            'tags' => json_encode([
                'strategy' => 'RSI_MACD_VOLUME_FUNDING',
                'market_condition' => $analysis['reason'] ?? 'Unknown',
                'signal_strength' => ($analysis['risk_score'] ?? 0) >= 80 ? 'HIGH' : 'MEDIUM',
                'session' => now()->timezone('UTC')->format('H') . 'H UTC',
                'dry_run' => $dryRun
            ])
        ]);

        // 2. Save Bot Log
        BotLog::create([
            'level' => 'info',
            'action' => $dryRun ? 'Dry Run Trade Logged' : 'Trade Execution',
            'message' => "EXECUTED $side on $symbol at $$price" . ($dryRun ? " [DRY RUN]" : ""),
            'context' => json_encode(['analysis' => $analysis])
        ]);
        
        $log = "[" . now()->toDateTimeString() . "] EXECUTED $side on $symbol at $$price" . ($dryRun ? " [DRY RUN]" : "") . "\n";
        Storage::append('trading_logs/history.log', $log);
    }
}
