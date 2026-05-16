<?php

namespace App\Services;

use App\Models\Trade;
use App\Models\RiskEvent;
use App\Models\DailyStatistic;
use App\Models\Position;

class RiskManagementService
{
    protected $maxOpenTrades = 3;
    protected $maxLongs = 2;
    protected $maxShorts = 2;
    protected $maxDailyLossPercent = 5.0;

    protected $correlationGroups = [
        'L1' => ['BTCUSDT', 'ETHUSDT', 'SOLUSDT'],
        'MEMES' => ['DOGEUSDT', 'SHIBUSDT', 'PEPEUSDT'],
    ];

    /**
     * The Master Authority.
     * Validates if a trade is safe to execute.
     */
    public function validateTrade($symbol, $side)
    {
        // 1. Check Daily Loss
        if (!$this->checkDailyLoss()) {
            return $this->reject($symbol, 'Daily Loss Hit', 'Trading is paused. Daily loss limit of ' . $this->maxDailyLossPercent . '% exceeded.');
        }

        // 2. Check Max Open Trades
        if (!$this->checkMaxTrades($side)) {
            return $this->reject($symbol, 'Max Trades Exceeded', "Cannot open $side. Max open trades limit reached.");
        }

        // 3. Check Correlation
        if (!$this->checkCorrelation($symbol, $side)) {
            return $this->reject($symbol, 'Correlation Block', "Blocked $side on $symbol to prevent highly correlated overexposure.");
        }

        return ['approved' => true];
    }

    protected function checkDailyLoss()
    {
        $todayStat = DailyStatistic::whereDate('date', today())->first();
        if ($todayStat && $todayStat->drawdown_percentage >= $this->maxDailyLossPercent) {
            return false; // Reject
        }
        return true;
    }

    protected function checkMaxTrades($side)
    {
        $openPositions = Trade::where('status', 'OPEN')->get();

        if ($openPositions->count() >= $this->maxOpenTrades) {
            return false;
        }

        $longs = $openPositions->where('side', 'BUY')->count();
        $shorts = $openPositions->where('side', 'SELL')->count();

        if ($side === 'BUY' && $longs >= $this->maxLongs) {
            return false;
        }

        if ($side === 'SELL' && $shorts >= $this->maxShorts) {
            return false;
        }

        return true;
    }

    protected function checkCorrelation($newSymbol, $side)
    {
        $openPositions = Trade::where('status', 'OPEN')->get();

        // Find which group this new symbol belongs to
        $groupName = null;
        foreach ($this->correlationGroups as $name => $symbols) {
            if (in_array($newSymbol, $symbols)) {
                $groupName = $name;
                break;
            }
        }

        if (!$groupName) {
            return true; // Not part of a correlation group, it's safe
        }

        // Check if we already have a position open in this same group in the SAME direction
        foreach ($openPositions as $position) {
            if (in_array($position->symbol, $this->correlationGroups[$groupName])) {
                if ($position->side === $side) {
                    // Blocked! We already have a trade in this group in this direction.
                    return false;
                }
            }
        }

        return true;
    }

    protected function reject($symbol, $eventType, $description)
    {
        RiskEvent::create([
            'symbol' => $symbol,
            'event_type' => $eventType,
            'description' => $description,
        ]);

        return [
            'approved' => false,
            'reason' => $description,
            'type' => $eventType
        ];
    }
}
