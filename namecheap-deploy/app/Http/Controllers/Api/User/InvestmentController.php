<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPlan;
use App\Models\UserInvestment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
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

        return response()->json($investments);
    }

    public function showInvestment(Request $request, $id): JsonResponse
    {
        $investment = $request->user()
            ->investments()
            ->with('plan')
            ->findOrFail($id);

        return response()->json($investment);
    }

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'total_invested' => (float) $user->investments()->sum('amount'),
            'total_earned' => (float) $user->investments()->sum('total_earned'),
            'active_investments' => $user->investments()->where('status', 'active')->count(),
            'completed_investments' => $user->investments()->where('status', 'completed')->count(),
        ]);
    }
}
