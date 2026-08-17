<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CopyTrader;
use App\Models\CopyTrade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CopyTradingController extends Controller
{
    public function index(): JsonResponse
    {
        $traders = CopyTrader::withCount('copyTrades')->latest()->get();

        return response()->json($traders);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|string',
            'description' => 'nullable|string',
            'win_rate' => 'nullable|numeric|min:0|max:100',
            'total_trades' => 'nullable|integer|min:0',
            'total_profit' => 'nullable|numeric',
            'subscribers' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['win_rate'] = $validated['win_rate'] ?? 0;
        $validated['total_trades'] = $validated['total_trades'] ?? 0;
        $validated['total_profit'] = $validated['total_profit'] ?? 0;
        $validated['subscribers'] = $validated['subscribers'] ?? 0;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $trader = CopyTrader::create($validated);

        return response()->json([
            'message' => 'Trader created.',
            'trader' => $trader,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $trader = CopyTrader::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'avatar' => 'nullable|string',
            'description' => 'nullable|string',
            'win_rate' => 'sometimes|numeric|min:0|max:100',
            'total_trades' => 'sometimes|integer|min:0',
            'total_profit' => 'sometimes|numeric',
            'subscribers' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $trader->update($validated);

        return response()->json([
            'message' => 'Trader updated.',
            'trader' => $trader->fresh(),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $trader = CopyTrader::findOrFail($id);
        $trader->delete();

        return response()->json(['message' => 'Trader deleted.']);
    }

    public function toggleStatus($id): JsonResponse
    {
        $trader = CopyTrader::findOrFail($id);
        $trader->update(['is_active' => !$trader->is_active]);

        return response()->json([
            'message' => 'Trader status toggled.',
            'is_active' => $trader->fresh()->is_active,
        ]);
    }

    public function activity(): JsonResponse
    {
        $activity = CopyTrade::with(['copyTrader', 'user'])
            ->latest()
            ->paginate(request()->input('per_page', 15));

        return response()->json($activity);
    }
}
