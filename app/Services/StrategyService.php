<?php

namespace App\Services;

class StrategyService
{
    /**
     * Calculate Simple Moving Average (SMA)
     */
    public function calculateSMA(array $prices, int $period)
    {
        if (count($prices) < $period) return null;
        
        $slice = array_slice($prices, -$period);
        return array_sum($slice) / $period;
    }

    /**
     * Calculate Relative Strength Index (RSI)
     */
    public function calculateRSI(array $prices, int $period = 14)
    {
        if (count($prices) <= $period) return null;

        $gains = [];
        $losses = [];

        for ($i = 1; $i < count($prices); $i++) {
            $diff = $prices[$i] - $prices[$i - 1];
            if ($diff >= 0) {
                $gains[] = $diff;
                $losses[] = 0;
            } else {
                $gains[] = 0;
                $losses[] = abs($diff);
            }
        }

        $avgGain = array_sum(array_slice($gains, -$period)) / $period;
        $avgLoss = array_sum(array_slice($losses, -$period)) / $period;

        if ($avgLoss == 0) return 100;

        $rs = $avgGain / $avgLoss;
        return 100 - (100 / (1 + $rs));
    }

    /**
     * Calculate MACD (Moving Average Convergence Divergence)
     */
    public function calculateMACD(array $prices, int $fast = 12, int $slow = 26, int $signal = 9)
    {
        if (count($prices) < $slow + $signal) return null;

        // Exponential Moving Averages (Simplified for now)
        $ema12 = $this->calculateSMA(array_slice($prices, -$fast), $fast);
        $ema26 = $this->calculateSMA(array_slice($prices, -$slow), $slow);
        
        $macdLine = $ema12 - $ema26;
        $signalLine = $this->calculateSMA([$macdLine], 1); // Simplified signal line

        return [
            'macd' => $macdLine,
            'signal' => $signalLine,
            'histogram' => $macdLine - $signalLine
        ];
    }

    /**
     * Calculate Exponential Moving Average (EMA)
     */
    public function calculateEMA(array $prices, int $period)
    {
        if (count($prices) < $period) return null;

        $k = 2 / ($period + 1);
        $ema = array_sum(array_slice($prices, 0, $period)) / $period; // Start with SMA

        for ($i = $period; $i < count($prices); $i++) {
            $ema = ($prices[$i] * $k) + ($ema * (1 - $k));
        }

        return $ema;
    }

    /**
     * Check if volume is surging (> 1.5x average)
     */
    public function isVolumeSurging(array $klines)
    {
        if (count($klines) < 21) return false;

        $volumes = array_map(fn($k) => (float) $k[5], $klines);
        $currentVolume = end($volumes);

        // Exclude current volume from historical average
        $historicalVolumes = array_slice($volumes, -21, 20);
        $avgVolume = array_sum($historicalVolumes) / count($historicalVolumes);

        return $avgVolume > 0 && $currentVolume > ($avgVolume * 1.5);
    }

    /**
     * The "Brain" - Analyze market data and give a signal with 5-Layer Sieve
     */
    public function analyze($symbol, array $klines, $fundingRate = 0.0, $timeframe = '1h')
    {
        // Extract closing prices
        $closePrices = array_map(fn($k) => (float) $k[4], $klines);
        $currentPrice = end($closePrices);

        // Calculate Indicators
        $ema200 = $this->calculateEMA($closePrices, 200);
        if (!$ema200) {
            $ema200 = $this->calculateSMA($closePrices, count($closePrices));
        }

        $rsi = $this->calculateRSI($closePrices, 14);
        $macd = $this->calculateMACD($closePrices);
        $volumeSurge = $this->isVolumeSurging($klines);

        // 5-Point Scoring Sieve
        $buyScore = 0;
        $sellScore = 0;

        // Layer 1: Trend Filter (EMA 200)
        if ($ema200) {
            if ($currentPrice > $ema200) $buyScore++;
            if ($currentPrice < $ema200) $sellScore++;
        }

        // Layer 2: RSI Levels
        if ($rsi) {
            if ($rsi < 35) $buyScore++;
            if ($rsi > 65) $sellScore++;
        }

        // Layer 3: MACD Histogram Crossover
        if ($macd && isset($macd['histogram'])) {
            if ($macd['histogram'] > 0) $buyScore++;
            if ($macd['histogram'] < 0) $sellScore++;
        }

        // Layer 4: Volume Surge Confirmation
        if ($volumeSurge) {
            $buyScore++;
            $sellScore++;
        }

        // Layer 5: Funding Rate Imbalances
        if ($fundingRate < -0.001) $buyScore++; // Funding below -0.1%
        if ($fundingRate > 0.001) $sellScore++;  // Funding above +0.1%

        // Signal Decision (Needs Score >= 4)
        $signal = 'WAIT';
        $finalScore = 0;
        $reason = 'Market conditions neutral.';

        if ($buyScore >= 4) {
            $signal = 'BUY';
            $finalScore = $buyScore;
            $reason = "A+ Long Signal (Score $buyScore/5). Trend aligned, RSI oversold ($rsi), MACD positive, Volume confirmed.";
        } elseif ($sellScore >= 4) {
            $signal = 'SELL';
            $finalScore = $sellScore;
            $reason = "A+ Short Signal (Score $sellScore/5). Trend aligned, RSI overbought ($rsi), MACD negative, Volume confirmed.";
        }

        return [
            'symbol' => $symbol,
            'price' => $currentPrice,
            'signal' => $signal,
            'reason' => $reason,
            'timeframe' => $timeframe,
            'risk_score' => $finalScore * 20, // Map 1-5 score to 20-100% confidence
            'tp' => $signal === 'BUY' ? $currentPrice * 1.05 : $currentPrice * 0.95, // TP set to 5% initially (dynamic stops handled in execution)
            'sl' => $signal === 'BUY' ? $currentPrice * 0.98 : $currentPrice * 1.02, // SL set to 2% initially
            'indicators' => [
                'rsi' => $rsi,
                'ema200' => $ema200,
                'macd' => $macd,
                'volume_surge' => $volumeSurge,
                'funding_rate' => $fundingRate
            ]
        ];
    }
}
