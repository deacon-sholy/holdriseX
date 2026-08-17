<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            InvestmentPlanSeeder::class,
            DepositSeeder::class,
            WithdrawalSeeder::class,
            TradeSeeder::class,
            CopyTraderSeeder::class,
            KycSeeder::class,
            AnnouncementSeeder::class,
            AuditLogSeeder::class,
            SettingSeeder::class,
            UserInvestmentSeeder::class,
        ]);
    }
}
