<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\KycDocument;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min((int) $request->query('limit', 20), 50);
        $notifications = collect();

        $recentDeposits = Deposit::with('user:id,name,email')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($d) => [
                'id' => "deposit-{$d->id}",
                'type' => 'deposit',
                'message' => "New deposit of \${$d->amount} from {$d->user->name}",
                'time' => $d->created_at->toISOString(),
                'icon' => 'wallet',
                'color' => 'green',
            ]);
        $notifications = $notifications->concat($recentDeposits);

        $recentWithdrawals = Withdrawal::with('user:id,name,email')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($w) => [
                'id' => "withdrawal-{$w->id}",
                'type' => 'withdrawal',
                'message' => "Withdrawal request: \${$w->amount} by {$w->user->name}",
                'time' => $w->created_at->toISOString(),
                'icon' => 'banknote',
                'color' => 'yellow',
            ]);
        $notifications = $notifications->concat($recentWithdrawals);

        $recentKyc = KycDocument::with('user:id,name,email')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($k) => [
                'id' => "kyc-{$k->id}",
                'type' => 'kyc',
                'message' => "KYC {$k->status}: {$k->user->name}",
                'time' => $k->created_at->toISOString(),
                'icon' => 'file-check',
                'color' => $k->status === 'verified' ? 'green' : ($k->status === 'rejected' ? 'red' : 'orange'),
            ]);
        $notifications = $notifications->concat($recentKyc);

        $recentUsers = User::where('role', 'user')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($u) => [
                'id' => "user-{$u->id}",
                'type' => 'registration',
                'message' => "New user registered: {$u->name}",
                'time' => $u->created_at->toISOString(),
                'icon' => 'user-plus',
                'color' => 'blue',
            ]);
        $notifications = $notifications->concat($recentUsers);

        $sorted = $notifications->sortByDesc('time')->take($limit)->values();
        $unread = $sorted->filter(fn($n) => now()->diffInMinutes(\Carbon\Carbon::parse($n['time'])) < 60)->count();

        return response()->json([
            'notifications' => $sorted,
            'unread_count' => $unread,
        ]);
    }
}
