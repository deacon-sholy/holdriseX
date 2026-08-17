<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Trade;
use App\Models\User;
use App\Models\UserInvestment;
use App\Models\Withdrawal;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $totalUsers = User::count();
        $totalDeposits = Deposit::where('status', 'completed')->sum('amount');
        $totalWithdrawals = Withdrawal::where('status', 'completed')->sum('amount');
        $activeTrades = Trade::where('status', 'open')->count();
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        $pendingKyc = User::where('kyc_status', 'pending')->count();
        $platformBalance = $totalDeposits - $totalWithdrawals;

        $profitToday = Deposit::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        $recentTransactions = Deposit::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($d) => [
                'id' => $d->id,
                'type' => 'deposit',
                'amount' => $d->amount,
                'status' => $d->status,
                'user' => $d->user->name ?? 'N/A',
                'created_at' => $d->created_at,
            ])
            ->merge(
                Withdrawal::with('user')
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn ($w) => [
                        'id' => $w->id,
                        'type' => 'withdrawal',
                        'amount' => $w->amount,
                        'status' => $w->status,
                        'user' => $w->user->name ?? 'N/A',
                        'created_at' => $w->created_at,
                    ])
            )
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        $recentUsers = User::latest('created_at')->take(5)->get();

        $monthlyRevenue = Deposit::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        $totalTradesCount = max(Trade::count(), 1);
        $completedTrades = Trade::where('status', 'closed')->count();
        $winningTrades = Trade::where('status', 'closed')->where('pnl', '>', 0)->count();
        $winRate = $totalTradesCount > 0 ? round(($winningTrades / max($completedTrades, 1)) * 100, 1) : 0;

        $totalUserCount = max($totalUsers, 1);
        $kycVerified = User::where('kyc_status', 'verified')->count();
        $copyTradingActive = User::whereHas('copyTrades')->count();

        $tradingStatus = [
            'win_rate' => $winRate,
            'uptime' => 99.9,
            'active_traders' => User::where('is_active', true)->where('role', 'user')->count(),
            'kyc_verified_percent' => round(($kycVerified / $totalUserCount) * 100, 1),
            'copy_trading_percent' => round(($copyTradingActive / $totalUserCount) * 100, 1),
        ];

        return response()->json([
            'total_users' => $totalUsers,
            'total_deposits' => (float) $totalDeposits,
            'total_withdrawals' => (float) $totalWithdrawals,
            'active_trades' => $activeTrades,
            'pending_withdrawals' => $pendingWithdrawals,
            'pending_kyc' => $pendingKyc,
            'platform_balance' => (float) $platformBalance,
            'profit_today' => (float) $profitToday,
            'recent_transactions' => $recentTransactions,
            'recent_users' => $recentUsers,
            'monthly_revenue' => $monthlyRevenue,
            'trading_status' => $tradingStatus,
        ]);
    }
}
