<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $withdrawals = $request->user()
            ->withdrawals()
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json($withdrawals);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $withdrawal = $request->user()
            ->withdrawals()
            ->findOrFail($id);

        return response()->json($withdrawal);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:20',
            'method' => 'required|in:bitcoin,ethereum,usdt,bank_transfer',
            'wallet_address' => 'required|string',
        ]);

        $user = $request->user();

        if ($validated['amount'] > $user->balance) {
            return response()->json(['message' => 'Insufficient balance.'], 422);
        }

        $pendingWithdrawals = $user->withdrawals()
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');

        $availableBalance = $user->balance - $pendingWithdrawals;

        if ($validated['amount'] > $availableBalance) {
            return response()->json(['message' => 'Insufficient available balance. You have pending withdrawals.'], 422);
        }

        $withdrawal = DB::transaction(function () use ($user, $validated) {
            $user->decrement('balance', $validated['amount']);

            return $user->withdrawals()->create([
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'wallet_address' => $validated['wallet_address'],
                'status' => 'pending',
            ]);
        });

        return response()->json([
            'message' => 'Withdrawal request submitted. It will be processed by an administrator.',
            'withdrawal' => $withdrawal,
        ], 201);
    }

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'total_withdrawn' => (float) $user->withdrawals()->where('status', 'completed')->sum('amount'),
            'pending' => (float) $user->withdrawals()->where('status', 'pending')->sum('amount'),
            'processing' => (float) $user->withdrawals()->where('status', 'processing')->sum('amount'),
            'rejected' => (float) $user->withdrawals()->where('status', 'rejected')->sum('amount'),
            'count_total' => $user->withdrawals()->count(),
            'count_pending' => $user->withdrawals()->where('status', 'pending')->count(),
            'count_completed' => $user->withdrawals()->where('status', 'completed')->count(),
        ]);
    }
}
