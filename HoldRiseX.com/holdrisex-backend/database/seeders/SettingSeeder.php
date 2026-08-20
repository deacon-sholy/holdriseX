<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'Algo Trading', 'group_name' => 'general'],
            ['key' => 'site_url', 'value' => 'https://HoldRiseX.com/holdrisex', 'group_name' => 'general'],
            ['key' => 'default_currency', 'value' => 'USD', 'group_name' => 'general'],
            ['key' => 'timezone', 'value' => 'UTC', 'group_name' => 'general'],
            ['key' => 'maintenance_mode', 'value' => 'false', 'group_name' => 'general'],

            // Trading
            ['key' => 'default_leverage', 'value' => '1:100', 'group_name' => 'trading'],
            ['key' => 'max_positions', 'value' => '10', 'group_name' => 'trading'],
            ['key' => 'spread_markup', 'value' => '0.5', 'group_name' => 'trading'],
            ['key' => 'copy_trading_fee', 'value' => '10', 'group_name' => 'trading'],

            // Payments
            ['key' => 'min_deposit', 'value' => '100', 'group_name' => 'payments'],
            ['key' => 'max_deposit', 'value' => '100000', 'group_name' => 'payments'],
            ['key' => 'withdrawal_fee', 'value' => '2.5', 'group_name' => 'payments'],
            ['key' => 'processing_time', 'value' => '24', 'group_name' => 'payments'],
            ['key' => 'deposit_wallet_bitcoin', 'value' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh', 'group_name' => 'payments'],
            ['key' => 'deposit_wallet_ethereum', 'value' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F', 'group_name' => 'payments'],
            ['key' => 'deposit_wallet_usdt', 'value' => 'TN3W4H6rK2ce4vX9YnFQHwKENnHjoxb3m9', 'group_name' => 'payments'],

            // Security
            ['key' => 'two_factor_enforcement', 'value' => 'false', 'group_name' => 'security'],
            ['key' => 'session_timeout', 'value' => '1440', 'group_name' => 'security'],
            ['key' => 'max_login_attempts', 'value' => '5', 'group_name' => 'security'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
