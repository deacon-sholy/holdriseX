<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
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

    public function approve(Request $request, $id): JsonResponse
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if (!in_array($withdrawal->status, ['pending', 'processing'])) {
            return response()->json(['message' => 'Withdrawal cannot be approved.'], 422);
        }

        if ($withdrawal->status === 'pending') {
            $withdrawal->update(['status' => 'processing']);

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'withdrawal_processing',
                'details' => "Set withdrawal #{$withdrawal->id} (\${$withdrawal->amount}) to processing (user: {$withdrawal->user->email})",
                'ip_address' => $request->ip(),
                'severity' => 'info',
            ]);

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
        });

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'withdrawal_approved',
            'details' => "Approved withdrawal #{$withdrawal->id} for \${$withdrawal->amount} (user: {$withdrawal->user->email})",
            'ip_address' => $request->ip(),
            'severity' => 'info',
        ]);

        return response()->json([
            'message' => 'Withdrawal approved and completed.',
            'withdrawal' => $withdrawal->fresh()->load('user'),
        ]);
    }

    public function reject(Request $request, $id): JsonResponse
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if (!in_array($withdrawal->status, ['pending', 'processing'])) {
            return response()->json(['message' => 'Withdrawal cannot be rejected.'], 422);
        }

        DB::transaction(function () use ($withdrawal) {
            $withdrawal->update([
                'status' => 'rejected',
                'admin_note' => $request->input('reason', 'Rejected by admin'),
                'processed_at' => now(),
            ]);

            $withdrawal->user->increment('balance', $withdrawal->amount);
        });

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'withdrawal_rejected',
            'details' => "Rejected withdrawal #{$withdrawal->id} for \${$withdrawal->amount}, refunded (user: {$withdrawal->user->email})",
            'ip_address' => $request->ip(),
            'severity' => 'warning',
        ]);

        return response()->json([
            'message' => 'Withdrawal rejected. Amount has been refunded to user balance.',
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
