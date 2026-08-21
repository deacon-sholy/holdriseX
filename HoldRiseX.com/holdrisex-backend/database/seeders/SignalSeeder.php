<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SignalSeeder extends Seeder
{
    public function run(): void
    {
        $signals = [
            [
                'symbol' => 'BTC/USD',
                'direction' => 'buy',
                'entry_price' => 67250.00,
                'take_profit' => 71000.00,
                'stop_loss' => 65400.00,
                'status' => 'active',
                'risk_reward' => 2.30,
                'analysis' => 'Bitcoin is holding above the 50-day moving average with rising volume. A breakout above resistance at 68,000 could accelerate momentum toward the 71,000 target. RSI at 58 leaves room for further upside.',
                'analyst' => 'Marcus Webb',
            ],
            [
                'symbol' => 'ETH/USD',
                'direction' => 'buy',
                'entry_price' => 3480.00,
                'take_profit' => 3850.00,
                'stop_loss' => 3320.00,
                'status' => 'active',
                'risk_reward' => 2.19,
                'analysis' => 'Ethereum shows a bullish flag on the 4H chart after retesting support near 3,400. Staking inflows remain strong and gas fees are stable, suggesting healthy network demand.',
                'analyst' => 'Priya Nair',
            ],
            [
                'symbol' => 'EURUSD',
                'direction' => 'sell',
                'entry_price' => 1.09,
                'take_profit' => 1.08,
                'stop_loss' => 1.10,
                'status' => 'active',
                'risk_reward' => 2.00,
                'analysis' => 'The euro faces renewed pressure as dovish ECB commentary contrasts with resilient US data. Price rejected the 1.0950 supply zone and is forming lower highs on the daily chart.',
                'analyst' => 'Daniel Kovacs',
            ],
            [
                'symbol' => 'GBPJPY',
                'direction' => 'buy',
                'entry_price' => 192.40,
                'take_profit' => 196.80,
                'stop_loss' => 190.10,
                'status' => 'active',
                'risk_reward' => 1.91,
                'analysis' => 'Cable strength against a weakening yen keeps GBPJPY in an uptrend channel. The pair bounced off the mid-channel trendline; watch for a close above 193.00 to confirm continuation.',
                'analyst' => 'Sarah Whitfield',
            ],
            [
                'symbol' => 'SOL/USD',
                'direction' => 'buy',
                'entry_price' => 168.50,
                'take_profit' => 195.00,
                'stop_loss' => 158.00,
                'status' => 'active',
                'risk_reward' => 2.52,
                'analysis' => 'Solana network activity and DEX volumes are climbing steadily. SOL reclaimed the 165 support level and is targeting the previous high near 195 with strong relative strength versus BTC.',
                'analyst' => 'Leo Zhang',
            ],
            [
                'symbol' => 'XAUUSD',
                'direction' => 'sell',
                'entry_price' => 2412.00,
                'take_profit' => 2340.00,
                'stop_loss' => 2455.00,
                'status' => 'closed',
                'risk_reward' => 1.60,
                'analysis' => 'Gold hit overbought conditions near all-time highs while real yields ticked higher. The short played out as price retreated from the 2,420 resistance block toward our take-profit zone.',
                'analyst' => 'Amelia Ford',
            ],
            [
                'symbol' => 'BTC/USD',
                'direction' => 'sell',
                'entry_price' => 69800.00,
                'take_profit' => 66800.00,
                'stop_loss' => 71200.00,
                'status' => 'closed',
                'risk_reward' => 2.14,
                'analysis' => 'A rejection wick at weekly resistance plus declining exchange outflows signaled short-term exhaustion. The pullback to 67k completed before buyers stepped back in.',
                'analyst' => 'Marcus Webb',
            ],
            [
                'symbol' => 'USDCAD',
                'direction' => 'sell',
                'entry_price' => 1.37,
                'take_profit' => 1.36,
                'stop_loss' => 1.38,
                'status' => 'closed',
                'risk_reward' => 1.75,
                'analysis' => 'Crude oil rally supported the Canadian dollar while softer US retail sales weighed on the greenback. The pair broke below its ascending trendline and reached target within three sessions.',
                'analyst' => 'Daniel Kovacs',
            ],
            [
                'symbol' => 'ETH/BTC',
                'direction' => 'buy',
                'entry_price' => 0.05,
                'take_profit' => 0.06,
                'stop_loss' => 0.05,
                'status' => 'expired',
                'risk_reward' => 1.80,
                'analysis' => 'The ETH/BTC ratio approached multi-month support with a potential double-bottom setup. Momentum failed to confirm before the validity window closed, so the signal expired without entry.',
                'analyst' => 'Priya Nair',
            ],
            [
                'symbol' => 'AUDNZD',
                'direction' => 'sell',
                'entry_price' => 1.09,
                'take_profit' => 1.08,
                'stop_loss' => 1.10,
                'status' => 'expired',
                'risk_reward' => 1.50,
                'analysis' => 'Divergence between RBA and RBNZ rate expectations favored downside, but price consolidated sideways through the signal window and never triggered the entry confirmation.',
                'analyst' => 'Sarah Whitfield',
            ],
            [
                'symbol' => 'BNB/USD',
                'direction' => 'buy',
                'entry_price' => 592.00,
                'take_profit' => 650.00,
                'stop_loss' => 565.00,
                'status' => 'pending',
                'risk_reward' => 2.15,
                'analysis' => 'BNB is coiling below the 600 psychological level. Waiting for a confirmed break and retest of 600 before activation; exchange burn metrics continue to reduce circulating supply.',
                'analyst' => 'Leo Zhang',
            ],
            [
                'symbol' => 'USDJPY',
                'direction' => 'buy',
                'entry_price' => 149.20,
                'take_profit' => 152.50,
                'stop_loss' => 147.60,
                'status' => 'pending',
                'risk_reward' => 2.06,
                'analysis' => 'BoJ policy uncertainty is keeping yen weakness intact. Entry will be armed once price clears the 149.50 pivot with volume; intervention risk remains a key caveat near 150.',
                'analyst' => 'Amelia Ford',
            ],
        ];

        foreach ($signals as $signal) {
            DB::table('signals')->insert(array_merge($signal, [
                'created_at' => now()->subDays(rand(1, 21)),
                'updated_at' => now(),
            ]));
        }
    }
}
