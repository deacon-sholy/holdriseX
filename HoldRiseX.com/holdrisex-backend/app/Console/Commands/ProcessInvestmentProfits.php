<?php

namespace App\Console\Commands;

use App\Models\UserInvestment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcessInvestmentProfits extends Command
{
    protected $signature = 'investment:process-profits';
    protected $description = 'Calculate daily profits for active investments and complete matured ones';

    public function handle(): int
    {
        $today = Carbon::today();
        $processed = 0;
        $completed = 0;

        $activeInvestments = UserInvestment::where('status', 'active')
            ->with('plan')
            ->get();

        foreach ($activeInvestments as $investment) {
            $plan = $investment->plan;
            if (!$plan) {
                continue;
            }

            $startDate = Carbon::parse($investment->start_date);
            $endDate = Carbon::parse($investment->end_date);
            $daysElapsed = $startDate->diffInDays($today);

            if ($daysElapsed < 1) {
                $daysElapsed = 1;
            }

            $totalDays = $plan->duration_days;
            $effectiveDays = min($daysElapsed, $totalDays);
            $dailyReturnPct = (float) $plan->daily_return;

            $totalEarned = round($investment->amount * ($dailyReturnPct / 100) * $effectiveDays, 2);
            $dailyEarned = round($investment->amount * ($dailyReturnPct / 100), 2);

            $investment->update([
                'daily_return_earned' => $dailyEarned,
                'total_earned' => $totalEarned,
            ]);

            $processed++;

            if ($today->gte($endDate)) {
                DB::transaction(function () use ($investment, $totalEarned, $totalDays) {
                    $user = User::findOrFail($investment->user_id);
                    $principalReturn = $investment->amount;
                    $user->increment('balance', $principalReturn + $totalEarned);

                    $investment->update([
                        'status' => 'completed',
                    ]);
                });

                $completed++;
            }
        }

        $this->info("Processed {$processed} active investments. Completed {$completed} matured investments.");

        return Command::SUCCESS;
    }
}
