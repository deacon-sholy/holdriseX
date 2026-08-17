<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $logs = [
            ['user_id' => 1, 'action' => 'login', 'details' => 'Admin logged in from dashboard', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 2, 'action' => 'login', 'details' => 'User logged in successfully', 'ip_address' => '10.0.0.45', 'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0) AppleWebKit/605.1.15', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 3, 'action' => 'deposit_approval', 'details' => 'Deposit of $5,000 via ethereum approved for user chidi.okonkwo@yahoo.com', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 15, 'action' => 'withdrawal_rejection', 'details' => 'Withdrawal of $1,000 rejected. Reason: Insufficient balance verification', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'rejected', 'severity' => 'warning'],
            ['user_id' => 1, 'action' => 'settings_change', 'details' => 'Updated payment settings: withdrawal_fee changed from 2.0 to 2.5', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 1, 'action' => 'user_suspension', 'details' => 'User account for minjun.park@naver.com suspended due to suspicious activity', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'critical'],
            ['user_id' => 1, 'action' => 'kyc_approval', 'details' => 'KYC documents verified for user john.carter@gmail.com', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 9, 'action' => 'login', 'details' => 'Failed login attempt - invalid password', 'ip_address' => '172.16.0.88', 'user_agent' => 'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/120.0.0.0 Mobile', 'status' => 'failed', 'severity' => 'warning'],
            ['user_id' => 9, 'action' => 'login', 'details' => 'Failed login attempt - invalid password', 'ip_address' => '172.16.0.88', 'user_agent' => 'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/120.0.0.0 Mobile', 'status' => 'failed', 'severity' => 'warning'],
            ['user_id' => 9, 'action' => 'login', 'details' => 'Account temporarily locked after 3 failed attempts', 'ip_address' => '172.16.0.88', 'user_agent' => 'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/120.0.0.0 Mobile', 'status' => 'failed', 'severity' => 'critical'],
            ['user_id' => 5, 'action' => 'trade_execution', 'details' => 'Buy order executed: 2.5 lots BTC/USD at $65,430.00', 'ip_address' => '10.0.1.22', 'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0) AppleWebKit/605.1.15', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 8, 'action' => 'trade_execution', 'details' => 'Sell order executed: 1.0 lots EUR/USD at 1.0856', 'ip_address' => '10.0.2.15', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 10, 'action' => 'deposit_approval', 'details' => 'Deposit of $15,000 via bank_transfer approved for user yuki.tanaka@icloud.com', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 1, 'action' => 'settings_change', 'details' => 'Maintenance mode toggled', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'warning'],
            ['user_id' => 4, 'action' => 'withdrawal_request', 'details' => 'Withdrawal request of $8,000 via bank_transfer', 'ip_address' => '10.0.0.77', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Edge/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 1, 'action' => 'announcement_publish', 'details' => 'Announcement published: Platform Maintenance Scheduled', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 14, 'action' => 'login', 'details' => 'User logged in from new device', 'ip_address' => '45.33.12.98', 'user_agent' => 'Mozilla/5.0 (iPad; CPU OS 17_0) AppleWebKit/605.1.15', 'status' => 'success', 'severity' => 'warning'],
            ['user_id' => 1, 'action' => 'kyc_rejection', 'details' => 'KYC documents rejected for user liam.obrien@outlook.com. Reason: Image quality too low', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'rejected', 'severity' => 'info'],
            ['user_id' => 7, 'action' => 'trade_execution', 'details' => 'Buy order executed: 0.5 lots XAU/USD at $2,345.67', 'ip_address' => '10.0.3.44', 'user_agent' => 'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/120.0.0.0 Mobile', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 17, 'action' => 'deposit_approval', 'details' => 'Deposit of $4,500 via bitcoin approved for user lucas.fernandes@gmail.com', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 6, 'action' => 'withdrawal_request', 'details' => 'Withdrawal request of $3,500 via usdt', 'ip_address' => '10.0.1.89', 'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0) AppleWebKit/605.1.15', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 1, 'action' => 'user_suspension', 'details' => 'Account for daniel.rossi@libero.it deactivated - KYC rejection appeal denied', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'critical'],
            ['user_id' => 19, 'action' => 'trade_execution', 'details' => 'Sell order executed: 3.0 lots TSLA at $245.30', 'ip_address' => '10.0.2.56', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 11, 'action' => 'login', 'details' => 'User logged in from new IP address', 'ip_address' => '198.51.100.42', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 1, 'action' => 'settings_change', 'details' => 'Trading settings updated: max_positions changed from 5 to 10', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 13, 'action' => 'deposit_approval', 'details' => 'Deposit of $600 via usdt approved for user sophie.laurent@orange.fr', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 18, 'action' => 'trade_execution', 'details' => 'Buy order executed: 1.2 lots ETH/USD at $3,245.80', 'ip_address' => '10.0.3.11', 'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0) AppleWebKit/605.1.15', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 20, 'action' => 'withdrawal_request', 'details' => 'Withdrawal request of $4,500 via bank_transfer', 'ip_address' => '10.0.0.33', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Firefox/120.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 1, 'action' => 'kyc_approval', 'details' => 'KYC documents verified for user priya.sharma@gmail.com', 'ip_address' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
            ['user_id' => 21, 'action' => 'login', 'details' => 'User logged in successfully', 'ip_address' => '10.0.4.17', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0', 'status' => 'success', 'severity' => 'info'],
        ];

        foreach ($logs as $index => $log) {
            DB::table('audit_logs')->insert(array_merge($log, [
                'created_at' => now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
                'updated_at' => now(),
            ]));
        }
    }
}
