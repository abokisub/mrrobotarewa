<?php

namespace App\Services;

class ExecutionService
{
    protected $bybit;

    public function __construct(BybitService $bybit)
    {
        $this->bybit = $bybit;
    }

    /**
     * Execute a trade based on a signal
     */
    public function executeSignal($analysis)
    {
        $symbol = $analysis['symbol'];
        $signal = $analysis['signal'];
        $price = $analysis['price'];
        $category = 'linear'; // Switching to Futures for small balances

        // 1. Set Leverage (Beginner Standard: 3x Futures Leverage)
        $leverage = 3;
        $this->bybit->setLeverage($symbol, $leverage);

        // 2. Get Balance
        $balanceData = $this->bybit->getWalletBalance('UNIFIED');
        if (!$balanceData['success']) return ['success' => false, 'message' => 'Could not fetch balance'];

        $usdtBalance = 0;
        foreach ($balanceData['data']['list'][0]['coin'] as $coin) {
            if ($coin['coin'] === 'USDT') {
                $usdtBalance = (float) $coin['walletBalance'];
                break;
            }
        }

        if ($usdtBalance < 1.0) {
            return ['success' => false, 'message' => 'Balance too low even for Futures ($1 min)'];
        }

        // 3. Calculate dynamic Swing Low/High Stop Loss (lowest/highest in last 10 candles)
        $slPrice = 0.0;
        $tpPrice = 0.0;
        $klineResponse = $this->bybit->makeRequest('GET', '/v5/market/kline', [
            'category' => 'linear',
            'symbol' => $symbol,
            'interval' => '60',
            'limit' => 15
        ]);

        $klines = array_reverse($klineResponse['data']['list'] ?? []);

        if (count($klines) >= 10) {
            $last10 = array_slice($klines, -10);
            if ($signal === 'BUY') {
                $lows = array_map(fn($k) => (float) $k[3], $last10); // Low wick is index 3
                $swingLow = min($lows);
                $slPrice = $swingLow * 0.997; // SL = Swing Low - 0.3% Buffer
                
                $riskPerCoin = $price - $slPrice;
                if ($riskPerCoin <= 0) $riskPerCoin = $price * 0.02; // Fallback
                $tpPrice = $price + ($riskPerCoin * 2); // 1:2 R:R Target
            } else {
                $highs = array_map(fn($k) => (float) $k[2], $last10); // High wick is index 2
                $swingHigh = max($highs);
                $slPrice = $swingHigh * 1.003; // SL = Swing High + 0.3% Buffer
                
                $riskPerCoin = $slPrice - $price;
                if ($riskPerCoin <= 0) $riskPerCoin = $price * 0.02; // Fallback
                $tpPrice = $price - ($riskPerCoin * 2); // 1:2 R:R Target
            }
        } else {
            // Fallback to fixed 2% SL & 4% TP if wicks fail to load
            $slPercent = 0.02;
            $tpPercent = 0.04;
            $tpPrice = $signal === 'BUY' ? $price * (1 + $tpPercent) : $price * (1 - $tpPercent);
            $slPrice = $signal === 'BUY' ? $price * (1 - $slPercent) : $price * (1 + $slPercent);
        }

        // 4. Calculate Quantity based on exact 2% Risk Rule
        $riskAmount = $usdtBalance * 0.02;
        $riskPerCoin = abs($price - $slPrice);
        $qty = $riskPerCoin > 0 ? ($riskAmount / $riskPerCoin) : 0;

        // Safety cap: Never exceed 3x max leverage position size
        $maxQty = ($usdtBalance * $leverage) / $price;
        if ($qty > $maxQty) $qty = $maxQty;

        // SAFETY BOLT: Min Qty safeguards for linear perpetuals
        if ($symbol === 'BTCUSDT' && $qty < 0.001) $qty = 0.001;
        if ($symbol === 'ETHUSDT' && $qty < 0.01) $qty = 0.01;
        
        $qty = round($qty, 3);
        if ($qty <= 0) return ['success' => false, 'message' => 'Quantity calculated is zero'];

        $orderParams = [
            'takeProfit' => (string) round($tpPrice, 4),
            'stopLoss' => (string) round($slPrice, 4),
            'tpTriggerBy' => 'LastPrice',
            'slTriggerBy' => 'LastPrice',
            'tpslMode' => 'Full',
        ];

        // 5. Enforce Dry Run (Paper Trading) Mode if active in .env
        $dryRun = env('MRROBOT_DRY_RUN', true);

        if ($dryRun) {
            return [
                'success' => true,
                'message' => 'Dry run execution successful (Paper Trade).',
                'data' => [
                    'orderId' => 'mock_dry_' . uniqid(),
                    'qty' => $qty,
                    'tp' => $tpPrice,
                    'sl' => $slPrice
                ]
            ];
        }

        $side = $signal === 'BUY' ? 'Buy' : 'Sell';

        $response = $this->bybit->placeOrder(
            $category,
            $symbol,
            $side,
            'Market',
            $qty,
            null,
            $orderParams
        );

        if ($response['success']) {
            $response['data'] = array_merge($response['data'] ?? [], [
                'qty' => $qty,
                'tp' => $tpPrice,
                'sl' => $slPrice
            ]);
        }

        return $response;
    }
}
