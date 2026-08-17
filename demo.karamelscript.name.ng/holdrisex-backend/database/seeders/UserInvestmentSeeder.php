<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserInvestmentSeeder extends Seeder
{
    public function run(): void
    {
        $investments = [
            ['user_id' => 2, 'plan_id' => 1, 'amount' => 500.00, 'daily_return_earned' => 75.00, 'total_earned' => 75.00, 'start_date' => now()->subDays(3), 'end_date' => now()->addDays(27), 'status' => 'active'],
            ['user_id' => 3, 'plan_id' => 2, 'amount' => 3000.00, 'daily_return_earned' => 315.00, 'total_earned' => 315.00, 'start_date' => now()->subDays(5), 'end_date' => now()->addDays(25), 'status' => 'active'],
            ['user_id' => 4, 'plan_id' => 1, 'amount' => 800.00, 'daily_return_earned' => 120.00, 'total_earned' => 120.00, 'start_date' => now()->subDays(2), 'end_date' => now()->addDays(28), 'status' => 'active'],
            ['user_id' => 5, 'plan_id' => 3, 'amount' => 15000.00, 'daily_return_earned' => 2250.00, 'total_earned' => 2250.00, 'start_date' => now()->subDays(7), 'end_date' => now()->addDays(23), 'status' => 'active'],
            ['user_id' => 6, 'plan_id' => 1, 'amount' => 250.00, 'daily_return_earned' => 37.50, 'total_earned' => 37.50, 'start_date' => now()->subDays(10), 'end_date' => now()->addDays(20), 'status' => 'active'],
            ['user_id' => 8, 'plan_id' => 2, 'amount' => 2000.00, 'daily_return_earned' => 210.00, 'total_earned' => 210.00, 'start_date' => now()->subDays(4), 'end_date' => now()->addDays(26), 'status' => 'active'],
            ['user_id' => 10, 'plan_id' => 4, 'amount' => 50000.00, 'daily_return_earned' => 10500.00, 'total_earned' => 10500.00, 'start_date' => now()->subDays(14), 'end_date' => now()->addDays(16), 'status' => 'active'],
            ['user_id' => 12, 'plan_id' => 2, 'amount' => 1500.00, 'daily_return_earned' => 157.50, 'total_earned' => 157.50, 'start_date' => now()->subDays(6), 'end_date' => now()->addDays(24), 'status' => 'active'],
            ['user_id' => 14, 'plan_id' => 5, 'amount' => 200000.00, 'daily_return_earned' => 60000.00, 'total_earned' => 60000.00, 'start_date' => now()->subDays(20), 'end_date' => now()->addDays(10), 'status' => 'active'],
            ['user_id' => 17, 'plan_id' => 1, 'amount' => 600.00, 'daily_return_earned' => 90.00, 'total_earned' => 90.00, 'start_date' => now()->subDays(1), 'end_date' => now()->addDays(29), 'status' => 'active'],
            ['user_id' => 18, 'plan_id' => 3, 'amount' => 8000.00, 'daily_return_earned' => 1200.00, 'total_earned' => 1200.00, 'start_date' => now()->subDays(8), 'end_date' => now()->addDays(22), 'status' => 'active'],
            ['user_id' => 19, 'plan_id' => 1, 'amount' => 350.00, 'daily_return_earned' => 52.50, 'total_earned' => 52.50, 'start_date' => now()->subDays(12), 'end_date' => now()->addDays(18), 'status' => 'active'],
            ['user_id' => 20, 'plan_id' => 2, 'amount' => 4000.00, 'daily_return_earned' => 420.00, 'total_earned' => 420.00, 'start_date' => now()->subDays(9), 'end_date' => now()->addDays(21), 'status' => 'active'],
            ['user_id' => 21, 'plan_id' => 2, 'amount' => 2500.00, 'daily_return_earned' => 0.00, 'total_earned' => 0.00, 'start_date' => now()->subDays(30), 'end_date' => now()->subDays(0), 'status' => 'completed'],
            ['user_id' => 7, 'plan_id' => 1, 'amount' => 150.00, 'daily_return_earned' => 0.00, 'total_earned' => 0.00, 'start_date' => now()->subDays(35), 'end_date' => now()->subDays(5), 'status' => 'completed'],
        ];

        foreach ($investments as $investment) {
            DB::table('user_investments')->insert(array_merge($investment, [
                'created_at' => $investment['start_date'],
                'updated_at' => now(),
            ]));
        }
    }
}
