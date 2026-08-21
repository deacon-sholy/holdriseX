<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPlan;
use App\Models\UserInvestment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    protected function calculateLiveEarnings(UserInvestment $investment): float
    {
        if ($investment->status !== 'active' || !$investment->plan) {
            return (float) $investment->total_earned;
        }

        $today = Carbon::today();
        $startDate = Carbon::parse($investment->start_date);
        $daysElapsed = max(1, $startDate->diffInDays($today));
        $effectiveDays = min($daysElapsed, $investment->plan->duration_days);

        return round($investment->amount * ((float) $investment->plan->daily_return / 100) * $effectiveDays, 2);
    }
    public function depositSettings(): JsonResponse
    {
        return response()->json([
            'bitcoin' => \App\Models\Setting::where('key', 'deposit_wallet_bitcoin')->value('value'),
            'ethereum' => \App\Models\Setting::where('key', 'deposit_wallet_ethereum')->value('value'),
            'usdt' => \App\Models\Setting::where('key', 'deposit_wallet_usdt')->value('value'),
        ]);
    }

    public function plans(): JsonResponse
    {
        $plans = InvestmentPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json($plans);
    }

    public function plan($id): JsonResponse
    {
        $plan = InvestmentPlan::where('is_active', true)->findOrFail($id);

        return response()->json($plan);
    }

    public function invest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:investment_plans,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $plan = InvestmentPlan::findOrFail($validated['plan_id']);

        if (!$plan->is_active) {
            return response()->json(['message' => 'This plan is no longer available.'], 422);
        }

        if ($validated['amount'] < $plan->min_amount || $validated['amount'] > $plan->max_amount) {
            return response()->json([
                'message' => "Amount must be between {$plan->min_amount} and {$plan->max_amount}.",
            ], 422);
        }

        $user = $request->user();

        if ($user->balance < $validated['amount']) {
            return response()->json(['message' => 'Insufficient balance.'], 422);
        }

        $investment = DB::transaction(function () use ($user, $plan, $validated) {
            $user->decrement('balance', $validated['amount']);

            $startDate = now()->toDateString();
            $endDate = now()->addDays($plan->duration_days)->toDateString();

            return $user->investments()->create([
                'plan_id' => $plan->id,
                'amount' => $validated['amount'],
                'daily_return_earned' => 0,
                'total_earned' => 0,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active',
            ]);
        });

        return response()->json([
            'message' => 'Investment successful.',
            'investment' => $investment->load('plan'),
        ], 201);
    }

    public function myInvestments(Request $request): JsonResponse
    {
        $investments = $request->user()
            ->investments()
            ->with('plan')
            ->latest()
            ->paginate($request->input('per_page', 15));

        $investments->getCollection()->transform(function ($inv) {
            $inv->live_earned = $this->calculateLiveEarnings($inv);
            return $inv;
        });

        return response()->json($investments);
    }

    public function showInvestment(Request $request, $id): JsonResponse
    {
        $investment = $request->user()
            ->investments()
            ->with('plan')
            ->findOrFail($id);

        $investment->live_earned = $this->calculateLiveEarnings($investment);

        return response()->json($investment);
    }

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $completedEarnings = $user->investments()->where('status', 'completed')->sum('total_earned');
        $activeInvestments = $user->investments()->where('status', 'active')->with('plan')->get();
        $activeEarnings = $activeInvestments->sum(fn ($inv) => $this->calculateLiveEarnings($inv));

        return response()->json([
            'total_invested' => (float) $user->investments()->sum('amount'),
            'total_earned' => (float) ($completedEarnings + $activeEarnings),
            'active_investments' => $activeInvestments->count(),
            'completed_investments' => $user->investments()->where('status', 'completed')->count(),
        ]);
    }
}
