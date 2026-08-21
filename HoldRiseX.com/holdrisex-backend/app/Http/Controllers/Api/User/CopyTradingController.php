<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\CopyTrader;
use App\Models\CopyTrade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CopyTradingController extends Controller
{
    public function traders(Request $request): JsonResponse
    {
        $query = CopyTrader::where('is_active', true)->withCount('copyTrades');

        if ($specialty = $request->input('specialty')) {
            $query->where('specialty', $specialty);
        }

        if ($riskLevel = $request->input('risk_level')) {
            $query->where('risk_level', $riskLevel);
        }

        $sortBy = $request->input('sort_by', 'total_followers');
        $allowedSorts = ['win_rate', 'monthly_return', 'total_followers', 'aum'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, 'desc');
        }

        $traders = $query->paginate($request->input('per_page', 15));

        return response()->json($traders);
    }

    public function trader($id): JsonResponse
    {
        $trader = CopyTrader::where('is_active', true)
            ->withCount('copyTrades')
            ->findOrFail($id);

        return response()->json($trader);
    }

    public function subscribe(Request $request, $traderId): JsonResponse
    {
        $trader = CopyTrader::where('is_active', true)->findOrFail($traderId);

        $validated = $request->validate([
            'symbol' => 'required|string|max:20',
            'lots' => 'required|numeric|min:0.01',
        ]);

        $user = $request->user();

        $requiredMargin = $validated['lots'] * 100;
        if ((float) $user->balance < $requiredMargin) {
            return response()->json([
                'message' => 'Insufficient balance. Required: $' . number_format($requiredMargin, 2) . ', Available: $' . number_format($user->balance, 2),
            ], 422);
        }

        $existingTrade = $user->copyTrades()
            ->where('trader_id', $traderId)
            ->where('symbol', $validated['symbol'])
            ->where('status', 'open')
            ->first();

        if ($existingTrade) {
            return response()->json(['message' => 'You already have an active copy trade for this symbol with this trader.'], 422);
        }

        $user->decrement('balance', $requiredMargin);

        $copyTrade = $user->copyTrades()->create([
            'trader_id' => $trader->id,
            'symbol' => strtoupper($validated['symbol']),
            'lots' => $validated['lots'],
            'pnl' => 0,
            'status' => 'open',
        ]);

        $trader->increment('total_followers');

        return response()->json([
            'message' => 'Successfully subscribed to copy trade.',
            'copy_trade' => $copyTrade->load('copyTrader'),
        ], 201);
    }

    public function myCopyTrades(Request $request): JsonResponse
    {
        $copyTrades = $request->user()
            ->copyTrades()
            ->with('copyTrader')
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json($copyTrades);
    }

    public function closeCopyTrade(Request $request, $id): JsonResponse
    {
        $copyTrade = $request->user()->copyTrades()->findOrFail($id);

        if ($copyTrade->status !== 'open') {
            return response()->json(['message' => 'Copy trade is not open.'], 422);
        }

        $copyTrade->update([
            'status' => 'closed',
        ]);

        $margin = $copyTrade->lots * 100;
        $pnl = $copyTrade->pnl ?? 0;
        $copyTrade->user->increment('balance', $margin + $pnl);

        $copyTrade->copyTrader->decrement('total_followers');

        return response()->json([
            'message' => 'Copy trade closed.',
            'copy_trade' => $copyTrade->fresh()->load('copyTrader'),
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'total_copy_trades' => $user->copyTrades()->count(),
            'active_copy_trades' => $user->copyTrades()->where('status', 'open')->count(),
            'closed_copy_trades' => $user->copyTrades()->where('status', 'closed')->count(),
            'total_pnl' => (float) $user->copyTrades()->where('status', 'closed')->sum('pnl'),
        ]);
    }
}
