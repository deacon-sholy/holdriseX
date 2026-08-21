<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WithdrawalSeeder extends Seeder
{
    public function run(): void
    {
        $btcAddress = '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa';
        $ethAddress = '0x742d35Cc6634C0532925a3b844Bc9e7595f2bD3e';
        $usdtAddress = 'TN2YrGZaK1z4V2e8U3x7J5m9Qw6Lb1kFo8';

        $withdrawals = [
            ['user_id' => 2, 'amount' => 5000.00, 'method' => 'bitcoin', 'wallet_address' => $btcAddress, 'status' => 'completed'],
            ['user_id' => 3, 'amount' => 2500.00, 'method' => 'ethereum', 'wallet_address' => $ethAddress, 'status' => 'completed'],
            ['user_id' => 4, 'amount' => 8000.00, 'method' => 'bank_transfer', 'wallet_address' => 'DE89370400440532013000', 'status' => 'completed'],
            ['user_id' => 5, 'amount' => 15000.00, 'method' => 'usdt', 'wallet_address' => $usdtAddress, 'status' => 'pending'],
            ['user_id' => 8, 'amount' => 3500.00, 'method' => 'bitcoin', 'wallet_address' => $btcAddress, 'status' => 'completed'],
            ['user_id' => 10, 'amount' => 12000.00, 'method' => 'ethereum', 'wallet_address' => $ethAddress, 'status' => 'processing'],
            ['user_id' => 12, 'amount' => 2000.00, 'method' => 'usdt', 'wallet_address' => $usdtAddress, 'status' => 'completed'],
            ['user_id' => 14, 'amount' => 25000.00, 'method' => 'bank_transfer', 'wallet_address' => 'GB29NWBK60161331926819', 'status' => 'completed'],
            ['user_id' => 15, 'amount' => 1000.00, 'method' => 'bitcoin', 'wallet_address' => $btcAddress, 'status' => 'rejected'],
            ['user_id' => 17, 'amount' => 6000.00, 'method' => 'ethereum', 'wallet_address' => $ethAddress, 'status' => 'completed'],
            ['user_id' => 18, 'amount' => 8500.00, 'method' => 'usdt', 'wallet_address' => $usdtAddress, 'status' => 'pending'],
            ['user_id' => 19, 'amount' => 300.00, 'method' => 'bitcoin', 'wallet_address' => $btcAddress, 'status' => 'completed'],
            ['user_id' => 20, 'amount' => 4500.00, 'method' => 'bank_transfer', 'wallet_address' => 'FR7630006000011234567890189', 'status' => 'completed'],
            ['user_id' => 21, 'amount' => 7000.00, 'method' => 'ethereum', 'wallet_address' => $ethAddress, 'status' => 'processing'],
            ['user_id' => 9, 'amount' => 1500.00, 'method' => 'usdt', 'wallet_address' => $usdtAddress, 'status' => 'completed'],
        ];

        foreach ($withdrawals as $withdrawal) {
            $created = now()->subDays(rand(0, 21))->subHours(rand(0, 23));
            $processed = null;
            $adminNote = null;

            if ($withdrawal['status'] === 'completed') {
                $processed = $created->copy()->addHours(rand(1, 48));
                $adminNote = 'Withdrawal processed successfully';
            } elseif ($withdrawal['status'] === 'rejected') {
                $processed = $created->copy()->addHours(rand(1, 24));
                $adminNote = 'Insufficient account balance verification failed';
            } elseif ($withdrawal['status'] === 'processing') {
                $adminNote = 'Awaiting blockchain confirmation';
            }

            DB::table('withdrawals')->insert([
                'user_id' => $withdrawal['user_id'],
                'amount' => $withdrawal['amount'],
                'method' => $withdrawal['method'],
                'wallet_address' => $withdrawal['wallet_address'],
                'status' => $withdrawal['status'],
                'admin_note' => $adminNote,
                'processed_at' => $processed,
                'created_at' => $created,
                'updated_at' => $processed ?? $created,
            ]);
        }
    }
}
