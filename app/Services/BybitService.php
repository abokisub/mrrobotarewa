<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BybitService
{
    protected $apiKey;
    protected $apiSecret;
    protected $baseUrl;
    protected $recvWindow;

    public function __construct()
    {
        $this->apiKey = config('services.bybit.key');
        $this->apiSecret = config('services.bybit.secret');
        $this->baseUrl = config('services.bybit.base_url', 'https://api.bybit.com');
        $this->recvWindow = config('services.bybit.recv_window', 5000);
    }

    /**
     * Generate HMAC Signature for Bybit V5 API
     */
    protected function generateSignature($timestamp, $params = [], $method = 'POST')
    {
        if (strtoupper($method) === 'GET') {
            $paramStr = http_build_query($params);
        } else {
            $paramStr = !empty($params) ? json_encode($params) : "";
        }

        $stringToSign = $timestamp . $this->apiKey . $this->recvWindow . $paramStr;
        return hash_hmac('sha256', $stringToSign, $this->apiSecret);
    }

    /**
     * Make an authenticated request to Bybit V5
     */
    public function makeRequest($method, $endpoint, $params = [])
    {
        $timestamp = now()->getTimestampMs();
        $signature = $this->generateSignature($timestamp, $params, $method);

        $headers = [
            'X-BAPI-API-KEY' => $this->apiKey,
            'X-BAPI-SIGN' => $signature,
            'X-BAPI-TIMESTAMP' => $timestamp,
            'X-BAPI-RECV-WINDOW' => $this->recvWindow,
            'Content-Type' => 'application/json',
        ];

        $url = $this->baseUrl . $endpoint;

        try {
            $options = [];
            $proxy = env('BYBIT_PROXY');
            if (!empty($proxy)) {
                $options['proxy'] = $proxy;
            }

            if (strtoupper($method) === 'GET') {
                $response = Http::withHeaders($headers)->withOptions($options)->get($url, $params);
            } else {
                $response = Http::withHeaders($headers)->withOptions($options)->post($url, $params);
            }

            if ($response->failed()) {
                Log::error("Bybit API HTTP Error: " . $response->body());
                return [
                    'success' => false,
                    'message' => $response->json('retMsg') ?? 'Unknown HTTP error',
                    'code' => $response->json('retCode')
                ];
            }

            $retCode = (int) $response->json('retCode');
            if ($retCode !== 0) {
                Log::error("Bybit Business Error: " . $response->json('retMsg') . " (Code: " . $retCode . ")");
                return [
                    'success' => false,
                    'message' => $response->json('retMsg') ?? 'Business rule violation',
                    'code' => $retCode
                ];
            }

            return [
                'success' => true,
                'data' => $response->json('result'),
                'time' => $response->json('time')
            ];

        } catch (\Exception $e) {
            Log::error("Bybit Exception: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get Market Tickers
     */
    public function getTickers($category = 'linear', $symbol = null)
    {
        $params = ['category' => $category];
        if ($symbol) {
            $params['symbol'] = $symbol;
        }

        return $this->makeRequest('GET', '/v5/market/tickers', $params);
    }

    /**
     * Get Wallet Balance
     */
    public function getWalletBalance($accountType = 'UNIFIED')
    {
        return $this->makeRequest('GET', '/v5/account/wallet-balance', ['accountType' => $accountType]);
    }

    /**
     * Place an Order
     */
    public function placeOrder($category, $symbol, $side, $orderType, $qty, $price = null, $extraParams = [])
    {
        $params = array_merge([
            'category' => $category,
            'symbol' => $symbol,
            'side' => $side,
            'orderType' => $orderType,
            'qty' => (string) $qty,
            'timeInForce' => 'GTC',
        ], $extraParams);

        if ($price) {
            $params['price'] = (string) $price;
        }

        return $this->makeRequest('POST', '/v5/order/create', $params);
    }

    /**
     * Set Leverage (For Derivatives)
     */
    public function setLeverage($symbol, $leverage, $category = 'linear')
    {
        return $this->makeRequest('POST', '/v5/position/set-leverage', [
            'category' => $category,
            'symbol' => $symbol,
            'buyLeverage' => (string) $leverage,
            'sellLeverage' => (string) $leverage,
        ]);
    }

    /**
     * Get Positions
     */
    public function getPositions($category = 'linear', $symbol = null)
    {
        $params = ['category' => $category];
        if ($symbol) $params['symbol'] = $symbol;

        return $this->makeRequest('GET', '/v5/position/list', $params);
    }
}
