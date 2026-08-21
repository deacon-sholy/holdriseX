<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Trade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Trade::with('user');

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('symbol', 'like', "%{$search}%");
        }

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

    public function show($id): JsonResponse
    {
        $trade = Trade::with('user')->findOrFail($id);

        return response()->json($trade);
    }

    public function close(Request $request, $id): JsonResponse
    {
        $trade = Trade::findOrFail($id);

        if ($trade->status !== 'open') {
            return response()->json(['message' => 'Trade is not open.'], 422);
        }

        $finalPrice = $request->input('close_price', $trade->current_price);

        if ($trade->type === 'buy') {
            $pnl = ($finalPrice - $trade->entry_price) * $trade->lot_size;
        } else {
            $pnl = ($trade->entry_price - $finalPrice) * $trade->lot_size;
        }

        $trade->update([
            'status' => 'closed',
            'current_price' => $finalPrice,
            'pnl' => round($pnl, 2),
            'closed_at' => now(),
        ]);

        $margin = $trade->entry_price * $trade->lot_size;
        $trade->user->increment('balance', $margin + $pnl);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'trade_closed',
            'details' => "Closed trade #{$trade->id} ({$trade->symbol} {$trade->type}) PnL: $" . number_format($pnl, 2) . " (user: {$trade->user->email})",
            'ip_address' => $request->ip(),
            'severity' => $pnl >= 0 ? 'info' : 'warning',
        ]);

        return response()->json([
            'message' => 'Trade closed.',
            'trade' => $trade->fresh()->load('user'),
            'final_pnl' => $pnl,
        ]);
    }

    public function stats(): JsonResponse
    {
        $total = Trade::count();
        $open = Trade::where('status', 'open')->count();
        $closed = Trade::where('status', 'closed')->count();
        $pending = Trade::where('status', 'pending')->count();
        $totalProfit = Trade::where('status', 'closed')->sum('pnl');

        $buyTrades = Trade::where('type', 'buy')->count();
        $sellTrades = Trade::where('type', 'sell')->count();

        return response()->json([
            'total' => $total,
            'open' => $open,
            'closed' => $closed,
            'pending' => $pending,
            'total_profit' => (float) $totalProfit,
            'buy_trades' => $buyTrades,
            'sell_trades' => $sellTrades,
        ]);
    }
}
