<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepositSeeder extends Seeder
{
    public function run(): void
    {
        $methods = ['bitcoin', 'ethereum', 'usdt', 'bank_transfer', 'card'];
        $statuses = ['completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'pending', 'failed'];

        $walletAddresses = [
            'bitcoin' => '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa',
            'ethereum' => '0x742d35Cc6634C0532925a3b844Bc9e7595f2bD3e',
            'usdt' => 'TN2YrGZaK1z4V2e8U3x7J5m9Qw6Lb1kFo8',
        ];

        $transactionHashes = [
            '0x8a3f2b1c4d5e6f7a8b9c0d1e2f3a4b5c6d7e8f9a0b1c2d3e4f5a6b7c8d9e0f1a',
            '0x4b5c6d7e8f9a0b1c2d3e4f5a6b7c8d9e0f1a2b3c4d5e6f7a8b9c0d1e2f3a4b5c',
            '0xf1a2b3c4d5e6f7a8b9c0d1e2f3a4b5c6d7e8f9a0b1c2d3e4f5a6b7c8d9e0f1a2',
        ];

        $deposits = [
            ['user_id' => 2, 'amount' => 2500.00, 'method' => 'bitcoin', 'status' => 'completed'],
            ['user_id' => 3, 'amount' => 5000.00, 'method' => 'ethereum', 'status' => 'completed'],
            ['user_id' => 4, 'amount' => 1000.00, 'method' => 'bank_transfer', 'status' => 'completed'],
            ['user_id' => 5, 'amount' => 25000.00, 'method' => 'usdt', 'status' => 'completed'],
            ['user_id' => 6, 'amount' => 3500.00, 'method' => 'card', 'status' => 'completed'],
            ['user_id' => 7, 'amount' => 750.00, 'method' => 'bitcoin', 'status' => 'completed'],
            ['user_id' => 8, 'amount' => 8000.00, 'method' => 'ethereum', 'status' => 'pending'],
            ['user_id' => 9, 'amount' => 2000.00, 'method' => 'usdt', 'status' => 'completed'],
            ['user_id' => 10, 'amount' => 15000.00, 'method' => 'bank_transfer', 'status' => 'completed'],
            ['user_id' => 11, 'amount' => 1200.00, 'method' => 'card', 'status' => 'failed'],
            ['user_id' => 12, 'amount' => 5000.00, 'method' => 'bitcoin', 'status' => 'completed'],
            ['user_id' => 13, 'amount' => 600.00, 'method' => 'usdt', 'status' => 'completed'],
            ['user_id' => 14, 'amount' => 20000.00, 'method' => 'ethereum', 'status' => 'completed'],
            ['user_id' => 15, 'amount' => 3000.00, 'method' => 'bank_transfer', 'status' => 'pending'],
            ['user_id' => 16, 'amount' => 1500.00, 'method' => 'card', 'status' => 'completed'],
            ['user_id' => 17, 'amount' => 4500.00, 'method' => 'bitcoin', 'status' => 'completed'],
            ['user_id' => 18, 'amount' => 10000.00, 'method' => 'usdt', 'status' => 'completed'],
            ['user_id' => 19, 'amount' => 2200.00, 'method' => 'ethereum', 'status' => 'failed'],
            ['user_id' => 20, 'amount' => 800.00, 'method' => 'card', 'status' => 'completed'],
            ['user_id' => 21, 'amount' => 7000.00, 'method' => 'bank_transfer', 'status' => 'completed'],
            ['user_id' => 2, 'amount' => 1800.00, 'method' => 'usdt', 'status' => 'completed'],
            ['user_id' => 5, 'amount' => 12000.00, 'method' => 'bitcoin', 'status' => 'completed'],
            ['user_id' => 9, 'amount' => 200.00, 'method' => 'card', 'status' => 'pending'],
            ['user_id' => 10, 'amount' => 25000.00, 'method' => 'ethereum', 'status' => 'completed'],
            ['user_id' => 16, 'amount' => 3500.00, 'method' => 'bank_transfer', 'status' => 'completed'],
        ];

        foreach ($deposits as $index => $deposit) {
            $method = $deposit['method'];
            $created = now()->subDays(rand(0, 30))->subHours(rand(0, 23));

            DB::table('deposits')->insert([
                'user_id' => $deposit['user_id'],
                'amount' => $deposit['amount'],
                'method' => $method,
                'wallet_address' => $walletAddresses[$method] ?? null,
                'transaction_hash' => in_array($method, ['bitcoin', 'ethereum', 'usdt'])
                    ? $transactionHashes[array_rand($transactionHashes)]
                    : null,
                'status' => $deposit['status'],
                'admin_note' => $deposit['status'] === 'failed'
                    ? 'Transaction could not be verified'
                    : ($deposit['status'] === 'completed' ? 'Deposit confirmed and credited' : null),
                'processed_at' => $deposit['status'] === 'completed'
                    ? $created->copy()->addMinutes(rand(5, 120))
                    : ($deposit['status'] === 'failed' ? $created->copy()->addHours(rand(1, 6)) : null),
                'created_at' => $created,
                'updated_at' => $deposit['status'] !== 'pending' ? $created->copy()->addMinutes(rand(5, 120)) : $created,
            ]);
        }
    }
}
