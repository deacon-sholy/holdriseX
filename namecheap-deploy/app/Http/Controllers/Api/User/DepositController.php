<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $deposits = $request->user()
            ->deposits()
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json($deposits);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $deposit = $request->user()
            ->deposits()
            ->findOrFail($id);

        return response()->json($deposit);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10',
            'method' => 'required|in:bitcoin,ethereum,usdt,bank_transfer,card',
            'wallet_address' => 'nullable|string',
            'transaction_hash' => 'nullable|string',
        ]);

        $deposit = $request->user()->deposits()->create([
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'wallet_address' => $validated['wallet_address'] ?? null,
            'transaction_hash' => $validated['transaction_hash'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Deposit request submitted. It will be reviewed by an administrator.',
            'deposit' => $deposit,
        ], 201);
    }

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'total' => (float) $user->deposits()->where('status', 'completed')->sum('amount'),
            'pending' => (float) $user->deposits()->where('status', 'pending')->sum('amount'),
            'failed' => (float) $user->deposits()->where('status', 'failed')->sum('amount'),
            'count_total' => $user->deposits()->count(),
            'count_pending' => $user->deposits()->where('status', 'pending')->count(),
            'count_completed' => $user->deposits()->where('status', 'completed')->count(),
        ]);
    }
}
