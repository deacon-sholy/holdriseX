<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TradeSeeder extends Seeder
{
    public function run(): void
    {
        $symbols = [
            ['symbol' => 'BTC/USD', 'asset_type' => 'crypto', 'entry_range' => [42000, 68000]],
            ['symbol' => 'ETH/USD', 'asset_type' => 'crypto', 'entry_range' => [2200, 3800]],
            ['symbol' => 'SOL/USD', 'asset_type' => 'crypto', 'entry_range' => [80, 180]],
            ['symbol' => 'EUR/USD', 'asset_type' => 'forex', 'entry_range' => [1.0500, 1.1200]],
            ['symbol' => 'GBP/USD', 'asset_type' => 'forex', 'entry_range' => [1.2100, 1.2800]],
            ['symbol' => 'USD/JPY', 'asset_type' => 'forex', 'entry_range' => [145.00, 158.00]],
            ['symbol' => 'XAU/USD', 'asset_type' => 'commodities', 'entry_range' => [1900, 2400]],
            ['symbol' => 'AAPL', 'asset_type' => 'stocks', 'entry_range' => [165, 210]],
            ['symbol' => 'TSLA', 'asset_type' => 'stocks', 'entry_range' => [180, 280]],
        ];

        $trades = [];

        for ($i = 0; $i < 30; $i++) {
            $sym = $symbols[array_rand($symbols)];
            $userId = rand(2, 21);
            $type = rand(0, 1) === 0 ? 'buy' : 'sell';
            $entryPrice = round($sym['entry_range'][0] + lcg_value() * ($sym['entry_range'][1] - $sym['entry_range'][0]), 6);

            $priceChange = (lcg_value() - 0.45) * 0.15;
            $currentPrice = round($entryPrice * (1 + $priceChange), 6);

            $lotSize = round(0.01 + lcg_value() * 4.99, 4);
            $isPositive = lcg_value() > 0.4;
            $pnl = $isPositive
                ? round(50 + lcg_value() * 4950, 2)
                : round(-(50 + lcg_value() * 2950), 2);

            $status = $i < 12 ? 'open' : (rand(0, 1) === 0 ? 'closed' : 'open');
            $created = now()->subDays(rand(0, 28))->subHours(rand(0, 23));
            $closedAt = null;

            if ($status === 'closed') {
                $closedAt = $created->copy()->addHours(rand(1, 168));
            }

            $trades[] = [
                'user_id' => $userId,
                'symbol' => $sym['symbol'],
                'type' => $type,
                'asset_type' => $sym['asset_type'],
                'entry_price' => $entryPrice,
                'current_price' => $status === 'open' ? $currentPrice : ($type === 'buy' ? $currentPrice : $currentPrice),
                'lot_size' => $lotSize,
                'pnl' => $status === 'open' ? 0 : $pnl,
                'status' => $status,
                'closed_at' => $closedAt,
                'created_at' => $created,
                'updated_at' => $closedAt ?? $created,
            ];
        }

        foreach ($trades as $trade) {
            DB::table('trades')->insert($trade);
        }
    }
}
