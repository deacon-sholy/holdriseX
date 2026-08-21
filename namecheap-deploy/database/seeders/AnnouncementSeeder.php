<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->where('role', 'admin')->value('id');

        $announcements = [
            [
                'title' => 'Platform Maintenance Scheduled',
                'content' => 'We will be performing scheduled maintenance on our trading platform on Saturday, August 23rd from 02:00 UTC to 06:00 UTC. During this time, trading will be temporarily unavailable. All open positions will remain unaffected. We apologize for any inconvenience.',
                'type' => 'maintenance',
                'target_audience' => 'all',
                'status' => 'published',
                'scheduled_at' => null,
                'views_count' => 1243,
            ],
            [
                'title' => 'New Copy Trading Features',
                'content' => 'We are excited to announce new copy trading features! You can now set custom risk limits per trader, track real-time performance metrics, and receive instant notifications when your copied traders execute trades. Update your app to the latest version to enjoy these features.',
                'type' => 'info',
                'target_audience' => 'all',
                'status' => 'published',
                'scheduled_at' => null,
                'views_count' => 2891,
            ],
            [
                'title' => 'Withdrawal Processing Delays',
                'content' => 'Due to high network congestion on the Bitcoin and Ethereum networks, withdrawal processing times may be longer than usual. We recommend using USDT (TRC-20) for faster processing. Our team is working to process all pending withdrawals as quickly as possible.',
                'type' => 'warning',
                'target_audience' => 'all',
                'status' => 'published',
                'scheduled_at' => null,
                'views_count' => 987,
            ],
            [
                'title' => 'Holiday Trading Hours',
                'content' => 'Please note that trading hours will be adjusted during the upcoming public holidays. Forex markets will close early on Friday and reopen Monday as normal. Crypto trading remains 24/7. Check our trading hours page for complete schedule details.',
                'type' => 'info',
                'target_audience' => 'all',
                'status' => 'published',
                'scheduled_at' => null,
                'views_count' => 564,
            ],
            [
                'title' => 'Upcoming Platform Update',
                'content' => 'We are preparing a major platform update with exciting new features including advanced charting tools, additional technical indicators, and improved mobile experience. The update will go live on September 1st. Stay tuned for more details.',
                'type' => 'maintenance',
                'target_audience' => 'all',
                'status' => 'scheduled',
                'scheduled_at' => now()->addDays(14),
                'views_count' => 0,
            ],
            [
                'title' => 'Referral Bonus Program',
                'content' => 'Invite your friends and earn bonus rewards! For every friend who signs up and makes their first deposit, you will both receive a $50 trading bonus. There is no limit to the number of referrals. Share your unique referral link from your dashboard.',
                'type' => 'promotion',
                'target_audience' => 'all',
                'status' => 'draft',
                'scheduled_at' => null,
                'views_count' => 0,
            ],
        ];

        foreach ($announcements as $announcement) {
            DB::table('announcements')->insert(array_merge($announcement, [
                'admin_id' => $adminId,
                'created_at' => now()->subDays(rand(1, 14)),
                'updated_at' => now(),
            ]));
        }
    }
}
