<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Deposit;
use App\Models\Trade;
use App\Models\UserInvestment;
use App\Models\Withdrawal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $totalDeposits = $user->deposits()->where('status', 'completed')->sum('amount');
        $totalWithdrawals = $user->withdrawals()->where('status', 'completed')->sum('amount');
        $activeTrades = $user->trades()->where('status', 'open')->count();
        $activeInvestments = $user->investments()->where('status', 'active')->count();
        $totalInvestmentEarnings = $user->investments()->sum('total_earned');
        $totalTradePnl = $user->trades()->where('status', 'closed')->sum('pnl');
        $totalInvested = $user->investments()->sum('amount');
        $totalBonus = 0;

        $activeInvestmentList = $user->investments()->where('status', 'active')
            ->with('plan')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'plan_name' => $inv->plan?->name ?? 'Investment',
                    'amount' => (float) $inv->amount,
                    'daily_roi' => $inv->plan?->daily_return ?? 0,
                    'end_date' => $inv->end_date?->toDateString(),
                    'status' => $inv->status,
                ];
            });

        $recentDeposits = $user->deposits()->latest()->take(5)->get();
        $recentWithdrawals = $user->withdrawals()->latest()->take(5)->get();
        $recentTrades = $user->trades()->latest()->take(5)->get();

        $announcements = Announcement::where('status', 'published')
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'content', 'type']);

        return response()->json([
            'balance' => (float) $user->balance,
            'total_deposits' => (float) $totalDeposits,
            'total_withdrawals' => (float) $totalWithdrawals,
            'total_profit' => (float) ($totalInvestmentEarnings + $totalTradePnl),
            'total_invested' => (float) $totalInvested,
            'total_bonus' => (float) $totalBonus,
            'active_trades' => $activeTrades,
            'active_plans' => $activeInvestments,
            'active_investments' => $activeInvestmentList,
            'total_investment_earnings' => (float) $totalInvestmentEarnings,
            'total_trade_pnl' => (float) $totalTradePnl,
            'recent_deposits' => $recentDeposits,
            'recent_withdrawals' => $recentWithdrawals,
            'recent_trades' => $recentTrades,
            'announcements' => $announcements,
            'kyc_status' => $user->kyc_status ?? 'unverified',
        ]);
    }
}
