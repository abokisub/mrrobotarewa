<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $token;
    protected $chatId;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
        $this->chatId = env('TELEGRAM_CHAT_ID');
    }

    /**
     * Send a message to Telegram
     */
    public function sendMessage($message, $parseMode = 'HTML')
    {
        if (!$this->token || !$this->chatId) {
            Log::warning("Telegram credentials missing.");
            return false;
        }

        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

        try {
            $response = Http::post($url, [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => $parseMode,
                'disable_web_page_preview' => true,
            ]);

            if ($response->failed()) {
                Log::error("Telegram API Error: " . $response->body());
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Telegram Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Bot Online notification
     */
    public function sendBotOnline($balance, $watchlist = 'ALL USDT Futures')
    {
        $message = "🟢 <b>MRROBOT ONLINE</b>\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "💰 <b>Account Balance:</b> $" . number_format($balance, 2) . "\n";
        $message .= "📊 <b>Scanning:</b> " . $watchlist . "\n";
        $message .= "⚙️ <b>Mode:</b> Production Hunter Mode\n";
        $message .= "🕐 <b>Time:</b> " . now()->timezone('UTC')->format('H:i') . " UTC\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "Ready to hunt. Waiting for A+ setups only.";

        return $this->sendMessage($message);
    }

    /**
     * Send Bot Offline notification
     */
    public function sendBotOffline($summary)
    {
        $message = "⚫ <b>MRROBOT OFFLINE</b>\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "📊 <b>SESSION SUMMARY:</b>\n";
        $message .= "  Total Trades: " . ($summary['total'] ?? 0) . "\n";
        $message .= "  Wins: " . ($summary['wins'] ?? 0) . " | Losses: " . ($summary['losses'] ?? 0) . "\n";
        $message .= "  Win Rate: " . ($summary['winRate'] ?? 0) . "%\n";
        $message .= "  Session P&L: $" . number_format($summary['pnl'] ?? 0.0, 2) . "\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "All positions closed. See you next session.";

        return $this->sendMessage($message);
    }

    /**
     * Send a Trading Signal Report (Production Signal Style)
     */
    public function sendSignalReport($analysis, $isExecuted = false)
    {
        $direction = $analysis['signal'] === 'BUY' ? 'LONG' : 'SHORT';
        $emojiDirection = $analysis['signal'] === 'BUY' ? '📈' : '📉';
        
        $message = "🚀 <b>TRADE ENTERED</b>\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "📌 <b>Pair:</b> " . $analysis['symbol'] . "\n";
        $message .= "{$emojiDirection} <b>Direction:</b> " . $direction . "\n";
        $message .= "💵 <b>Entry Price:</b> $" . number_format($analysis['price'], 4) . "\n";
        $message .= "🎯 <b>Take Profit:</b> $" . number_format($analysis['tp'], 4) . " (+4.0%)\n";
        $message .= "🛑 <b>Stop Loss:</b> $" . number_format($analysis['sl'], 4) . " (-2.0%)\n";
        $message .= "⚖️ <b>Leverage:</b> 3x\n\n";
        
        $message .= "📊 <b>WHY THIS TRADE:</b>\n";
        $message .= "  ✅ " . $analysis['reason'] . "\n";
        $message .= "  ✅ RSI: " . number_format($analysis['indicators']['rsi'] ?? 0, 1) . "\n";
        $message .= "  ✅ MACD: " . number_format($analysis['indicators']['macd']['histogram'] ?? 0, 4) . "\n";
        $message .= "  ✅ Volume: Surge Confirmed\n";
        $message .= "  ✅ Funding Rate: " . number_format(($analysis['indicators']['funding_rate'] ?? 0.0) * 100, 3) . "%\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "Risk/Reward: 1:2 ✅";

        return $this->sendMessage($message);
    }

    /**
     * Send a Market Scan Summary (Muted in Production but kept for backwards compatibility)
     */
    public function sendMarketSummary($scanResults)
    {
        return true; // Silent scanning
    }

    /**
     * Send a Risk Block Alert
     */
    public function sendRiskAlert($symbol, $reason, $type)
    {
        $message = "⚠️ <b>RISK ENGINE BLOCKED TRADE</b>\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "<b>Blocked Pair:</b> " . $symbol . "\n";
        $message .= "<b>Reason:</b> " . $reason . "\n";
        $message .= "<b>Type:</b> " . $type . "\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "🧠 <i>Capital Preservation Shield held.</i>";

        return $this->sendMessage($message);
    }
}
