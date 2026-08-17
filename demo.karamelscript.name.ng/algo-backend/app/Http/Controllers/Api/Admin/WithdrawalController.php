<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Withdrawal::with('user');

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($method = $request->input('method')) {
            $query->where('method', $method);
        }

        $withdrawals = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json($withdrawals);
    }

    public function show($id): JsonResponse
    {
        $withdrawal = Withdrawal::with('user')->findOrFail($id);

        return response()->json($withdrawal);
    }

    public function approve($id): JsonResponse
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if (!in_array($withdrawal->status, ['pending', 'processing'])) {
            return response()->json(['message' => 'Withdrawal cannot be approved.'], 422);
        }

        if ($withdrawal->status === 'pending') {
            $withdrawal->update(['status' => 'processing']);

            return response()->json([
                'message' => 'Withdrawal set to processing.',
                'withdrawal' => $withdrawal->fresh()->load('user'),
            ]);
        }

        DB::transaction(function () use ($withdrawal) {
            $withdrawal->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            $withdrawal->user->decrement('balance', $withdrawal->amount);
        });

        return response()->json([
            'message' => 'Withdrawal approved and completed.',
            'withdrawal' => $withdrawal->fresh()->load('user'),
        ]);
    }

    public function reject($id): JsonResponse
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if (!in_array($withdrawal->status, ['pending', 'processing'])) {
            return response()->json(['message' => 'Withdrawal cannot be rejected.'], 422);
        }

        $withdrawal->update([
            'status' => 'rejected',
            'admin_note' => request()->input('reason', 'Rejected by admin'),
            'processed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Withdrawal rejected.',
            'withdrawal' => $withdrawal->fresh()->load('user'),
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'total' => Withdrawal::sum('amount'),
            'pending' => Withdrawal::where('status', 'pending')->sum('amount'),
            'processing' => Withdrawal::where('status', 'processing')->sum('amount'),
            'completed' => Withdrawal::where('status', 'completed')->sum('amount'),
            'rejected' => Withdrawal::where('status', 'rejected')->sum('amount'),
            'count_pending' => Withdrawal::where('status', 'pending')->count(),
            'count_processing' => Withdrawal::where('status', 'processing')->count(),
            'count_completed' => Withdrawal::where('status', 'completed')->count(),
            'count_rejected' => Withdrawal::where('status', 'rejected')->count(),
        ]);
    }
}
