<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CopyTraderSeeder extends Seeder
{
    public function run(): void
    {
        $traders = [
            [
                'name' => 'Alex Morgan',
                'specialty' => 'forex',
                'win_rate' => 72.50,
                'monthly_return' => 15.30,
                'total_followers' => 450,
                'aum' => 2100000.00,
                'risk_level' => 'low',
                'description' => 'Experienced forex trader specializing in major currency pairs. Consistent returns with disciplined risk management. 10+ years of trading experience.',
            ],
            [
                'name' => 'Sarah Chen',
                'specialty' => 'crypto',
                'win_rate' => 68.20,
                'monthly_return' => 22.70,
                'total_followers' => 320,
                'aum' => 1500000.00,
                'risk_level' => 'medium',
                'description' => 'Crypto market specialist focused on Bitcoin and Ethereum. Leverages market volatility for maximum gains with smart position sizing.',
            ],
            [
                'name' => 'James Rodriguez',
                'specialty' => 'mixed',
                'win_rate' => 75.10,
                'monthly_return' => 12.80,
                'total_followers' => 680,
                'aum' => 3200000.00,
                'risk_level' => 'low',
                'description' => 'Multi-asset trader with expertise across forex, crypto, and equities. Conservative approach prioritizing capital preservation.',
            ],
            [
                'name' => 'Emily Watson',
                'specialty' => 'forex',
                'win_rate' => 62.80,
                'monthly_return' => 18.50,
                'total_followers' => 210,
                'aum' => 890000.00,
                'risk_level' => 'high',
                'description' => 'Aggressive forex trader focusing on high-volatility sessions. Larger positions with tight stop losses for maximum profit potential.',
            ],
            [
                'name' => 'David Kim',
                'specialty' => 'crypto',
                'win_rate' => 70.30,
                'monthly_return' => 25.10,
                'total_followers' => 150,
                'aum' => 650000.00,
                'risk_level' => 'medium',
                'description' => 'DeFi and altcoin specialist. Identifies early opportunities in emerging crypto projects with strong fundamentals.',
            ],
            [
                'name' => 'Lisa Park',
                'specialty' => 'mixed',
                'win_rate' => 66.90,
                'monthly_return' => 14.20,
                'total_followers' => 380,
                'aum' => 1800000.00,
                'risk_level' => 'high',
                'description' => 'Versatile trader across multiple markets. Uses algorithmic strategies combined with technical analysis for consistent returns.',
            ],
        ];

        foreach ($traders as $trader) {
            DB::table('copy_traders')->insert(array_merge($trader, [
                'is_active' => true,
                'created_at' => now()->subDays(rand(60, 180)),
                'updated_at' => now(),
            ]));
        }
    }
}
