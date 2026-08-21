<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvestmentPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'min_amount' => 100.00,
                'max_amount' => 999.00,
                'daily_return' => 2.50,
                'duration_days' => 30,
                'min_profit' => 75.00,
                'description' => 'Perfect for beginners. Start with as little as $100 and earn 2.5% daily returns over 30 days.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Silver',
                'slug' => 'silver',
                'min_amount' => 1000.00,
                'max_amount' => 4999.00,
                'daily_return' => 3.50,
                'duration_days' => 30,
                'min_profit' => 1050.00,
                'description' => 'Step up your game with Silver. Invest $1,000-$4,999 for 3.5% daily returns over 30 days.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'min_amount' => 5000.00,
                'max_amount' => 24999.00,
                'daily_return' => 5.00,
                'duration_days' => 30,
                'min_profit' => 7500.00,
                'description' => 'Our most popular plan. Earn 5.0% daily on investments from $5,000 to $24,999.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Platinum',
                'slug' => 'platinum',
                'min_amount' => 25000.00,
                'max_amount' => 99999.00,
                'daily_return' => 7.00,
                'duration_days' => 30,
                'min_profit' => 52500.00,
                'description' => 'For serious investors. 7.0% daily returns on investments from $25,000 to $99,999.',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Diamond',
                'slug' => 'diamond',
                'min_amount' => 100000.00,
                'max_amount' => 1000000.00,
                'daily_return' => 10.00,
                'duration_days' => 30,
                'min_profit' => 300000.00,
                'description' => 'Our premium tier. 10.0% daily returns for VIP investors with $100K-$1M.',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('investment_plans')->insert(array_merge($plan, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
