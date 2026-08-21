<?php

namespace App\Console\Commands;

use App\Models\CopyTrade;
use Illuminate\Console\Command;

class UpdateCopyTradingPnl extends Command
{
    protected $signature = 'copy-trading:update-pnl';
    protected $description = 'Simulate PnL updates for open copy trades based on real market prices';

    public function handle(): int
    {
        $openTrades = CopyTrade::where('status', 'open')->with('copyTrader')->get();

        if ($openTrades->isEmpty()) {
            $this->info('No open copy trades to update.');
            return 0;
        }

        $updated = 0;

        foreach ($openTrades as $trade) {
            $currentPrice = $this->fetchMarketPrice($trade->symbol);

            if ($trade->pnl == 0) {
                $openPrice = $currentPrice;
            } else {
                $openPrice = $currentPrice - ($trade->pnl / max($trade->lots, 0.01));
            }

            if ($trade->copyTrader && $trade->copyTrader->win_rate > 50) {
                $drift = (mt_rand(-50, 100) / 10000) * $currentPrice;
            } else {
                $drift = (mt_rand(-100, 50) / 10000) * $currentPrice;
            }

            $simulatedPrice = $currentPrice + $drift;
            $newPnl = ($simulatedPrice - $openPrice) * $trade->lots;

            $trade->update(['pnl' => round($newPnl, 2)]);
            $updated++;
        }

        $this->info("Updated PnL for {$updated} open copy trade(s).");
        return 0;
    }

    private function fetchMarketPrice(string $symbol): float
    {
        $symbol = strtolower($symbol);
        $map = [
            'btcusd' => 'bitcoin', 'ethusd' => 'ethereum', 'xrpusd' => 'ripple',
            'solusd' => 'solana', 'dogeusd' => 'dogecoin', 'adusd' => 'cardano',
        ];
        $coinId = $map[$symbol] ?? null;

        if ($coinId) {
            try {
                $url = "https://api.coingecko.com/api/v3/simple/price?ids={$coinId}&vs_currencies=usd";
                $ctx = stream_context_create(['http' => ['timeout' => 3]]);
                $response = @file_get_contents($url, false, $ctx);
                if ($response) {
                    $data = json_decode($response, true);
                    if (isset($data[$coinId]['usd'])) {
                        return (float) $data[$coinId]['usd'];
                    }
                }
            } catch (\Throwable $e) {}
        }

        return 1.0 + (float)(crc32($symbol) % 10000) / 100;
    }
}
