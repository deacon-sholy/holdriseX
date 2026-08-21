<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->trades();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($assetType = $request->input('asset_type')) {
            $query->where('asset_type', $assetType);
        }

        $trades = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json($trades);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $trade = $request->user()->trades()->findOrFail($id);

        return response()->json($trade);
    }

    public function open(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'symbol' => 'required|string|max:20',
            'type' => 'required|in:buy,sell',
            'asset_type' => 'required|in:forex,crypto,stocks,commodities',
            'entry_price' => 'required|numeric|min:0',
            'lot_size' => 'required|numeric|min:0.01',
        ]);

        $user = $request->user();

        $trade = $user->trades()->create([
            'symbol' => strtoupper($validated['symbol']),
            'type' => $validated['type'],
            'asset_type' => $validated['asset_type'],
            'entry_price' => $validated['entry_price'],
            'current_price' => $validated['entry_price'],
            'lot_size' => $validated['lot_size'],
            'pnl' => 0,
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Trade opened successfully.',
            'trade' => $trade,
        ], 201);
    }

    public function close(Request $request, $id): JsonResponse
    {
        $trade = $request->user()->trades()->findOrFail($id);

        if ($trade->status !== 'open') {
            return response()->json(['message' => 'Trade is not open.'], 422);
        }

        $closePrice = $request->input('close_price', $trade->current_price);

        $trade->update([
            'status' => 'closed',
            'current_price' => $closePrice,
            'closed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Trade closed.',
            'trade' => $trade->fresh(),
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $totalTrades = $user->trades()->count();
        $closedTrades = $user->trades()->where('status', 'closed')->count();
        $winningTrades = $user->trades()->where('status', 'closed')->where('pnl', '>', 0)->count();
        $winRate = $closedTrades > 0 ? round(($winningTrades / $closedTrades) * 100, 1) : 0;

        return response()->json([
            'total_trades' => $totalTrades,
            'open_trades' => $user->trades()->where('status', 'open')->count(),
            'closed_trades' => $closedTrades,
            'total_pnl' => (float) $user->trades()->where('status', 'closed')->sum('pnl'),
            'winning_trades' => $winningTrades,
            'losing_trades' => $closedTrades - $winningTrades,
            'win_rate' => $winRate,
        ]);
    }
}
