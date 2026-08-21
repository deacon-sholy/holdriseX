<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
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

    public function approve(Request $request, $id): JsonResponse
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

            if ($deposit->user->referred_by) {
                $referrer = \App\Models\User::find($deposit->user->referred_by);
                if ($referrer) {
                    $commission = round($deposit->amount * 0.05, 2);
                    $referrer->increment('balance', $commission);
                }
            }
        });

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'deposit_approved',
            'details' => "Approved deposit #{$deposit->id} for \${$deposit->amount} (user: {$deposit->user->email})",
            'ip_address' => $request->ip(),
            'severity' => 'info',
        ]);

        return response()->json([
            'message' => 'Deposit approved and credited.',
            'deposit' => $deposit->fresh()->load('user'),
        ]);
    }

    public function reject(Request $request, $id): JsonResponse    {
        $deposit = Deposit::findOrFail($id);

        if ($deposit->status !== 'pending') {
            return response()->json(['message' => 'Deposit is not pending.'], 422);
        }

        $deposit->update([
            'status' => 'failed',
            'admin_note' => request()->input('reason', 'Rejected by admin'),
            'processed_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'deposit_rejected',
            'details' => "Rejected deposit #{$deposit->id} for \${$deposit->amount} (user: {$deposit->user->email})",
            'ip_address' => $request->ip(),
            'severity' => 'warning',
        ]);

        return response()->json([
            'message' => 'Deposit rejected.',
            'deposit' => $deposit->fresh()->load('user'),
        ]);
    }

    public function refund($id): JsonResponse
    {
        $deposit = Deposit::findOrFail($id);

        if ($deposit->status !== 'completed') {
            return response()->json(['message' => 'Only completed deposits can be refunded.'], 422);
        }

        if ($deposit->user->balance < $deposit->amount) {
            return response()->json(['message' => 'User has insufficient balance to refund ($' . number_format($deposit->user->balance, 2) . ' available).'], 422);
        }

        DB::transaction(function () use ($deposit) {
            $deposit->user->decrement('balance', $deposit->amount);

            $deposit->update([
                'status' => 'refunded',
                'admin_note' => request()->input('reason', 'Refunded by admin'),
                'processed_at' => now(),
            ]);
        });

        return response()->json([
            'message' => 'Deposit refunded and amount deducted from user balance.',
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
