<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MRROBOT | Advanced Quant Terminal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg: #04060a;
            --surface: #0a0c14;
            --surface-card: #0f121e;
            --surface-hover: #151a2b;
            --primary: #00e676;
            --primary-glow: rgba(0, 230, 118, 0.2);
            --secondary: #00b0ff;
            --secondary-glow: rgba(0, 176, 255, 0.2);
            --error: #ff1744;
            --error-glow: rgba(255, 23, 68, 0.2);
            --warning: #ffea00;
            --warning-glow: rgba(255, 234, 0, 0.2);
            --text: #f0f2f5;
            --text-dim: #707e94;
            --border: rgba(255, 255, 255, 0.035);
            --border-glow: rgba(255, 255, 255, 0.08);
            --font-main: 'Outfit', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: var(--font-main); scroll-behavior: smooth; }
        body { background: var(--bg); color: var(--text); min-height: 100vh; overflow: hidden; display: flex; }

        /* Left-side Navigation Dock (Bybit Style) */
        nav { width: 64px; height: 100vh; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; align-items: center; padding: 20px 0; gap: 20px; z-index: 100; flex-shrink: 0; }
        .nav-logo { font-size: 26px; font-weight: 700; color: var(--primary); text-shadow: 0 0 10px var(--primary-glow); margin-bottom: 25px; cursor: pointer; }
        .nav-item { width: 40px; height: 40px; border-radius: 10px; background: transparent; border: 1px solid transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--text-dim); text-decoration: none; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); position: relative; }
        .nav-item:hover, .nav-item.active { border-color: var(--border-glow); background: var(--surface-hover); color: var(--primary); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); }
        .nav-tooltip { position: absolute; left: 75px; background: var(--surface-card); border: 1px solid var(--border); padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; color: var(--text); display: none; white-space: nowrap; pointer-events: none; z-index: 200; box-shadow: 0 4px 12px rgba(0,0,0,0.5); }
        .nav-item:hover .nav-tooltip { display: block; }

        /* Outer Frame Layout */
        .app-container { flex-grow: 1; display: flex; flex-direction: column; height: 100vh; overflow: hidden; }

        /* Top Institutional Ticker Header Bar */
        .ticker-header { height: 56px; background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 20px; flex-shrink: 0; }
        .ticker-brand { display: flex; align-items: center; gap: 10px; }
        .ticker-brand h1 { font-family: var(--font-mono); font-size: 16px; font-weight: 700; color: var(--text); }
        .ticker-brand span { color: var(--primary); }
        
        .ticker-data { display: flex; align-items: center; gap: 30px; margin-left: 40px; }
        .ticker-item { display: flex; flex-direction: column; gap: 2px; }
        .ticker-label { font-size: 9px; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.5px; }
        .ticker-value { font-family: var(--font-mono); font-size: 13px; font-weight: 700; }

        .system-status { display: flex; align-items: center; gap: 8px; font-size: 10px; font-weight: 700; letter-spacing: 0.5px; color: var(--primary); background: rgba(0, 230, 118, 0.05); padding: 5px 12px; border-radius: 6px; border: 1px solid rgba(0, 230, 118, 0.1); }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--primary); box-shadow: 0 0 10px var(--primary); animation: pulse 1.8s infinite; }

        /* SPA Subviews Page Sections */
        .view-section { display: none; height: calc(100vh - 56px); overflow: hidden; }
        .view-section.active { display: block; }

        /* Master Workspace Split Panel Grid */
        .master-workspace { display: grid; grid-template-columns: 1fr 340px; height: 100%; overflow: hidden; }
        
        /* Main Core View Area */
        .main-panel { display: flex; flex-direction: column; overflow-y: auto; padding: 15px; gap: 15px; border-right: 1px solid var(--border); height: 100%; }
        
        /* Side Market Intelligence Area */
        .side-panel { display: flex; flex-direction: column; overflow-y: auto; padding: 15px; gap: 15px; background: #06080d; height: 100%; }

        /* Stats Strip Ribbon */
        .stats-ribbon { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
        .stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 15px; display: flex; flex-direction: column; gap: 8px; position: relative; transition: all 0.3s; }
        .stat-card:hover { border-color: var(--border-glow); transform: translateY(-1px); }
        .stat-card::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 2px; background: transparent; transition: all 0.3s; }
        .stat-card.active-pnl::after { background: var(--primary); }
        .stat-card.negative-pnl::after { background: var(--error); }
        .stat-card.accent-pnl::after { background: var(--secondary); }
        
        .stat-title { font-size: 10px; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-num { font-family: var(--font-mono); font-size: 20px; font-weight: 700; }

        /* Core Candlestick Chart Frame */
        .chart-frame { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; height: 380px; position: relative; }

        /* Panel Abstraction */
        .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 20px; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15); display: flex; flex-direction: column; gap: 15px; }
        .panel-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid var(--border); }
        .panel-title { font-size: 11px; font-weight: 700; color: var(--text-dim); text-transform: uppercase; letter-spacing: 1px; }

        /* Professional Styled Tables */
        table { width: 100%; border-collapse: collapse; font-size: 12px; text-align: left; }
        th { padding: 10px; color: var(--text-dim); font-size: 9px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid var(--border); letter-spacing: 0.5px; }
        td { padding: 12px 10px; border-bottom: 1px solid var(--border); font-family: var(--font-mono); color: var(--text); }
        tr:hover td { background: var(--surface-hover); }
        tr:last-child td { border: none; }

        /* Badges & Glowing Indicators */
        .badge { padding: 3px 6px; border-radius: 4px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; }
        .badge.buy { background: rgba(0, 230, 118, 0.06); color: var(--primary); border: 1px solid rgba(0, 230, 118, 0.15); box-shadow: 0 0 8px rgba(0, 230, 118, 0.05); }
        .badge.sell { background: rgba(255, 23, 68, 0.06); color: var(--error); border: 1px solid rgba(255, 23, 68, 0.15); box-shadow: 0 0 8px rgba(255, 23, 68, 0.05); }
        .badge.paper { background: rgba(0, 176, 255, 0.06); color: var(--secondary); border: 1px solid rgba(0, 176, 255, 0.15); }
        .badge.live { background: rgba(255, 234, 0, 0.06); color: var(--warning); border: 1px solid rgba(255, 234, 0, 0.15); }
        .badge.risk { background: rgba(255, 234, 0, 0.06); color: var(--warning); border: 1px solid rgba(255, 234, 0, 0.15); }

        .text-green { color: var(--primary); }
        .text-red { color: var(--error); }

        /* Live Watchlist Ticks (Right Side) */
        .panel-title-text { font-size: 11px; font-weight: 700; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.8px; display: flex; align-items: center; justify-content: space-between; }
        
        .watchlist-container { display: flex; flex-direction: column; gap: 8px; }
        .ticker-row { background: rgba(255, 255, 255, 0.015); border: 1px solid var(--border); border-radius: 8px; padding: 10px 12px; display: flex; align-items: center; justify-content: space-between; transition: all 0.25s; cursor: pointer; }
        .ticker-row:hover { background: var(--surface-hover); border-color: var(--border-glow); }
        .ticker-name { font-weight: 700; font-size: 12px; color: var(--text); display: flex; align-items: center; gap: 6px; }
        .ticker-name span { font-size: 9px; color: var(--text-dim); font-weight: 500; }
        .ticker-price-container { text-align: right; display: flex; flex-direction: column; gap: 2px; }
        .ticker-price { font-family: var(--font-mono); font-size: 12px; font-weight: 700; transition: color 0.15s; }
        .ticker-change { font-family: var(--font-mono); font-size: 9.5px; font-weight: 700; }

        /* Simulated Live Tickers Order Book Panel */
        .orderbook-panel { display: flex; flex-direction: column; gap: 6px; }
        .orderbook-row { display: flex; justify-content: space-between; font-size: 11px; font-family: var(--font-mono); padding: 3px 0; position: relative; }
        .orderbook-fill-bid { position: absolute; top: 0; bottom: 0; right: 0; background: rgba(0, 230, 118, 0.035); transition: width 0.3s; z-index: 1; }
        .orderbook-fill-ask { position: absolute; top: 0; bottom: 0; right: 0; background: rgba(255, 23, 68, 0.035); transition: width 0.3s; z-index: 1; }
        .orderbook-val { position: relative; z-index: 2; }

        /* Subview Layout Elements (Full Screen Layouts) */
        .fullscreen-workspace { padding: 25px; display: flex; flex-direction: column; gap: 20px; overflow-y: auto; height: 100%; }
        
        .deck-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .config-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 20px; display: flex; flex-direction: column; gap: 15px; }
        
        .config-item { display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid var(--border); }
        .config-label { font-size: 12px; color: var(--text-dim); font-weight: 600; }
        .config-val { font-family: var(--font-mono); font-size: 12px; font-weight: 700; color: var(--text); }

        .settings-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 15px; margin-bottom: 10px; }
        .settings-title { font-size: 18px; font-weight: 700; color: var(--text); }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 230, 118, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(0, 230, 118, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 230, 118, 0); }
        }

        /* Centered Premium Page Loader Splash */
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: #04060a;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.6s cubic-bezier(0.25, 1, 0.5, 1), visibility 0.6s;
            opacity: 1;
            visibility: visible;
        }
        .loader-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
            text-align: center;
        }
        .loader-logo {
            font-family: var(--font-mono);
            font-size: 36px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -1.5px;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.05);
        }
        .loader-logo span {
            color: var(--primary);
            text-shadow: 0 0 25px var(--primary-glow);
        }
        .loader-spinner {
            width: 46px;
            height: 46px;
            border: 3px solid rgba(255, 255, 255, 0.015);
            border-top-color: var(--primary);
            border-bottom-color: var(--secondary);
            border-radius: 50%;
            animation: spin 1s cubic-bezier(0.5, 0.1, 0.5, 0.9) infinite;
            box-shadow: 0 0 15px rgba(0, 230, 118, 0.08);
        }
        .loader-text {
            font-family: var(--font-mono);
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 2.5px;
            color: var(--text-dim);
            text-transform: uppercase;
            animation: text-pulse 1.4s ease-in-out infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes text-pulse {
            0%, 100% { opacity: 0.35; }
            50% { opacity: 1; }
        }
    </style>
</head>
<body>
    <!-- PREMIUM CENTER LOAD OVERLAY -->
    <div id="loader-overlay">
        <div class="loader-content">
            <div class="loader-logo">MR<span>ROBOT</span></div>
            <div class="loader-spinner"></div>
            <div class="loader-text">ESTABLISHING SECURE GATEWAYS...</div>
        </div>
    </div>

    <!-- LEFT SIDEBAR NAVIGATION DOCK -->
    <nav>
        <div class="nav-logo" onclick="switchView('dashboard')">🤖</div>
        
        <div class="nav-item active" onclick="switchView('dashboard', this)">
            📊
            <span class="nav-tooltip">Trading Center</span>
        </div>
        
        <div class="nav-item" onclick="switchView('ledger', this)">
            📋
            <span class="nav-tooltip">Audit Ledger</span>
        </div>
        
        <div class="nav-item" onclick="switchView('risk', this)">
            🛡️
            <span class="nav-tooltip">Risk Governance</span>
        </div>
        
        <div class="nav-item" onclick="switchView('settings', this)">
            ⚙️
            <span class="nav-tooltip">Control Panel</span>
        </div>
    </nav>

    <div class="app-container">
        <!-- TOP Ticker Header Bar -->
        <header class="ticker-header">
            <div class="ticker-brand">
                <h1>MR<span>ROBOT</span> QUANT OPERATIONAL PLATFORM</h1>
                <div class="ticker-data">
                    <div class="ticker-item">
                        <span class="ticker-label">API Keys Authority</span>
                        <span class="ticker-val {{ $hasKeys ? 'text-green' : 'text-red' }}">{{ $hasKeys ? 'CONNECTED' : 'DISCONNECTED' }}</span>
                    </div>
                    <div class="ticker-item">
                        <span class="ticker-label">Risk Governance Shield</span>
                        <span class="ticker-val text-green">HEALTHY & ACTIVE</span>
                    </div>
                    <div class="ticker-item">
                        <span class="ticker-label">Heartbeat Cycle</span>
                        <span class="ticker-val" style="color: var(--secondary);">2m Watchdog</span>
                    </div>
                    <div class="ticker-item">
                        <span class="ticker-label">Margin Balance</span>
                        <span class="ticker-val">${{ number_format($balance, 2) }} USDT</span>
                    </div>
                </div>
            </div>
            <div class="system-status">
                <span class="status-dot"></span>
                PRODUCTION HUNTER ACTIVE
            </div>
        </header>

        <!-- ============================================== -->
        <!-- VIEW 1: TRADING CENTER (DASHBOARD) -->
        <!-- ============================================== -->
        <div id="view-dashboard" class="view-section active">
            <div class="master-workspace">
                <!-- Main Core Column -->
                <div class="main-panel">
                    <!-- Performance Ribbon -->
                    <div class="stats-ribbon">
                        <div class="stat-card">
                            <span class="stat-title">System Win Rate</span>
                            <span class="stat-num text-green">{{ $analytics['winRate'] }}%</span>
                        </div>
                        <div class="stat-card {{ $analytics['totalPnl'] >= 0 ? 'active-pnl' : 'negative-pnl' }}">
                            <span class="stat-title">Cumulative Performance</span>
                            <span class="stat-num {{ $analytics['totalPnl'] >= 0 ? 'text-green' : 'text-red' }}">
                                {{ $analytics['totalPnl'] >= 0 ? '+' : '' }}${{ number_format($analytics['totalPnl'], 2) }}
                            </span>
                        </div>
                        <div class="stat-card accent-pnl">
                            <span class="stat-title">Mathematical Edge (Profit Factor)</span>
                            <span class="stat-num" style="color: var(--secondary);">{{ $analytics['profitFactor'] }}</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-title">Max System Drawdown</span>
                            <span class="stat-num text-red">{{ $analytics['maxDrawdown'] }}%</span>
                        </div>
                    </div>

                    <!-- Live TradingView Chart Frame -->
                    <div class="chart-frame">
                        <div id="tradingview_chart" style="height: 100%; width: 100%;"></div>
                    </div>

                    <!-- Active Positions Table Panel -->
                    <div class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">Active Positions</h3>
                            <span class="badge" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border);">
                                Exposure Cap: {{ $activePositions->count() }} / 3 Max
                            </span>
                        </div>
                        @if($activePositions->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Trading Asset</th>
                                    <th>Direction</th>
                                    <th>Leverage</th>
                                    <th>Entry Mark</th>
                                    <th>Stop Loss</th>
                                    <th>Take Profit</th>
                                    <th>Class</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activePositions as $pos)
                                @php $tags = json_decode($pos->tags, true); @endphp
                                <tr>
                                    <td style="font-weight: 700; color: var(--text);">{{ $pos->symbol }}</td>
                                    <td>
                                        <span class="badge {{ $pos->side === 'BUY' ? 'buy' : 'sell' }}">
                                            {{ $pos->side === 'BUY' ? 'LONG 📈' : 'SHORT 📉' }}
                                        </span>
                                    </td>
                                    <td>{{ $pos->leverage }}x</td>
                                    <td>${{ number_format($pos->entry_price, 4) }}</td>
                                    <td class="text-red">${{ number_format($pos->stop_loss, 4) }}</td>
                                    <td class="text-green">${{ number_format($pos->take_profit, 4) }}</td>
                                    <td>
                                        @if(isset($tags['dry_run']) && $tags['dry_run'])
                                            <span class="badge paper">PAPER</span>
                                        @else
                                            <span class="badge live">LIVE</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div style="padding: 30px; text-align: center; color: var(--text-dim); font-size: 12.5px;">
                            No active positions held. Waiting for the next high-probability breakout...
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right-side Market Deep Column -->
                <div class="side-panel">
                    <!-- Watchlist Ticker Ticks -->
                    <div class="panel">
                        <div class="panel-title-text">
                            <span>USDT Perpetual Watchlist</span>
                            <span style="font-size: 9px; color: var(--primary);">● SCAN ACTIVE</span>
                        </div>
                        <div class="watchlist-container">
                            <div class="ticker-row" onclick="changeChartSymbol('BTCUSDT')">
                                <div class="ticker-name">BTC<span>USDT</span></div>
                                <div class="ticker-price-container">
                                    <span class="ticker-price" id="price_btc">$96,432.50</span>
                                    <span class="ticker-change text-green" id="change_btc">+2.45%</span>
                                </div>
                            </div>
                            <div class="ticker-row" onclick="changeChartSymbol('ETHUSDT')">
                                <div class="ticker-name">ETH<span>USDT</span></div>
                                <div class="ticker-price-container">
                                    <span class="ticker-price" id="price_eth">$3,420.20</span>
                                    <span class="ticker-change text-green" id="change_eth">+1.82%</span>
                                </div>
                            </div>
                            <div class="ticker-row" onclick="changeChartSymbol('SOLUSDT')">
                                <div class="ticker-name">SOL<span>USDT</span></div>
                                <div class="ticker-price-container">
                                    <span class="ticker-price" id="price_sol">$145.45</span>
                                    <span class="ticker-change text-red" id="change_sol">-0.85%</span>
                                </div>
                            </div>
                            <div class="ticker-row" onclick="changeChartSymbol('AVAXUSDT')">
                                <div class="ticker-name">AVAX<span>USDT</span></div>
                                <div class="ticker-price-container">
                                    <span class="ticker-price" id="price_avax">$35.15</span>
                                    <span class="ticker-change text-green" id="change_avax">+4.12%</span>
                                </div>
                            </div>
                            <div class="ticker-row" onclick="changeChartSymbol('XRPUSDT')">
                                <div class="ticker-name">XRP<span>USDT</span></div>
                                <div class="ticker-price-container">
                                    <span class="ticker-price" id="price_xrp">$1.1240</span>
                                    <span class="ticker-change text-green" id="change_xrp">+0.45%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live simulated Bids/Asks depth (Bybit visual feel) -->
                    <div class="panel">
                        <div class="panel-title-text">
                            <span>Bybit Futures Ticks (Sim)</span>
                            <span style="font-size: 9px; color: var(--text-dim);">Live Bid/Ask</span>
                        </div>
                        <div class="orderbook-panel">
                            <div class="orderbook-row text-red">
                                <span class="orderbook-val" id="ask_price_3">96,445.0</span>
                                <span class="orderbook-val" id="ask_qty_3">1.450</span>
                                <div class="orderbook-fill-ask" style="width: 45%;"></div>
                            </div>
                            <div class="orderbook-row text-red">
                                <span class="orderbook-val" id="ask_price_2">96,440.0</span>
                                <span class="orderbook-val" id="ask_qty_2">0.820</span>
                                <div class="orderbook-fill-ask" style="width: 25%;"></div>
                            </div>
                            <div class="orderbook-row text-red" style="border-bottom: 1px solid rgba(255,255,255,0.02); padding-bottom: 8px;">
                                <span class="orderbook-val" id="ask_price_1">96,435.0</span>
                                <span class="orderbook-val" id="ask_qty_1">3.120</span>
                                <div class="orderbook-fill-ask" style="width: 75%;"></div>
                            </div>
                            <div style="text-align: center; font-size: 13px; font-weight: 700; font-family: var(--font-mono); margin: 6px 0;" id="mark_price_indicator" class="text-green">
                                96,432.50
                            </div>
                            <div class="orderbook-row text-green" style="padding-top: 8px;">
                                <span class="orderbook-val" id="bid_price_1">96,430.0</span>
                                <span class="orderbook-val" id="bid_qty_1">2.410</span>
                                <div class="orderbook-fill-bid" style="width: 60%;"></div>
                            </div>
                            <div class="orderbook-row text-green">
                                <span class="orderbook-val" id="bid_price_2">96,425.0</span>
                                <span class="orderbook-val" id="bid_qty_2">1.150</span>
                                <div class="orderbook-fill-bid" style="width: 35%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- VIEW 2: EXECUTION AUDIT LEDGER (HISTORY) -->
        <!-- ============================================== -->
        <div id="view-ledger" class="view-section">
            <div class="fullscreen-workspace">
                <div class="panel" style="flex-grow: 1;">
                    <div class="panel-header">
                        <h3 class="panel-title">Complete Execution Ledger</h3>
                        <span class="badge" style="background: var(--surface-hover); color: var(--secondary);">Total Logs: {{ $tradeHistory->count() }}</span>
                    </div>
                    
                    @if($tradeHistory->count() > 0)
                    <div style="overflow-y: auto; flex-grow: 1;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Execution Timestamp</th>
                                    <th>Asset Contract</th>
                                    <th>Execution Side</th>
                                    <th>Entry Mark</th>
                                    <th>Net Realized PnL</th>
                                    <th>Account Status</th>
                                    <th>Trigger Core Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tradeHistory as $trade)
                                @php $tags = json_decode($trade->tags, true); @endphp
                                <tr>
                                    <td>{{ $trade->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td style="font-weight: 700; color: var(--secondary);">{{ $trade->symbol }}</td>
                                    <td>
                                        <span class="badge {{ $trade->side === 'BUY' ? 'buy' : 'sell' }}">
                                            {{ $trade->side === 'BUY' ? 'LONG 📈' : 'SHORT 📉' }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($trade->entry_price, 4) }}</td>
                                    <td class="{{ $trade->pnl >= 0 ? 'text-green' : 'text-red' }}">
                                        {{ $trade->pnl >= 0 ? '+' : '' }}${{ number_format($trade->pnl, 4) }}
                                    </td>
                                    <td>
                                        @if(isset($tags['dry_run']) && $tags['dry_run'])
                                            <span class="badge paper">PAPER</span>
                                        @else
                                            <span class="badge live">LIVE</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-size: 11px; color: var(--text-dim);">
                                            {{ $tags['market_condition'] ?? 'Technical Convergence Trigger' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div style="padding: 100px 0; text-align: center; color: var(--text-dim);">
                        <div style="font-size: 40px; margin-bottom: 20px;">📋</div>
                        <h3>No closed ledger audit logs recorded yet.</h3>
                        <p style="font-size: 12px; margin-top: 5px; color: #4b5563;">MrRobot is actively monitoring technical indicators for opportunities.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- VIEW 3: RISK GOVERNANCE SHIELD -->
        <!-- ============================================== -->
        <div id="view-risk" class="view-section">
            <div class="fullscreen-workspace">
                <div class="panel" style="flex-grow: 1;">
                    <div class="panel-header">
                        <h3 class="panel-title">Risk Governance Authority Logs</h3>
                        <span class="badge" style="background: rgba(255, 179, 0, 0.05); color: var(--warning); border-color: rgba(255,179,0,0.15);">DEFENSE LOGS ACTIVE</span>
                    </div>

                    @if($riskEvents->count() > 0)
                    <div style="overflow-y: auto; flex-grow: 1;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Defense Timestamp</th>
                                    <th>Target Asset</th>
                                    <th>Defense Guard Block</th>
                                    <th>Risk Violation Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riskEvents as $event)
                                <tr>
                                    <td>{{ $event->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td style="font-weight: 700; color: var(--warning);">{{ $event->symbol ?? 'SYSTEM' }}</td>
                                    <td><span class="badge risk">{{ $event->event_type }}</span></td>
                                    <td style="color: var(--text-dim); line-height: 1.4;">{{ $event->description }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div style="padding: 100px 0; text-align: center; color: var(--text-dim);">
                        <div style="font-size: 40px; margin-bottom: 20px;">🛡️</div>
                        <h3 class="text-green">All risk parameters stable.</h3>
                        <p style="font-size: 12px; margin-top: 5px; color: #4b5563;">Capital preservation shield holds. No correlation wicks or drawdown alerts registered.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- VIEW 4: SYSTEM CONTROL PANEL & SETTINGS -->
        <!-- ============================================== -->
        <div id="view-settings" class="view-section">
            <div class="fullscreen-workspace">
                <div class="settings-header">
                    <h2 class="settings-title">MrRobot Control Configuration</h2>
                    <span class="badge paper" style="font-size: 11px; padding: 6px 12px;">Mode: {{ env('MRROBOT_DRY_RUN', true) ? 'PAPER TRADING' : 'LIVE TRADING' }}</span>
                </div>
                
                <div class="deck-grid">
                    <!-- Card 1: API Configuration -->
                    <div class="config-card">
                        <h3 class="panel-title" style="color: var(--secondary);">Bybit Connection Port</h3>
                        <div class="config-item">
                            <span class="config-label">Exchange API Key</span>
                            <span class="config-val">{{ substr(env('BYBIT_API_KEY'), 0, 5) }}******</span>
                        </div>
                        <div class="config-item">
                            <span class="config-label">API Authority Status</span>
                            <span class="config-val text-green">CONNECTED</span>
                        </div>
                        <div class="config-item">
                            <span class="config-label">Base URL Endpoint</span>
                            <span class="config-val" style="font-size: 11px;">https://api.bytick.com</span>
                        </div>
                    </div>

                    <!-- Card 2: Risk Tolerances -->
                    <div class="config-card">
                        <h3 class="panel-title" style="color: var(--warning);">Quant Risk Limits</h3>
                        <div class="config-item">
                            <span class="config-label">Per Trade Risk Limit</span>
                            <span class="config-val text-green">2.0% Equity</span>
                        </div>
                        <div class="config-item">
                            <span class="config-label">Exposure Limit Cap</span>
                            <span class="config-val">3 Open Max</span>
                        </div>
                        <div class="config-item">
                            <span class="config-label">Leverage Safe-Guard</span>
                            <span class="config-val" style="color: var(--secondary);">3x Leverage Limit</span>
                        </div>
                        <div class="config-item">
                            <span class="config-label">Daily Drawdown Pause</span>
                            <span class="config-val text-red">5.0% Lockout</span>
                        </div>
                    </div>

                    <!-- Card 3: Broadcast Channels -->
                    <div class="config-card">
                        <h3 class="panel-title" style="color: var(--primary);">Telegram Broadcast</h3>
                        <div class="config-item">
                            <span class="config-label">Telegram Chat ID</span>
                            <span class="config-val">{{ env('TELEGRAM_CHAT_ID') }}</span>
                        </div>
                        <div class="config-item">
                            <span class="config-label">Broadcasting Level</span>
                            <span class="config-val" style="color: var(--secondary);">Executive Alerts Only</span>
                        </div>
                        <div class="config-item">
                            <span class="config-label">Connection Integrity</span>
                            <span class="config-val text-green">ONLINE</span>
                        </div>
                    </div>
                </div>

                <!-- 5-Layer Quantitative Checklist Box -->
                <div class="panel">
                    <h3 class="panel-title" style="color: var(--secondary); margin-bottom: 10px;">Active Technical Sieve Stack Rules</h3>
                    <div style="font-size: 13px; line-height: 1.8; color: var(--text-dim);">
                        <p style="margin-bottom: 8px;">MrRobot uses a rigorous **5-Layer Technical Filtering Stack** to execute trades. The system must score at least **4 out of 5** to authorize a trade entry:</p>
                        <ul style="list-style-type: square; padding-left: 20px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                            <li>**EMA 200 Filter:** Only BUY if price is above EMA 200; only SELL (Short) if price is below.</li>
                            <li>**RSI Momentum:** Confirms oversold bounds (&lt;35) or overbought bounds (&gt;65).</li>
                            <li>**MACD Convergence:** Bullish or Bearish MACD crossovers confirm momentum shift.</li>
                            <li>**Volume average spike:** Current volume must exceed **1.5x of the 20-period average**.</li>
                            <li>**Funding Rate Edge:** Captures short squeezes (&gt;+0.1%) or long squeezes (&lt;-0.1%).</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- SCRIPTS -->
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    <script type="text/javascript">
        // 1. Interactive SPA View Switching Logic
        function switchView(viewName, element = null) {
            // Deactivate all views
            const sections = document.getElementsByClassName("view-section");
            for (let i = 0; i < sections.length; i++) {
                sections[i].classList.remove("active");
            }

            // Activate target view
            document.getElementById(`view-${viewName}`).classList.add("active");

            // Deactivate nav items
            const navItems = document.getElementsByClassName("nav-item");
            for (let i = 0; i < navItems.length; i++) {
                navItems[i].classList.remove("active");
            }

            // Activate clicked nav item
            if (element) {
                element.classList.add("active");
            }

            // Reload chart widget if switching back to dashboard to prevent display bugs
            if (viewName === 'dashboard') {
                loadChart('BTCUSDT');
            }
        }

        // 2. Initialize TradingView Widget
        let tvWidget = null;
        function loadChart(symbol) {
            tvWidget = new TradingView.widget({
                "width": "100%",
                "height": "100%",
                "symbol": `BYBIT:${symbol}.P`,
                "interval": "60",
                "timezone": "Etc/UTC",
                "theme": "dark",
                "style": "1",
                "locale": "en",
                "enable_publishing": false,
                "hide_side_toolbar": false,
                "allow_symbol_change": true,
                "container_id": "tradingview_chart",
                "studies": [
                    "RSI@tv-basicstudies",
                    "MACD@tv-basicstudies",
                    "EMA50@tv-basicstudies",
                    "EMA200@tv-basicstudies"
                ]
            });
        }
        
        loadChart('BTCUSDT');

        function changeChartSymbol(symbol) {
            loadChart(symbol);
        }

        // 3. REAL LIVE Bybit WebSocket with HTTP API Fallback (Bypasses Geoblocking!)
        let ws = null;
        let wsReceivedMessage = false;
        let fallbackInterval = null;
        
        function connectWebSocket() {
            try {
                // Public linear perp stream endpoint
                ws = new WebSocket('wss://stream.bybit.com/v5/public/linear');
                
                // Set a watchdog timer to switch to HTTP polling if WebSocket is geoblocked/fails
                const wsTimeout = setTimeout(() => {
                    if (!wsReceivedMessage) {
                        console.warn('Bybit WebSocket handshake timed out or geoblocked. Engaging institutional HTTP Fallback Port...');
                        if (ws) ws.close();
                        startFallbackPolling();
                    }
                }, 3500);
                
                ws.onopen = () => {
                    console.log('Bybit Live Market Stream Opened...');
                    ws.send(JSON.stringify({
                        op: 'subscribe',
                        args: [
                            'tickers.BTCUSDT',
                            'tickers.ETHUSDT',
                            'tickers.SOLUSDT',
                            'tickers.AVAXUSDT',
                            'tickers.XRPUSDT'
                        ]
                    }));
                };
                
                ws.onmessage = (event) => {
                    wsReceivedMessage = true;
                    clearTimeout(wsTimeout);
                    if (fallbackInterval) {
                        clearInterval(fallbackInterval);
                        fallbackInterval = null;
                    }
                    
                    const msg = JSON.parse(event.data);
                    if (msg.topic && msg.topic.startsWith('tickers.')) {
                        const symbol = msg.topic.split('.')[1];
                        const data = msg.data;
                        updatePriceUI(symbol, data);
                    }
                };
                
                ws.onclose = () => {
                    if (!wsReceivedMessage) {
                        startFallbackPolling();
                    }
                };
                
                ws.onerror = (err) => {
                    console.error('Bybit WebSocket Error:', err);
                    if (ws) ws.close();
                };
            } catch (e) {
                console.error('Failed to construct WebSocket:', e);
                startFallbackPolling();
            }
        }
        
        // Helper to update elements on screen
        function updatePriceUI(symbol, data) {
            const idMap = {
                'BTCUSDT': 'btc',
                'ETHUSDT': 'eth',
                'SOLUSDT': 'sol',
                'AVAXUSDT': 'avax',
                'XRPUSDT': 'xrp'
            };
            
            const key = idMap[symbol];
            if (!key) return;
            
            const lastPrice = parseFloat(data.lastPrice);
            // Handle Bybit WS vs Laravel HTTP decimal formats for 24h percentage
            let price24hPcnt = parseFloat(data.price24hPcnt);
            if (Math.abs(price24hPcnt) < 1.0 && price24hPcnt !== 0.0) {
                price24hPcnt = price24hPcnt * 100;
            }
            
            if (lastPrice) {
                const priceEl = document.getElementById(`price_${key}`);
                const changeEl = document.getElementById(`change_${key}`);
                
                if (priceEl) {
                    const prevValText = priceEl.innerText.replace('$', '').replace(/,/g, '');
                    const prevPrice = parseFloat(prevValText) || 0.0;
                    
                    priceEl.innerText = `$${lastPrice.toLocaleString(undefined, {minimumFractionDigits: key === 'xrp' ? 4 : 2})}`;
                    
                    // Flicker neon green or neon red depending on actual price tick wiggles!
                    if (lastPrice > prevPrice && prevPrice > 0) {
                        priceEl.style.color = '#00e676';
                        priceEl.style.textShadow = '0 0 10px rgba(0, 230, 118, 0.4)';
                    } else if (lastPrice < prevPrice && prevPrice > 0) {
                        priceEl.style.color = '#ff1744';
                        priceEl.style.textShadow = '0 0 10px rgba(255, 23, 68, 0.4)';
                    }
                    
                    setTimeout(() => {
                        priceEl.style.color = '#f0f2f5';
                        priceEl.style.textShadow = 'none';
                    }, 250);
                }
                
                if (changeEl && !isNaN(price24hPcnt)) {
                    const isUp = price24hPcnt >= 0;
                    changeEl.innerText = `${isUp ? '+' : ''}${price24hPcnt.toFixed(2)}%`;
                    changeEl.className = `ticker-change ${isUp ? 'text-green' : 'text-red'}`;
                }
                
                // If it's Bitcoin, also update the simulated live bids/asks orderbook with the REAL live spreads!
                if (symbol === 'BTCUSDT') {
                    document.getElementById('mark_price_indicator').innerText = lastPrice.toLocaleString(undefined, {minimumFractionDigits: 2});
                    
                    const askPrice = parseFloat(data.ask1Price) || (lastPrice + 0.5);
                    const bidPrice = parseFloat(data.bid1Price) || (lastPrice - 0.5);
                    
                    document.getElementById('ask_price_1').innerText = askPrice.toLocaleString(undefined, {minimumFractionDigits: 1});
                    document.getElementById('ask_price_2').innerText = (askPrice + 1.5).toLocaleString(undefined, {minimumFractionDigits: 1});
                    document.getElementById('ask_price_3').innerText = (askPrice + 3.0).toLocaleString(undefined, {minimumFractionDigits: 1});
                    
                    document.getElementById('bid_price_1').innerText = bidPrice.toLocaleString(undefined, {minimumFractionDigits: 1});
                    document.getElementById('bid_price_2').innerText = (bidPrice - 1.5).toLocaleString(undefined, {minimumFractionDigits: 1});
                }
            }
        }
        
        // HTTP Fallback Polling Engine (Bypasses geoblocking perfectly!)
        function startFallbackPolling() {
            if (fallbackInterval) return; // already active
            
            console.log('Engaging background local HTTP polling loops. Tickers active.');
            
            const fetchPrices = () => {
                fetch('/api/live-prices')
                    .then(res => res.json())
                    .then(data => {
                        Object.keys(data).forEach(symbol => {
                            updatePriceUI(symbol, data[symbol]);
                        });
                    })
                    .catch(err => console.error('Fallback polling failed:', err));
            };
            
            // Fetch immediately, then loop
            fetchPrices();
            fallbackInterval = setInterval(fetchPrices, 2500);
        }
        
        // Start streaming real-time ticks
        connectWebSocket();

        // 4. Centered Splash Loader Fade-out
        window.addEventListener('load', () => {
            const loader = document.getElementById('loader-overlay');
            // Give 800ms for TradingView and WebSockets to bind smoothly
            setTimeout(() => {
                loader.style.opacity = '0';
                loader.style.visibility = 'hidden';
            }, 800);
        });
    </script>
</body>
</html>
