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

    private function fetchMarketPrice(string $symbol): float
    {
        $symbol = strtolower($symbol);
        $map = [
            'btcusd' => 'bitcoin', 'ethusd' => 'ethereum', 'xrpusd' => 'ripple',
            'btceur' => 'bitcoin', 'etheur' => 'ethereum', 'adusd' => 'cardano',
            'solusd' => 'solana', 'dogeusd' => 'dogecoin', 'dotusd' => 'polkadot',
        ];
        $coinId = $map[$symbol] ?? null;
        if (!$coinId) {
            return 1.0 + (float) ($this->hashSymbol($symbol) % 10000) / 100;
        }
        try {
            $url = "https://api.coingecko.com/api/v3/simple/price?ids={$coinId}&vs_currencies=usd";
            $ctx = stream_context_create(['http' => ['timeout' => 3]]);
            $response = @file_get_contents($url, false, $ctx);
            if ($response) {
                $data = json_decode($response, true);
                return $data[$coinId]['usd'] ?? 1.0;
            }
        } catch (\Throwable $e) {}
        return 1.0 + (float) ($this->hashSymbol($symbol) % 10000) / 100;
    }

    private function hashSymbol(string $s): int
    {
        $h = 0;
        for ($i = 0; $i < strlen($s); $i++) {
            $h = ($h * 31 + ord($s[$i])) & 0x7FFFFFFF;
        }
        return $h;
    }

    public function open(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'symbol' => 'required|string|max:20',
            'type' => 'required|in:buy,sell',
            'asset_type' => 'required|in:forex,crypto,stocks,commodities',
            'lot_size' => 'required|numeric|min:0.01',
        ]);

        $user = $request->user();
        $entryPrice = $this->fetchMarketPrice($validated['symbol']);

        $requiredMargin = $entryPrice * $validated['lot_size'];
        if ((float) $user->balance < $requiredMargin) {
            return response()->json([
                'message' => 'Insufficient balance. Required: $' . number_format($requiredMargin, 2) . ', Available: $' . number_format($user->balance, 2),
            ], 422);
        }

        $user->decrement('balance', $requiredMargin);

        $trade = $user->trades()->create([
            'symbol' => strtoupper($validated['symbol']),
            'type' => $validated['type'],
            'asset_type' => $validated['asset_type'],
            'entry_price' => $entryPrice,
            'current_price' => $entryPrice,
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

        $closePrice = $this->fetchMarketPrice($trade->symbol);

        if ($trade->type === 'buy') {
            $pnl = ($closePrice - $trade->entry_price) * $trade->lot_size;
        } else {
            $pnl = ($trade->entry_price - $closePrice) * $trade->lot_size;
        }

        $trade->update([
            'status' => 'closed',
            'current_price' => $closePrice,
            'pnl' => round($pnl, 2),
            'closed_at' => now(),
        ]);

        $user = $request->user();
        $margin = $trade->entry_price * $trade->lot_size;
        $user->increment('balance', $margin + $pnl);

        return response()->json([
            'message' => 'Trade closed.',
            'trade' => $trade->fresh(),
            'pnl' => round($pnl, 2),
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
