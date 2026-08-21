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
            ['key' => 'site_description', 'value' => 'HoldRiseX offers CFD trading on stocks, forex, indices, commodities, and cryptocurrencies with competitive spreads and advanced trading tools.', 'group_name' => 'general'],
            ['key' => 'support_email', 'value' => 'support@HoldRiseX.com', 'group_name' => 'general'],
            ['key' => 'contact_phone', 'value' => '', 'group_name' => 'general'],
            ['key' => 'company_address', 'value' => '', 'group_name' => 'general'],
            ['key' => 'twitter_url', 'value' => '#', 'group_name' => 'general'],
            ['key' => 'linkedin_url', 'value' => '#', 'group_name' => 'general'],
            ['key' => 'facebook_url', 'value' => '#', 'group_name' => 'general'],
            ['key' => 'telegram_url', 'value' => '#', 'group_name' => 'general'],
            ['key' => 'default_currency', 'value' => 'USD', 'group_name' => 'general'],
            ['key' => 'timezone', 'value' => 'UTC', 'group_name' => 'general'],
            ['key' => 'maintenance_mode', 'value' => 'false', 'group_name' => 'general'],
            ['key' => 'allow_registration', 'value' => 'true', 'group_name' => 'general'],

            // Trading
            ['key' => 'default_leverage', 'value' => '1:100', 'group_name' => 'trading'],
            ['key' => 'max_positions', 'value' => '10', 'group_name' => 'trading'],
            ['key' => 'spread_markup', 'value' => '0.5', 'group_name' => 'trading'],
            ['key' => 'copy_trading_fee', 'value' => '10', 'group_name' => 'trading'],

            // Payments
            ['key' => 'min_deposit', 'value' => '100', 'group_name' => 'payments'],
            ['key' => 'max_deposit', 'value' => '100000', 'group_name' => 'payments'],
            ['key' => 'min_withdrawal', 'value' => '50', 'group_name' => 'payments'],
            ['key' => 'max_withdrawal', 'value' => '50000', 'group_name' => 'payments'],
            ['key' => 'withdrawal_fee', 'value' => '2.5', 'group_name' => 'payments'],
            ['key' => 'processing_time', 'value' => '24', 'group_name' => 'payments'],
            ['key' => 'bank_transfer_enabled', 'value' => 'true', 'group_name' => 'payments'],
            ['key' => 'mobile_money_enabled', 'value' => 'false', 'group_name' => 'payments'],
            ['key' => 'deposit_wallet_bitcoin', 'value' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh', 'group_name' => 'payments'],
            ['key' => 'deposit_wallet_ethereum', 'value' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F', 'group_name' => 'payments'],
            ['key' => 'deposit_wallet_usdt', 'value' => 'TN3W4H6rK2ce4vX9YnFQHwKENnHjoxb3m9', 'group_name' => 'payments'],

            // Security
            ['key' => 'two_factor_enforcement', 'value' => 'false', 'group_name' => 'security'],
            ['key' => 'session_timeout', 'value' => '1440', 'group_name' => 'security'],
            ['key' => 'max_login_attempts', 'value' => '5', 'group_name' => 'security'],

            // Notifications
            ['key' => 'email_deposit_enabled', 'value' => 'true', 'group_name' => 'notifications'],
            ['key' => 'email_withdrawal_enabled', 'value' => 'true', 'group_name' => 'notifications'],
            ['key' => 'email_kyc_enabled', 'value' => 'true', 'group_name' => 'notifications'],
            ['key' => 'email_trade_enabled', 'value' => 'true', 'group_name' => 'notifications'],
            ['key' => 'sms_enabled', 'value' => 'false', 'group_name' => 'notifications'],
            ['key' => 'sms_transaction_enabled', 'value' => 'false', 'group_name' => 'notifications'],

            // Email
            ['key' => 'smtp_host', 'value' => '', 'group_name' => 'email'],
            ['key' => 'smtp_port', 'value' => '587', 'group_name' => 'email'],
            ['key' => 'smtp_username', 'value' => '', 'group_name' => 'email'],
            ['key' => 'smtp_password', 'value' => '', 'group_name' => 'email'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'group_name' => 'email'],
            ['key' => 'from_address', 'value' => 'noreply@holdrisex.com', 'group_name' => 'email'],
            ['key' => 'from_name', 'value' => 'HoldRiseX', 'group_name' => 'email'],
            ['key' => 'reply_to_address', 'value' => 'support@holdrisex.com', 'group_name' => 'email'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
