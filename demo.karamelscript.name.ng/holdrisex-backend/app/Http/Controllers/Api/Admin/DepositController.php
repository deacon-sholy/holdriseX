<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Deposit::with('user');

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

        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->input('to_date'));
        }

        $deposits = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json($deposits);
    }

    public function show($id): JsonResponse
    {
        $deposit = Deposit::with('user')->findOrFail($id);

        return response()->json($deposit);
    }

    public function approve($id): JsonResponse
    {
        $deposit = Deposit::findOrFail($id);

        if ($deposit->status !== 'pending') {
            return response()->json(['message' => 'Deposit is not pending.'], 422);
        }

        DB::transaction(function () use ($deposit) {
            $deposit->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            $deposit->user->increment('balance', $deposit->amount);
        });

        return response()->json([
            'message' => 'Deposit approved and credited.',
            'deposit' => $deposit->fresh()->load('user'),
        ]);
    }

    public function reject($id): JsonResponse
    {
        $deposit = Deposit::findOrFail($id);

        if ($deposit->status !== 'pending') {
            return response()->json(['message' => 'Deposit is not pending.'], 422);
        }

        $deposit->update([
            'status' => 'failed',
            'admin_note' => request()->input('reason', 'Rejected by admin'),
            'processed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Deposit rejected.',
            'deposit' => $deposit->fresh()->load('user'),
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'total' => Deposit::sum('amount'),
            'pending' => Deposit::where('status', 'pending')->sum('amount'),
            'completed' => Deposit::where('status', 'completed')->sum('amount'),
            'failed' => Deposit::where('status', 'failed')->sum('amount'),
            'count_pending' => Deposit::where('status', 'pending')->count(),
            'count_completed' => Deposit::where('status', 'completed')->count(),
            'count_failed' => Deposit::where('status', 'failed')->count(),
        ]);
    }
}
